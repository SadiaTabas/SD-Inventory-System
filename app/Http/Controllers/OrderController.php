<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\DeliveryAgent;
use App\Models\Coupon;
use App\Models\OrderPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'deliveryAgent', 'items.product', 'payments'])
            ->latest()
            ->get();

        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $customers = User::where('role', 'customer')
            ->where('status', 1)
            ->with('addresses')
            ->get();

        $products = Product::where('qty', '>', 0)->get();
        $deliveryAgents = DeliveryAgent::where('status', 'available')->orWhere('status', 1)->get();
        $coupons = Coupon::where('status', 1)->get();

        return view('orders.create', compact('customers', 'products', 'deliveryAgents', 'coupons'));
    }

    public function store(Request $request)
    {
        $authUser = Auth::user();
        $isAdmin = $authUser && $authUser->role === 'admin';

        $rules = [
            'items'               => 'required|array|min:1',
            'items.*.product_id'  => 'required|exists:products,id',
            'items.*.quantity'    => 'required|integer|min:1',
            'shipping_address'    => 'required|string',
            'delivery_charge'     => 'required|numeric|min:0',
            'payment_method'      => 'nullable|string',
            'delivery_agent_id'   => 'nullable|exists:delivery_agents,id',
            'delivery_slot'       => 'nullable|string',
            'delivery_notes'      => 'nullable|string',
            'coupon_id'           => 'nullable|exists:coupons,id',
            'advance_payment'     => 'nullable|numeric|min:0',
            'transaction_id'      => 'nullable|string|max:100',
        ];

        if ($isAdmin) {
            $rules['user_id'] = 'required|exists:users,id';
        }

        $request->validate($rules);

        $targetUserId = $isAdmin ? $request->user_id : $authUser->id;

        DB::transaction(function () use ($request, $targetUserId, $isAdmin, $authUser) {
            $subtotal = 0.00;
            $itemsPayload = [];

            // 1. Lock and check stock for each submitted product
            foreach ($request->items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                if ($item['quantity'] > $product->qty) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'items' => 'Not enough stock for ' . $product->name . '. Only ' . $product->qty . ' available.',
                    ]);
                }

                $rowSubtotal = $product->price * $item['quantity'];
                $subtotal += $rowSubtotal;

                $itemsPayload[] = [
                    'product'  => $product,
                    'quantity' => $item['quantity'],
                    'price'    => $product->price,
                    'subtotal' => $rowSubtotal,
                ];
            }

            $discount = 0.00;
            $couponCode = null;

            // 2. Apply coupon if valid
            if ($request->filled('coupon_id')) {
                $coupon = Coupon::find($request->coupon_id);
                if ($coupon && $subtotal >= $coupon->min_order_amount) {
                    $couponCode = $coupon->code;
                    $discount = $coupon->type === 'percent'
                        ? ($subtotal * ($coupon->value / 100))
                        : $coupon->value;
                }
            }

            $discount = min($discount, $subtotal);
            $deliveryCharge = floatval($request->delivery_charge ?? 60.00);
            $grandTotal = max(0, ($subtotal - $discount) + $deliveryCharge);

            // 3. Calculate advance payment and due balance
            $advancePaid = floatval($request->advance_payment ?? 0.00);
            $advancePaid = min($advancePaid, $grandTotal);
            $dueAmount = max(0, $grandTotal - $advancePaid);

            $paymentStatus = 'pending';
            if ($grandTotal > 0 && $advancePaid >= $grandTotal) {
                $paymentStatus = 'paid';
            } elseif ($advancePaid > 0) {
                $paymentStatus = 'partially_paid';
            }

            $agentName = null;
            $agentPhone = null;
            if ($request->filled('delivery_agent_id')) {
                $agent = DeliveryAgent::find($request->delivery_agent_id);
                if ($agent) {
                    $agentName = $agent->name;
                    $agentPhone = $agent->phone;
                }
            }

            // 4. Create Master Order
            $order = Order::create([
                'order_number'       => 'ORD-' . strtoupper(uniqid()),
                'user_id'            => $targetUserId,
                'customer_id'        => null,
                'delivery_agent_id'  => $request->delivery_agent_id ?: null,
                'order_placed_by'    => $isAdmin ? 'admin' : 'customer',
                'created_by'         => $authUser->name ?? 'System',
                'subtotal_amount'    => $subtotal,
                'discount_amount'    => $discount,
                'delivery_charge'    => $deliveryCharge,
                'coupon_code'        => $couponCode,
                'total_amount'       => $grandTotal,
                'paid_amount'        => $advancePaid,
                'due_amount'         => $dueAmount,
                'status'             => 'pending',
                'payment_status'     => $paymentStatus,
                'payment_method'     => $request->payment_method ?? 'cod',
                'delivery_status'    => 'pending',
                'shipping_address'   => $request->shipping_address,
                'delivery_slot'      => $request->delivery_slot,
                'delivery_man_name'  => $agentName,
                'delivery_man_phone' => $agentPhone,
                'delivery_notes'     => $request->delivery_notes,
            ]);

            // 5. Attach each item and decrement quantity
            foreach ($itemsPayload as $itemData) {
                $order->items()->create([
                    'product_id' => $itemData['product']->id,
                    'quantity'   => $itemData['quantity'],
                    'price'      => $itemData['price'],
                    'subtotal'   => $itemData['subtotal'],
                ]);

                $itemData['product']->decrement('qty', $itemData['quantity']);
            }

            // 6. Record Initial Payment Transaction if made
            if ($advancePaid > 0) {
                $order->payments()->create([
                    'amount'         => $advancePaid,
                    'payment_method' => $request->payment_method ?? 'cash',
                    'transaction_id' => $request->transaction_id,
                    'payment_date'   => now()->toDateString(),
                    'note'           => 'Advance / Initial Payment',
                    'recorded_by'    => $authUser->name ?? 'System',
                ]);
            }
        });

        return redirect()
            ->route('order.index')
            ->with('success', 'Order created successfully.');
    }

    public function addPayment(Request $request, Order $order)
    {
        $request->validate([
            'amount'         => 'required|numeric|min:0.01|max:' . max(0.01, $order->due_amount),
            'payment_method' => 'required|string',
            'payment_date'   => 'required|date',
            'transaction_id' => 'nullable|string|max:100',
            'note'           => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request, $order) {
            $paymentAmount = floatval($request->amount);

            $order->payments()->create([
                'amount'         => $paymentAmount,
                'payment_method' => $request->payment_method,
                'transaction_id' => $request->transaction_id,
                'payment_date'   => $request->payment_date,
                'note'           => $request->filled('note') 
                                        ? $request->note 
                                        : (($order->paid_amount ?? 0) > 0 ? 'Installment Payment' : 'Advance Payment'),
                'recorded_by'    => Auth::user()->name ?? 'System',
            ]);

            $newPaid = $order->paid_amount + $paymentAmount;
            $newDue = max(0, $order->total_amount - $newPaid);

            $status = $newDue <= 0 ? 'paid' : 'partially_paid';

            $order->update([
                'paid_amount'    => $newPaid,
                'due_amount'     => $newDue,
                'payment_status' => $status,
                'updated_by'     => Auth::user()->name ?? 'System',
            ]);
        });

        return redirect()
            ->route('order.show', $order)
            ->with('success', 'Payment recorded successfully.');
    }

    public function show(Order $order)
    {
        $order->load(['user', 'deliveryAgent', 'items.product', 'payments']);

        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        // Check 2-day window (48 hours)
        $hoursSinceCreation = $order->created_at->diffInHours(now());
        $isEditable = ($hoursSinceCreation <= 48) && !in_array($order->status, ['delivered', 'cancelled']);

        $order->load(['user', 'deliveryAgent', 'items.product', 'payments']);
        $customers = User::where('role', 'customer')->where('status', 1)->get();
        $deliveryAgents = DeliveryAgent::all();
        $products = Product::all();

        return view('orders.edit', compact('order', 'customers', 'deliveryAgents', 'products', 'isEditable'));
    }

    public function update(Request $request, Order $order)
    {
        $hoursSinceCreation = $order->created_at->diffInHours(now());
        $canEditProducts = ($hoursSinceCreation <= 48) && !in_array($order->status, ['delivered', 'cancelled']);

        $rules = [
            'status'            => 'required|string',
            'payment_status'    => 'required|string',
            'payment_method'    => 'nullable|string',
            'delivery_status'   => 'required|string',
            'shipping_address'  => 'required|string',
            'delivery_agent_id' => 'nullable|exists:delivery_agents,id',
            'delivery_slot'     => 'nullable|string',
            'delivery_notes'    => 'nullable|string',
        ];

        if ($request->has('items') && is_array($request->items)) {
            if (!$canEditProducts) {
                return back()->withErrors([
                    'items' => 'The 2-day (48-hour) window to modify products for this order has expired.',
                ]);
            }

            $rules['items']              = 'required|array|min:1';
            $rules['items.*.product_id'] = 'required|exists:products,id';
            $rules['items.*.quantity']   = 'required|integer|min:1';
        }

        $request->validate($rules);

        DB::transaction(function () use ($request, $order, $canEditProducts) {
            $agentName = $order->delivery_man_name;
            $agentPhone = $order->delivery_man_phone;

            if ($request->filled('delivery_agent_id') && $request->delivery_agent_id != $order->delivery_agent_id) {
                $agent = DeliveryAgent::find($request->delivery_agent_id);
                if ($agent) {
                    $agentName = $agent->name;
                    $agentPhone = $agent->phone;
                }
            } elseif (!$request->filled('delivery_agent_id')) {
                $agentName = null;
                $agentPhone = null;
            }

            // Product & Financial modifications if items submitted within 2 days
            if ($request->has('items') && $canEditProducts) {
                // 1. Restock existing items back to inventory
                foreach ($order->items as $oldItem) {
                    if ($oldItem->product) {
                        $oldItem->product->increment('qty', $oldItem->quantity);
                    }
                }

                // 2. Clear previous items
                $order->items()->delete();

                // 3. Attach new items and deduct stock
                $newSubtotal = 0.00;
                foreach ($request->items as $itemData) {
                    $product = Product::lockForUpdate()->findOrFail($itemData['product_id']);

                    if ($itemData['quantity'] > $product->qty) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'items' => 'Insufficient stock for ' . $product->name . '. Available: ' . $product->qty,
                        ]);
                    }

                    $lineTotal = $product->price * $itemData['quantity'];
                    $newSubtotal += $lineTotal;

                    $order->items()->create([
                        'product_id' => $product->id,
                        'quantity'   => $itemData['quantity'],
                        'price'      => $product->price,
                        'subtotal'   => $lineTotal,
                    ]);

                    $product->decrement('qty', $itemData['quantity']);
                }

                // 4. Recalculate discount if coupon existed
                $discount = floatval($order->discount_amount);
                if ($order->coupon_code) {
                    $coupon = Coupon::where('code', $order->coupon_code)->first();
                    if ($coupon) {
                        $discount = $coupon->type === 'percent'
                            ? ($newSubtotal * ($coupon->value / 100))
                            : $coupon->value;
                    }
                }
                $discount = min($discount, $newSubtotal);

                // 5. Recompute totals
                $deliveryCharge = floatval($order->delivery_charge);
                $newTotal = max(0, ($newSubtotal - $discount) + $deliveryCharge);
                $paid = floatval($order->paid_amount);
                $due = max(0, $newTotal - $paid);

                $paymentStatus = $request->payment_status;
                if ($newTotal > 0 && $paid >= $newTotal) {
                    $paymentStatus = 'paid';
                } elseif ($paid > 0 && $paid < $newTotal) {
                    $paymentStatus = 'partially_paid';
                }

                $order->subtotal_amount = $newSubtotal;
                $order->discount_amount = $discount;
                $order->total_amount    = $newTotal;
                $order->due_amount      = $due;
                $order->payment_status  = $paymentStatus;
            }

            // Stock restoration on cancellation
            if ($request->status === 'cancelled' && $order->status !== 'cancelled') {
                foreach ($order->items as $item) {
                    if ($item->product) {
                        $item->product->increment('qty', $item->quantity);
                    }
                }
            }

            $order->update([
                'delivery_agent_id'  => $request->delivery_agent_id ?: null,
                'status'             => $request->status,
                'payment_status'     => $order->payment_status ?? $request->payment_status,
                'payment_method'     => $request->payment_method ?? $order->payment_method,
                'delivery_status'    => $request->delivery_status,
                'shipping_address'   => $request->shipping_address,
                'delivery_slot'      => $request->delivery_slot,
                'delivery_man_name'  => $agentName,
                'delivery_man_phone' => $agentPhone,
                'delivery_notes'     => $request->delivery_notes,
                'updated_by'         => Auth::user()->name ?? 'System',
            ]);
        });

        return redirect()
            ->route('order.show', $order)
            ->with('success', 'Order updated successfully. Updated invoice is ready for download.');
    }

    public function downloadInvoice(Order $order)
    {
        $order->load(['user', 'deliveryAgent', 'items.product', 'payments']);

        $pdf = Pdf::loadView('orders.invoice', compact('order'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Invoice-' . ($order->order_number ?? $order->id) . '.pdf');
    }

    public function destroy(Order $order)
    {
        if ($order->status !== 'cancelled') {
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->increment('qty', $item->quantity);
                }
            }
        }

        $order->delete();

        return redirect()
            ->route('order.index')
            ->with('success', 'Order deleted successfully.');
    }
}