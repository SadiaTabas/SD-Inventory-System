@extends('layouts.customer')

@section('title', 'My Orders - SD Store')

@section('content')
<div style="display: grid; grid-template-columns: 260px 1fr; gap: 30px; align-items: start;">
    <!-- Customer Sidebar -->
    <div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 24px;">
        <nav style="display: flex; flex-direction: column; gap: 6px;">
            <a href="{{ route('customer.account') }}" style="padding: 10px 14px; border-radius: var(--radius-sm); font-size: 14px; font-weight: 600; color: var(--gray-700);">📊 Dashboard</a>
            <a href="{{ route('customer.orders') }}" style="padding: 10px 14px; border-radius: var(--radius-sm); font-size: 14px; font-weight: 600; background: var(--primary-light); color: var(--primary);">📦 My Orders</a>
            <a href="{{ route('customer.addresses') }}" style="padding: 10px 14px; border-radius: var(--radius-sm); font-size: 14px; font-weight: 600; color: var(--gray-700);">📍 Saved Addresses</a>
            <a href="{{ route('customer.wishlist') }}" style="padding: 10px 14px; border-radius: var(--radius-sm); font-size: 14px; font-weight: 600; color: var(--gray-700);">♥ My Wishlist</a>
            <a href="{{ route('customer.reviews') }}" style="padding: 10px 14px; border-radius: var(--radius-sm); font-size: 14px; font-weight: 600; color: var(--gray-700);">★ My Reviews</a>
            <a href="{{ route('customer.profile') }}" style="padding: 10px 14px; border-radius: var(--radius-sm); font-size: 14px; font-weight: 600; color: var(--gray-700);">⚙️ Profile Settings</a>
        </nav>
    </div>

    <!-- Main Content -->
    <div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
            <h1 style="font-size: 24px; font-weight: 800;">Purchase History &amp; Orders</h1>

            <!-- Filter Tabs -->
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                <a href="{{ route('customer.orders') }}" class="btn {{ !$status ? 'btn-primary' : 'btn-secondary' }} btn-sm">All</a>
                <a href="{{ route('customer.orders', ['status' => 'pending']) }}" class="btn {{ $status == 'pending' ? 'btn-primary' : 'btn-secondary' }} btn-sm">Pending</a>
                <a href="{{ route('customer.orders', ['status' => 'processing']) }}" class="btn {{ $status == 'processing' ? 'btn-primary' : 'btn-secondary' }} btn-sm">Processing</a>
                <a href="{{ route('customer.orders', ['status' => 'shipped']) }}" class="btn {{ $status == 'shipped' ? 'btn-primary' : 'btn-secondary' }} btn-sm">Shipped</a>
                <a href="{{ route('customer.orders', ['status' => 'delivered']) }}" class="btn {{ $status == 'delivered' ? 'btn-primary' : 'btn-secondary' }} btn-sm">Delivered</a>
                <a href="{{ route('customer.orders', ['status' => 'cancelled']) }}" class="btn {{ $status == 'cancelled' ? 'btn-primary' : 'btn-secondary' }} btn-sm">Cancelled</a>
            </div>
        </div>

        @if($orders->isEmpty())
            <div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 50px 20px; text-align: center;">
                <h3 style="font-size: 18px; font-weight: 800; margin-bottom: 8px;">No orders found</h3>
                <p style="color: var(--gray-500); font-size: 14px; margin-bottom: 20px;">You don't have any orders matching this status.</p>
                <a href="{{ route('shop') }}" class="btn btn-primary">Start Shopping &rarr;</a>
            </div>
        @else
            <div style="display: flex; flex-direction: column; gap: 16px;">
                @foreach($orders as $order)
                    <div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 22px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--gray-100); padding-bottom: 12px; margin-bottom: 14px; flex-wrap: wrap; gap: 10px;">
                            <div>
                                <span style="font-size: 16px; font-weight: 800; color: var(--primary);">{{ $order->order_number }}</span>
                                <span style="font-size: 13px; color: var(--gray-400); margin-left: 10px;">{{ $order->created_at->format('M d, Y h:i A') }}</span>
                            </div>
                            <div style="display: flex; gap: 8px;">
                                <span style="font-size: 12px; font-weight: 700; padding: 3px 8px; border-radius: 4px; text-transform: capitalize; background: {{ $order->status === 'delivered' ? '#ecfdf5; color:#065f46;' : ($order->status === 'cancelled' ? '#fef2f2; color:#991b1b;' : '#eff6ff; color:#1e40af;') }}">
                                    Order: {{ $order->status }}
                                </span>
                                <span style="font-size: 12px; font-weight: 700; padding: 3px 8px; border-radius: 4px; text-transform: capitalize; background: {{ $order->payment_status === 'paid' ? '#ecfdf5; color:#065f46;' : '#fffbeb; color:#92400e;' }}">
                                    Payment: {{ str_replace('_', ' ', $order->payment_status) }}
                                </span>
                            </div>
                        </div>

                        <!-- Item Summary -->
                        <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 14px;">
                            @foreach($order->items as $item)
                                <div style="display: flex; justify-content: space-between; font-size: 14px;">
                                    <span>{{ $item->product_name ?? $item->product->name ?? 'Product' }} &times; <strong>{{ $item->quantity }}</strong></span>
                                    <span style="font-weight: 600;">৳{{ number_format($item->subtotal, 2) }}</span>
                                </div>
                            @endforeach
                        </div>

                        <!-- Bottom Row -->
                        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--gray-100); padding-top: 12px; flex-wrap: wrap; gap: 10px;">
                            <div>
                                <span style="font-size: 13px; color: var(--gray-500);">Total:</span>
                                <strong style="font-size: 18px; color: var(--gray-900); margin-left: 6px;">৳{{ number_format($order->total_amount, 2) }}</strong>
                                <span style="font-size: 12px; color: var(--gray-400); margin-left: 8px;">({{ strtoupper($order->payment_method) }})</span>
                            </div>
                            <div style="display: flex; gap: 8px;">
                                <a href="{{ route('customer.orders.invoice', $order) }}" class="btn btn-outline btn-sm">Invoice</a>
                                <a href="{{ route('customer.orders.show', $order) }}" class="btn btn-primary btn-sm">View Timeline &rarr;</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="margin-top: 24px;">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
