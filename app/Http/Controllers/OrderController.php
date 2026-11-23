<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\DeliveryAgent;
use App\Models\Coupon;
use App\Models\OrderPayment;
use App\Models\StockMovement;
use App\Models\OrderStatusHistory;
use App\Models\Delivery;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'customer', 'deliveryAgent', 'items.product', 'payments'])->latest();

        // Search
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('shipping_address', 'like', "%{$search}%")
                  ->orWhere('delivery_man_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        // Order Source filter
        if ($request->filled('order_source') && in_array($request->order_source, ['online', 'admin'])) {
            $query->where('order_source', $request->order_source);
        }

        // Order Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Payment Status filter
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Delivery Status filter
        if ($request->filled('delivery_status')) {
            $query->where('delivery_status', $request->delivery_status);
        }

        // Date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->paginate(15)->withQueryString();
        $deliveryAgents = DeliveryAgent::where('status', 'available')->orWhere('status', 1)->get();

        return view('orders.index', compact('orders', 'deliveryAgents'));
    }

    public function create()
    {
        $customers = User::where('role', 'customer')
            ->where('status', 1)
            ->with('addresses')
            ->get();

        $products = Product::where('qty', '>', 0)->where('status', 1)->get();
        $deliveryAgents = DeliveryAgent::where('status', 'available')->orWhere('status', 1)->get();
        $coupons = Coupon::where('status', 1)->get();

        return view('orders.create', compact('customers', 'products', 'deliveryAgents', 'coupons'));
    }

    public function store(Request $request)
    {
        $authUser = Auth::user();
        $isAdmin = $authUser && $authUser->isAdmin();

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

        $createdOrder = DB::transaction(function () use ($request, $targetUserId, $isAdmin, $authUser) {
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
                    'product'      => $product,
                    'quantity'     => $item['quantity'],
                    'price'        => $product->price,
                    'subtotal'     => $rowSubtotal,
                    'product_name' => $product->name,
                    'product_sku'  => $product->sku,
                ];
            }

            $discount = 0.00;
            $couponCode = null;

            // 2. Apply coupon if valid
            if ($request->filled('coupon_id')) {
                $coupon = Coupon::find($request->coupon_id);
                if ($coupon && $subtotal >= $coupon->min_order_amount) {
                    $couponCode = $coupon->code;
                    $discount = $coupon->calculateDiscount($subtotal);
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
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

            $order = Order::create([
                'order_number'       => $orderNumber,
                'user_id'            => $targetUserId,
                'customer_id'        => null,
                'order_source'       => $isAdmin ? 'admin' : 'online',
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
                'delivery_status'    => $request->filled('delivery_agent_id') ? 'assigned' : 'pending',
                'shipping_address'   => $request->shipping_address,
                'delivery_slot'      => $request->delivery_slot,
                'delivery_man_name'  => $agentName,
                'delivery_man_phone' => $agentPhone,
                'delivery_notes'     => $request->delivery_notes,
            ]);

            // 5. Attach each item and decrement quantity with stock movement log
            foreach ($itemsPayload as $itemData) {
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
                    'user_id'        => $authUser->id,
                    'user_name'      => $authUser->name,
                    'reason'         => "Order #{$order->order_number} creation",
                ]);
            }

            // 6. Record Initial Payment Transaction if made
            if ($advancePaid > 0) {
                $order->payments()->create([
                    'user_id'             => $targetUserId,
                    'amount'              => $advancePaid,
                    'payment_method'      => $request->payment_method ?? 'cash',
                    'transaction_id'      => $request->transaction_id,
                    'payment_date'        => now()->toDateString(),
                    'payment_status'      => $paymentStatus,
                    'verification_status' => 'verified',
                    'note'                => 'Advance / Initial Payment',
                    'recorded_by'         => $authUser->name ?? 'System',
                ]);
            }

            // 7. Status history
            $order->statusHistories()->create([
                'status'     => 'pending',
                'comment'    => 'Order created by ' . ($isAdmin ? 'Admin' : 'Customer'),
                'changed_by' => $authUser->name ?? 'System',
            ]);

            // 8. If delivery agent assigned, create delivery entry
            if ($request->filled('delivery_agent_id')) {
                Delivery::create([
                    'order_id'          => $order->id,
                    'delivery_agent_id' => $request->delivery_agent_id,
                    'delivery_status'   => 'assigned',
                    'assigned_at'       => now(),
                    'notes'             => $request->delivery_notes,
                ]);
            }

            ActivityLog::log('Order Created', 'Order', $order->id, "Order #{$order->order_number} created with total ৳" . number_format($order->total_amount, 2));

            return $order;
        });

        return redirect()
            ->route('order.show', $createdOrder)
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
                'user_id'             => $order->user_id,
                'amount'              => $paymentAmount,
                'payment_method'      => $request->payment_method,
                'transaction_id'      => $request->transaction_id,
                'payment_date'        => $request->payment_date,
                'payment_status'      => 'paid',
                'verification_status' => 'verified',
                'note'                => $request->filled('note') 
                                            ? $request->note 
                                            : (($order->paid_amount ?? 0) > 0 ? 'Installment Payment' : 'Advance Payment'),
                'recorded_by'         => Auth::user()->name ?? 'System',
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

            ActivityLog::log('Payment Recorded', 'Order', $order->id, "Payment of ৳{$paymentAmount} recorded for Order #{$order->order_number}");
        });

        return redirect()
            ->route('order.show', $order)
            ->with('success', 'Payment recorded successfully.');
    }

    public function verifyPayment(Request $request, OrderPayment $payment)
    {
        $request->validate([
            'action' => 'required|in:verify,reject',
            'note'   => 'nullable|string|max:255',
        ]);

        $order = $payment->order;

        DB::transaction(function () use ($payment, $order, $request) {
            if ($request->action === 'verify') {
                $payment->update([
                    'verification_status' => 'verified',
                    'payment_status'      => 'paid',
                    'note'                => $payment->note . ' (Verified by ' . Auth::user()->name . ')',
                ]);

                $newPaid = $order->paid_amount + $payment->amount;
                $newDue = max(0, $order->total_amount - $newPaid);
                $paymentStatus = $newDue <= 0 ? 'paid' : 'partially_paid';

                $order->update([
                    'paid_amount'    => $newPaid,
                    'due_amount'     => $newDue,
                    'payment_status' => $paymentStatus,
                    'updated_by'     => Auth::user()->name,
                ]);

                ActivityLog::log('Payment Verified', 'OrderPayment', $payment->id, "Transaction {$payment->transaction_id} verified for Order #{$order->order_number}");
            } else {
                $payment->update([
                    'verification_status' => 'rejected',
                    'payment_status'      => 'failed',
                    'note'                => $payment->note . ' (Rejected: ' . ($request->note ?? 'Invalid reference') . ')',
                ]);

                ActivityLog::log('Payment Rejected', 'OrderPayment', $payment->id, "Transaction {$payment->transaction_id} rejected for Order #{$order->order_number}");
            }
        });

        return back()->with('success', 'Payment ' . ($request->action === 'verify' ? 'verified' : 'rejected') . ' successfully.');
    }

    public function assignRider(Request $request, Order $order)
    {
        $request->validate([
            'delivery_agent_id' => 'required|exists:delivery_agents,id',
            'delivery_notes'    => 'nullable|string',
        ]);

        $agent = DeliveryAgent::findOrFail($request->delivery_agent_id);

        DB::transaction(function () use ($order, $agent, $request) {
            $order->update([
                'delivery_agent_id'  => $agent->id,
                'delivery_man_name'  => $agent->name,
                'delivery_man_phone' => $agent->phone,
                'delivery_status'    => 'assigned',
                'delivery_notes'     => $request->delivery_notes ?? $order->delivery_notes,
                'updated_by'         => Auth::user()->name,
            ]);

            Delivery::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'delivery_agent_id' => $agent->id,
                    'delivery_status'   => 'assigned',
                    'assigned_at'       => now(),
                    'notes'             => $request->delivery_notes,
                ]
            );

            $order->statusHistories()->create([
                'status'     => $order->status,
                'comment'    => "Assigned to delivery rider: {$agent->name} ({$agent->phone})",
                'changed_by' => Auth::user()->name,
            ]);

            ActivityLog::log('Rider Assigned', 'Order', $order->id, "Rider {$agent->name} assigned to Order #{$order->order_number}");
        });

        return back()->with('success', "Delivery rider {$agent->name} assigned successfully.");
    }

    public function show(Order $order)
    {
        $order->load(['user', 'customer', 'deliveryAgent', 'items.product', 'payments', 'statusHistories', 'delivery']);
        $deliveryAgents = DeliveryAgent::where('status', 'available')->orWhere('status', 1)->get();

        return view('orders.show', compact('order', 'deliveryAgents'));
    }

    public function edit(Order $order)
    {
        $hoursSinceCreation = $order->created_at->diffInHours(now());
        $isEditable = ($hoursSinceCreation <= 48) && !in_array($order->status, ['delivered', 'cancelled']);

        $order->load(['user', 'deliveryAgent', 'items.product', 'payments']);
        $customers = User::where('role', 'customer')->where('status', 1)->get();
        $deliveryAgents = DeliveryAgent::all();
        $products = Product::where('status', true)->get();

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
            'admin_notes'       => 'nullable|string',
            'cancel_reason'     => 'nullable|string',
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
                // 1. Restock existing items back to inventory with log
                foreach ($order->items as $oldItem) {
                    if ($oldItem->product) {
                        $prevStock = $oldItem->product->qty;
                        $oldItem->product->increment('qty', $oldItem->quantity);
                        $newStock = $oldItem->product->qty;

                        StockMovement::create([
                            'product_id'     => $oldItem->product_id,
                            'type'           => 'adjustment',
                            'quantity'       => $oldItem->quantity,
                            'previous_stock' => $prevStock,
                            'new_stock'      => $newStock,
                            'reference_type' => 'Order',
                            'reference_id'   => $order->id,
                            'user_id'        => Auth::id(),
                            'user_name'      => Auth::user()->name,
                            'reason'         => "Order #{$order->order_number} product edit restock",
                        ]);
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
                        'product_id'   => $product->id,
                        'product_name' => $product->name,
                        'product_sku'  => $product->sku,
                        'quantity'     => $itemData['quantity'],
                        'price'        => $product->price,
                        'subtotal'     => $lineTotal,
                    ]);

                    $prevStock = $product->qty;
                    $product->decrement('qty', $itemData['quantity']);
                    $newStock = $product->qty;

                    StockMovement::create([
                        'product_id'     => $product->id,
                        'type'           => 'sale',
                        'quantity'       => -$itemData['quantity'],
                        'previous_stock' => $prevStock,
                        'new_stock'      => $newStock,
                        'reference_type' => 'Order',
                        'reference_id'   => $order->id,
                        'user_id'        => Auth::id(),
                        'user_name'      => Auth::user()->name,
                        'reason'         => "Order #{$order->order_number} product update",
                    ]);
                }

                // 4. Recalculate discount if coupon existed
                $discount = floatval($order->discount_amount);
                if ($order->coupon_code) {
                    $coupon = Coupon::where('code', $order->coupon_code)->first();
                    if ($coupon) {
                        $discount = $coupon->calculateDiscount($newSubtotal);
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
                        $prevStock = $item->product->qty;
                        $item->product->increment('qty', $item->quantity);
                        $newStock = $item->product->qty;

                        StockMovement::create([
                            'product_id'     => $item->product_id,
                            'type'           => 'cancellation',
                            'quantity'       => $item->quantity,
                            'previous_stock' => $prevStock,
                            'new_stock'      => $newStock,
                            'reference_type' => 'Order',
                            'reference_id'   => $order->id,
                            'user_id'        => Auth::id(),
                            'user_name'      => Auth::user()->name,
                            'reason'         => "Order #{$order->order_number} cancelled: " . ($request->cancel_reason ?? 'Admin cancellation'),
                        ]);
                    }
                }
            }

            // Record status change history if status changed
            if ($order->status !== $request->status) {
                $order->statusHistories()->create([
                    'status'     => $request->status,
                    'comment'    => "Status changed from {$order->status} to {$request->status} by " . Auth::user()->name,
                    'changed_by' => Auth::user()->name,
                ]);
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
                'admin_notes'        => $request->admin_notes ?? $order->admin_notes,
                'cancel_reason'      => $request->cancel_reason ?? $order->cancel_reason,
                'updated_by'         => Auth::user()->name ?? 'System',
            ]);

            ActivityLog::log('Order Updated', 'Order', $order->id, "Order #{$order->order_number} updated. Status: {$request->status}");
        });

        return redirect()
            ->route('order.show', $order)
            ->with('success', 'Order updated successfully.');
    }

    public function downloadInvoice(Order $order)
    {
        $order->load(['user', 'customer', 'deliveryAgent', 'items.product', 'payments']);

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

        ActivityLog::log('Order Deleted', 'Order', $order->id, "Order #{$order->order_number} deleted.");

        $order->delete();

        return redirect()
            ->route('order.index')
            ->with('success', 'Order deleted successfully.');
    }
}
