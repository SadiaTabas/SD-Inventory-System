@extends('layouts.customer')

@section('title', 'Order Confirmed - SD Store')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <!-- Confirmation Banner -->
    <div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 40px; text-align: center; margin-bottom: 30px; box-shadow: var(--shadow-sm);">
        <div style="width: 72px; height: 72px; border-radius: 50%; background: #ecfdf5; color: #10b981; font-size: 36px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px;">
            ✓
        </div>
        <h1 style="font-size: 28px; font-weight: 800; margin-bottom: 8px; color: var(--gray-900);">Thank You For Your Order!</h1>
        <p style="font-size: 15px; color: var(--gray-500); margin-bottom: 20px;">
            Your order has been received and is being processed by our warehouse.
        </p>
        <div style="display: inline-block; background: #f8fafc; border: 1.5px dashed var(--primary); border-radius: var(--radius); padding: 12px 24px;">
            <span style="font-size: 13px; color: var(--gray-500); display: block;">Order Reference Number</span>
            <span style="font-size: 22px; font-weight: 800; color: var(--primary); letter-spacing: 1px;">{{ $order->order_number }}</span>
        </div>
    </div>

    <!-- Visual Tracking Steps -->
    <div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 30px; margin-bottom: 30px;">
        <h3 style="font-size: 17px; font-weight: 800; margin-bottom: 24px;">Order Status Tracker</h3>
        
        <div style="display: flex; justify-content: space-between; position: relative; margin: 0 10px;">
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
                    $isDone = $stepIdx <= $currentIndex && $order->status !== 'cancelled';
                @endphp
                <div style="display: flex; flex-direction: column; align-items: center; text-align: center; flex: 1; z-index: 2;">
                    <div style="width: 34px; height: 34px; border-radius: 50%; background: {{ $isDone ? 'var(--primary)' : 'var(--gray-200)' }}; color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; margin-bottom: 8px;">
                        {{ $isDone ? '✓' : $stepIdx + 1 }}
                    </div>
                    <span style="font-size: 12px; font-weight: 600; color: {{ $isDone ? 'var(--gray-900)' : 'var(--gray-400)' }};">{{ $label }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Order Details Breakdown -->
    <div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 30px; margin-bottom: 30px;">
        <h3 style="font-size: 18px; font-weight: 800; margin-bottom: 18px; border-bottom: 1px solid var(--gray-100); padding-bottom: 12px;">Order Summary</h3>

        <div style="display: flex; flex-direction: column; gap: 14px; margin-bottom: 24px;">
            @foreach($order->items as $item)
                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 14px;">
                    <div>
                        <strong>{{ $item->product_name ?? $item->product->name ?? 'Product' }}</strong>
                        <div style="color: var(--gray-500); font-size: 12px;">Qty: {{ $item->quantity }} &times; ৳{{ number_format($item->price, 2) }}</div>
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
                    <span>Discount ({{ $order->coupon_code }})</span>
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
        </div>

        <div style="margin-top: 24px; padding-top: 18px; border-top: 1px solid var(--gray-100); display: grid; grid-template-columns: 1fr 1fr; gap: 20px; font-size: 13px;">
            <div>
                <strong style="display: block; margin-bottom: 4px; color: var(--gray-500); text-transform: uppercase;">Delivery Address:</strong>
                <div style="white-space: pre-line; line-height: 1.5;">{{ $order->shipping_address }}</div>
            </div>
            <div>
                <strong style="display: block; margin-bottom: 4px; color: var(--gray-500); text-transform: uppercase;">Payment Details:</strong>
                <div><strong>Method:</strong> {{ strtoupper($order->payment_method) }}</div>
                <div><strong>Payment Status:</strong> <span style="text-transform: capitalize; font-weight: 700;">{{ str_replace('_', ' ', $order->payment_status) }}</span></div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div style="display: flex; justify-content: center; gap: 14px; flex-wrap: wrap;">
        <a href="{{ route('customer.orders.show', $order) }}" class="btn btn-primary">Track &amp; View Order Details &rarr;</a>
        <a href="{{ route('customer.orders.invoice', $order) }}" class="btn btn-outline">Download Invoice PDF</a>
        <a href="{{ route('shop') }}" class="btn btn-secondary">Continue Shopping</a>
    </div>
</div>
@endsection
