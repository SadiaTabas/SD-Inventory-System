@extends('layouts.customer')

@section('title', 'Order ' . $order->order_number . ' - SD Store')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
        <div>
            <a href="{{ route('customer.orders') }}" style="color: var(--primary); font-size: 14px; font-weight: 600;">&larr; Back to Orders</a>
            <h1 style="font-size: 26px; font-weight: 800; margin-top: 4px;">Order #{{ $order->order_number }}</h1>
            <p style="color: var(--gray-500); font-size: 13px;">Placed on {{ $order->created_at->format('M d, Y h:i A') }}</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('customer.orders.invoice', $order) }}" class="btn btn-outline btn-sm">Download Invoice PDF</a>
            @if($order->canBeCancelled())
                <button type="button" class="btn btn-danger btn-sm" onclick="document.getElementById('cancel-order-modal').style.display='block'">Cancel Order</button>
            @endif
        </div>
    </div>

    <!-- Visual Tracking Timeline -->
    <div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 32px 24px; margin-bottom: 28px;">
        <h3 style="font-size: 17px; font-weight: 800; margin-bottom: 28px;">Delivery Timeline</h3>

        @if($order->status === 'cancelled')
            <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: var(--radius); padding: 18px; color: #991b1b; text-align: center;">
                <strong style="font-size: 16px;">This order has been CANCELLED</strong>
                <p style="font-size: 13px; margin-top: 4px;">Reason: {{ $order->cancel_reason ?? 'Customer request' }}</p>
            </div>
        @else
            <div style="display: flex; justify-content: space-between; position: relative;">
                @php
                    $steps = [
                        'pending'          => 'Order Placed',
                        'confirmed'        => 'Confirmed',
                        'processing'       => 'Processing',
                        'shipped'          => 'Shipped',
                        'out_for_delivery' => 'Out for Delivery',
                        'delivered'        => 'Delivered',
                    ];
                    $statuses = array_keys($steps);
                    $currentIndex = array_search($order->status, $statuses);
                    if ($currentIndex === false) $currentIndex = 0;
                @endphp

                @foreach($steps as $key => $label)
                    @php
                        $stepIdx = array_search($key, $statuses);
                        $isDone = $stepIdx <= $currentIndex;
                    @endphp
                    <div style="display: flex; flex-direction: column; align-items: center; text-align: center; flex: 1; z-index: 2;">
                        <div style="width: 36px; height: 36px; border-radius: 50%; background: {{ $isDone ? 'var(--primary)' : 'var(--gray-200)' }}; color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; margin-bottom: 8px;">
                            {{ $isDone ? '✓' : $stepIdx + 1 }}
                        </div>
                        <span style="font-size: 12px; font-weight: 700; color: {{ $isDone ? 'var(--gray-900)' : 'var(--gray-400)' }};">{{ $label }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Items Snapshot -->
    <div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 28px; margin-bottom: 28px;">
        <h3 style="font-size: 17px; font-weight: 800; margin-bottom: 18px; border-bottom: 1px solid var(--gray-100); padding-bottom: 10px;">Purchased Items</h3>

        <div style="display: flex; flex-direction: column; gap: 14px; margin-bottom: 20px;">
            @foreach($order->items as $item)
                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 14px;">
                    <div>
                        <strong>{{ $item->product_name ?? $item->product->name ?? 'Product' }}</strong>
                        @if($item->product_sku)
                            <div style="font-size: 12px; color: var(--gray-400);">SKU: {{ $item->product_sku }}</div>
                        @endif
                        <div style="font-size: 13px; color: var(--gray-500);">Qty: {{ $item->quantity }} &times; ৳{{ number_format($item->price, 2) }}</div>
                    </div>
                    <span style="font-weight: 700;">৳{{ number_format($item->subtotal, 2) }}</span>
                </div>
            @endforeach
        </div>

        <div style="border-top: 1px solid var(--gray-100); padding-top: 14px; font-size: 14px; display: flex; flex-direction: column; gap: 8px;">
            <div style="display: flex; justify-content: space-between;">
                <span style="color: var(--gray-500);">Subtotal</span>
                <span>৳{{ number_format($order->subtotal_amount, 2) }}</span>
            </div>
            @if($order->discount_amount > 0)
                <div style="display: flex; justify-content: space-between; color: var(--success);">
                    <span>Coupon Discount ({{ $order->coupon_code }})</span>
                    <span>-৳{{ number_format($order->discount_amount, 2) }}</span>
                </div>
            @endif
            <div style="display: flex; justify-content: space-between;">
                <span style="color: var(--gray-500);">Delivery Charge</span>
                <span>৳{{ number_format($order->delivery_charge, 2) }}</span>
            </div>
            <div style="border-top: 2px solid var(--gray-100); padding-top: 12px; display: flex; justify-content: space-between; font-size: 18px; font-weight: 800;">
                <span>Total Amount</span>
                <span style="color: var(--primary);">৳{{ number_format($order->total_amount, 2) }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 14px; color: var(--gray-600);">
                <span>Amount Paid</span>
                <span>৳{{ number_format($order->paid_amount, 2) }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 14px; color: {{ $order->due_amount > 0 ? 'var(--danger)' : 'var(--success)' }}; font-weight: 700;">
                <span>Outstanding Balance</span>
                <span>৳{{ number_format($order->due_amount, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Details Grid -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 22px;">
            <h4 style="font-size: 15px; font-weight: 700; margin-bottom: 10px; color: var(--gray-500); text-transform: uppercase;">Delivery Address</h4>
            <div style="white-space: pre-line; font-size: 14px; line-height: 1.6; color: var(--gray-800);">
                {{ $order->shipping_address }}
            </div>
            @if($order->delivery_man_name)
                <div style="margin-top: 14px; padding-top: 12px; border-top: 1px solid var(--gray-100); font-size: 13px;">
                    <strong>Assigned Delivery Rider:</strong><br>
                    {{ $order->delivery_man_name }} ({{ $order->delivery_man_phone }})
                </div>
            @endif
        </div>

        <div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 22px;">
            <h4 style="font-size: 15px; font-weight: 700; margin-bottom: 10px; color: var(--gray-500); text-transform: uppercase;">Payment Information</h4>
            <div style="font-size: 14px; line-height: 1.7; color: var(--gray-800);">
                <div><strong>Method:</strong> {{ strtoupper($order->payment_method) }}</div>
                <div><strong>Payment Status:</strong> <span style="text-transform: capitalize; font-weight: 700;">{{ str_replace('_', ' ', $order->payment_status) }}</span></div>
                @if($order->payments->isNotEmpty())
                    <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid var(--gray-100);">
                        <strong>Transactions:</strong>
                        @foreach($order->payments as $p)
                            <div style="font-size: 13px; color: var(--gray-600); margin-top: 4px;">
                                ৳{{ number_format($p->amount, 2) }} via {{ strtoupper($p->payment_method) }} (Ref: {{ $p->transaction_id ?? 'N/A' }}) - <em>{{ $p->verification_status }}</em>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Cancel Order Modal -->
<div id="cancel-order-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: var(--radius-lg); padding: 28px; max-width: 480px; width: 90%; margin: 100px auto;">
        <h3 style="font-size: 18px; font-weight: 800; margin-bottom: 10px;">Cancel Order #{{ $order->order_number }}</h3>
        <p style="font-size: 14px; color: var(--gray-600); margin-bottom: 16px;">
            Are you sure you want to cancel this order? Stock reserved for you will be released back to the store inventory.
        </p>

        <form action="{{ route('customer.orders.cancel', $order) }}" method="POST">
            @csrf
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Reason for Cancellation *</label>
                <textarea name="reason" required rows="3" placeholder="Please tell us why you need to cancel this order..." style="width: 100%; padding: 10px; border: 1px solid var(--gray-300); border-radius: var(--radius-sm); font-size: 14px; font-family: inherit;"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('cancel-order-modal').style.display='none'">Keep Order</button>
                <button type="submit" class="btn btn-danger btn-sm">Confirm Cancellation</button>
            </div>
        </form>
    </div>
</div>
@endsection
