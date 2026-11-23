<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Order;
use App\Models\Address;
use App\Models\OrderPayment;
use App\Models\OrderStatusHistory;
use App\Models\StockMovement;
use App\Models\ActivityLog;
use App\Models\BusinessSetting;
use App\Services\CartService;

class CheckoutController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        $cartTotals = $this->cartService->getCartTotals();

        if ($cartTotals['cart']->items->isEmpty()) {
            return redirect()->route('shop')->with('error', 'Your shopping cart is empty. Please add products before checking out.');
        }

        // Revalidate stock
        foreach ($cartTotals['cart']->items as $item) {
            if (!$item->product || !$item->product->status || $item->quantity > $item->product->qty) {
                return redirect()->route('cart.index')->with(
                    'error',
                    "Stock changed for '{$item->product?->name}'. Only {$item->product?->qty} available. Please review your cart."
                );
            }
        }

        $user = Auth::user();
        $addresses = $user->addresses()->latest()->get();
        $defaultAddress = $user->defaultAddress ?? $addresses->first();

        // Payment settings
        $enableCod = BusinessSetting::get('enable_cod', '1') == '1';
        $enableBkash = BusinessSetting::get('enable_bkash', '1') == '1';
        $enableNagad = BusinessSetting::get('enable_nagad', '1') == '1';
        $bkashNumber = BusinessSetting::get('bkash_number', '01700000000');
        $nagadNumber = BusinessSetting::get('nagad_number', '01800000000');

        return view('storefront.checkout', array_merge($cartTotals, compact(
            'user',
            'addresses',
            'defaultAddress',
            'enableCod',
            'enableBkash',
            'enableNagad',
            'bkashNumber',
            'nagadNumber'
        )));
    }

    public function placeOrder(Request $request)
    {
        $user = Auth::user();
        $cartTotals = $this->cartService->getCartTotals();

        if ($cartTotals['cart']->items->isEmpty()) {
            return redirect()->route('shop')->with('error', 'Your cart is empty.');
        }

        $rules = [
            'payment_method'  => 'required|in:cod,bkash,nagad',
            'delivery_notes'  => 'nullable|string|max:500',
            'delivery_slot'   => 'nullable|string|max:100',
            'transaction_id'  => 'nullable|string|max:100',
        ];

        if ($request->filled('address_id')) {
            $rules['address_id'] = 'required|exists:addresses,id';
        } else {
            $rules['new_full_name']    = 'required|string|max:255';
            $rules['new_phone']        = ['required', 'string', 'regex:/^(?:\+?8801|01)[3-9]\d{8}$/'];
            $rules['new_address_line'] = 'required|string|max:500';
            $rules['new_city']         = 'required|string|max:100';
            $rules['new_area']         = 'nullable|string|max:100';
            $rules['new_postal_code']  = 'nullable|string|max:20';
        }

        if (in_array($request->payment_method, ['bkash', 'nagad'])) {
            $rules['transaction_id'] = 'required|string|max:100';
        }

        $request->validate($rules, [
            'transaction_id.required' => 'Please provide the transaction ID/reference for your manual mobile payment.',
        ]);

        $order = DB::transaction(function () use ($request, $user, $cartTotals) {
            // 1. Resolve Delivery Address
            if ($request->filled('address_id')) {
                $address = Address::where('id', $request->address_id)
                    ->where('user_id', $user->id)
                    ->firstOrFail();

                $shippingAddressSnapshot = "{$address->full_name} | Phone: {$address->phone}\n{$address->address_line}, " .
                    ($address->area ? "{$address->area}, " : '') . "{$address->city}" .
                    ($address->postal_code ? " - {$address->postal_code}" : '');
            } else {
                $shippingAddressSnapshot = "{$request->new_full_name} | Phone: {$request->new_phone}\n{$request->new_address_line}, " .
                    ($request->new_area ? "{$request->new_area}, " : '') . "{$request->new_city}" .
                    ($request->new_postal_code ? " - {$request->new_postal_code}" : '');

                // Save address for future use
                $user->addresses()->create([
                    'full_name'    => $request->new_full_name,
                    'phone'        => $request->new_phone,
                    'address_line' => $request->new_address_line,
                    'area'         => $request->new_area,
                    'city'         => $request->new_city,
                    'district'     => $request->new_city,
                    'postal_code'  => $request->new_postal_code,
                    'is_default'   => $user->addresses()->count() === 0,
                ]);
            }

            // 2. Lock products and check stock
            $itemsToAttach = [];
            $subtotal = 0.00;

            foreach ($cartTotals['cart']->items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item->product_id);

                if (!$product->status || $item->quantity > $product->qty) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'items' => "Insufficient stock for {$product->name}. Only {$product->qty} available.",
                    ]);
                }

                $lineTotal = $product->price * $item->quantity;
                $subtotal += $lineTotal;

                $itemsToAttach[] = [
                    'product'      => $product,
                    'quantity'     => $item->quantity,
                    'price'        => $product->price,
                    'subtotal'     => $lineTotal,
                    'product_name' => $product->name,
                    'product_sku'  => $product->sku,
                ];
            }

            // 3. Recalculate server-side totals
            $discount = 0.00;
            $couponCode = null;

            if ($cartTotals['cart']->coupon_code) {
                $coupon = \App\Models\Coupon::where('code', $cartTotals['cart']->coupon_code)->where('status', true)->first();
                if ($coupon && $subtotal >= $coupon->min_order_amount) {
                    $discount = $coupon->calculateDiscount($subtotal);
                    $couponCode = $coupon->code;
                }
            }

            $defaultDeliveryCharge = (float) BusinessSetting::get('default_delivery_charge', 60.00);
            $freeDeliveryThreshold = (float) BusinessSetting::get('free_delivery_threshold', 0.00);

            $deliveryCharge = $defaultDeliveryCharge;
            if ($freeDeliveryThreshold > 0 && $subtotal >= $freeDeliveryThreshold) {
                $deliveryCharge = 0.00;
            }

            $grandTotal = max(0, ($subtotal - $discount) + $deliveryCharge);

            // 4. Create Order
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

            $order = Order::create([
                'order_number'       => $orderNumber,
                'user_id'            => $user->id,
                'customer_id'        => null,
                'order_source'       => 'online',
                'order_placed_by'    => 'customer',
                'created_by'         => $user->name,
                'subtotal_amount'    => $subtotal,
                'discount_amount'    => $discount,
                'coupon_code'        => $couponCode,
                'delivery_charge'    => $deliveryCharge,
                'total_amount'       => $grandTotal,
                'paid_amount'        => 0.00,
                'due_amount'         => $grandTotal,
                'status'             => 'pending',
                'payment_status'     => 'pending',
                'payment_method'     => $request->payment_method,
                'delivery_status'    => 'pending',
                'shipping_address'   => $shippingAddressSnapshot,
                'delivery_slot'      => $request->delivery_slot,
                'delivery_notes'     => $request->delivery_notes,
            ]);

            // 5. Attach Items and decrement stock with stock movement log
            foreach ($itemsToAttach as $itemData) {
                $order->items()->create([
                    'product_id'   => $itemData['product']->id,
                    'product_name' => $itemData['product_name'],
                    'product_sku'  => $itemData['product_sku'],
                    'quantity'     => $itemData['quantity'],
                    'price'        => $itemData['price'],
                    'subtotal'     => $itemData['subtotal'],
                ]);

                $prevStock = $itemData['product']->qty;
                $itemData['product']->decrement('qty', $itemData['quantity']);
                $newStock = $itemData['product']->qty;

                StockMovement::create([
                    'product_id'     => $itemData['product']->id,
                    'type'           => 'sale',
                    'quantity'       => -$itemData['quantity'],
                    'previous_stock' => $prevStock,
                    'new_stock'      => $newStock,
                    'reference_type' => 'Order',
                    'reference_id'   => $order->id,
                    'user_id'        => $user->id,
                    'user_name'      => $user->name,
                    'reason'         => "Online customer order #{$order->order_number}",
                ]);
            }

            // 6. Record Payment record if bKash/Nagad
            if (in_array($request->payment_method, ['bkash', 'nagad']) && $request->filled('transaction_id')) {
                $order->payments()->create([
                    'user_id'             => $user->id,
                    'amount'              => $grandTotal,
                    'payment_method'      => $request->payment_method,
                    'transaction_id'      => trim($request->transaction_id),
                    'payment_date'        => now()->toDateString(),
                    'payment_status'      => 'pending',
                    'verification_status' => 'pending',
                    'note'                => 'Customer submitted ' . strtoupper($request->payment_method) . ' transaction ID for verification.',
                    'recorded_by'         => $user->name,
                ]);
            }

            // 7. Initial status history
            $order->statusHistories()->create([
                'status'     => 'pending',
                'comment'    => 'Order placed by customer via online storefront.',
                'changed_by' => $user->name,
            ]);

            // 8. Clear Cart
            $this->cartService->clearCart();

            ActivityLog::log('Order Placed', 'Order', $order->id, "Customer placed order #{$order->order_number} totaling ৳" . number_format($order->total_amount, 2));

            return $order;
        });

        return redirect()->route('checkout.success', $order->order_number)
            ->with('success', 'Your order has been placed successfully!');
    }

    public function success(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->with(['items.product', 'payments'])
            ->firstOrFail();

        return view('storefront.checkout-success', compact('order'));
    }
}
