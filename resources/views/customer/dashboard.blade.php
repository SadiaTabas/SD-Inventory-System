@extends('layouts.customer')

@section('title', 'My Account - SD Store')

@section('content')
<div style="display: grid; grid-template-columns: 260px 1fr; gap: 30px; align-items: start;">
    <!-- Customer Sidebar -->
    <div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 24px;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid var(--gray-100);">
            <div style="width: 48px; height: 48px; border-radius: 50%; background: #eff6ff; color: var(--primary); font-size: 20px; font-weight: 800; display: flex; align-items: center; justify-content: center;">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <strong style="display: block; font-size: 15px;">{{ $user->name }}</strong>
                <span style="font-size: 12px; color: var(--gray-500);">Member since {{ $user->created_at->format('M Y') }}</span>
            </div>
        </div>

        <nav style="display: flex; flex-direction: column; gap: 6px;">
            <a href="{{ route('customer.account') }}" style="padding: 10px 14px; border-radius: var(--radius-sm); font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 10px; background: {{ request()->routeIs('customer.account') ? 'var(--primary-light)' : 'transparent' }}; color: {{ request()->routeIs('customer.account') ? 'var(--primary)' : 'var(--gray-700)' }};">
                <span>📊</span> Dashboard
            </a>
            <a href="{{ route('customer.orders') }}" style="padding: 10px 14px; border-radius: var(--radius-sm); font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 10px; background: {{ request()->routeIs('customer.orders*') ? 'var(--primary-light)' : 'transparent' }}; color: {{ request()->routeIs('customer.orders*') ? 'var(--primary)' : 'var(--gray-700)' }};">
                <span>📦</span> My Orders
            </a>
            <a href="{{ route('customer.addresses') }}" style="padding: 10px 14px; border-radius: var(--radius-sm); font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 10px; background: {{ request()->routeIs('customer.addresses*') ? 'var(--primary-light)' : 'transparent' }}; color: {{ request()->routeIs('customer.addresses*') ? 'var(--primary)' : 'var(--gray-700)' }};">
                <span>📍</span> Saved Addresses
            </a>
            <a href="{{ route('customer.wishlist') }}" style="padding: 10px 14px; border-radius: var(--radius-sm); font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 10px; background: {{ request()->routeIs('customer.wishlist*') ? 'var(--primary-light)' : 'transparent' }}; color: {{ request()->routeIs('customer.wishlist*') ? 'var(--primary)' : 'var(--gray-700)' }};">
                <span>♥</span> My Wishlist
            </a>
            <a href="{{ route('customer.reviews') }}" style="padding: 10px 14px; border-radius: var(--radius-sm); font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 10px; background: {{ request()->routeIs('customer.reviews*') ? 'var(--primary-light)' : 'transparent' }}; color: {{ request()->routeIs('customer.reviews*') ? 'var(--primary)' : 'var(--gray-700)' }};">
                <span>★</span> My Reviews
            </a>
            <a href="{{ route('customer.profile') }}" style="padding: 10px 14px; border-radius: var(--radius-sm); font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 10px; background: {{ request()->routeIs('customer.profile*') ? 'var(--primary-light)' : 'transparent' }}; color: {{ request()->routeIs('customer.profile*') ? 'var(--primary)' : 'var(--gray-700)' }};">
                <span>⚙️</span> Profile Settings
            </a>
            <form action="{{ route('logout') }}" method="POST" style="margin-top: 10px; padding-top: 10px; border-top: 1px solid var(--gray-100);">
                @csrf
                <button type="submit" style="width: 100%; text-align: left; padding: 10px 14px; background: none; border: none; color: var(--danger); font-size: 14px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 10px;">
                    <span>🚪</span> Sign Out
                </button>
            </form>
        </nav>
    </div>

    <!-- Main Content Area -->
    <div>
        <!-- Greeting Card -->
        <div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 28px; margin-bottom: 24px;">
            <h1 style="font-size: 24px; font-weight: 800; margin-bottom: 6px;">Welcome back, {{ $user->name }}!</h1>
            <p style="color: var(--gray-500); font-size: 14px;">Manage your orders, update your profile details, and track deliveries all in one place.</p>
        </div>

        <!-- Quick Stats -->
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 28px;">
            <div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius); padding: 20px;">
                <span style="font-size: 12px; font-weight: 700; color: var(--gray-500); text-transform: uppercase;">Total Orders</span>
                <div style="font-size: 28px; font-weight: 800; color: var(--gray-900); margin-top: 4px;">{{ $totalOrders }}</div>
            </div>
            <div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius); padding: 20px;">
                <span style="font-size: 12px; font-weight: 700; color: var(--gray-500); text-transform: uppercase;">In Progress</span>
                <div style="font-size: 28px; font-weight: 800; color: #f59e0b; margin-top: 4px;">{{ $pendingOrders }}</div>
            </div>
            <div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius); padding: 20px;">
                <span style="font-size: 12px; font-weight: 700; color: var(--gray-500); text-transform: uppercase;">Delivered</span>
                <div style="font-size: 28px; font-weight: 800; color: #10b981; margin-top: 4px;">{{ $deliveredOrders }}</div>
            </div>
            <div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius); padding: 20px;">
                <span style="font-size: 12px; font-weight: 700; color: var(--gray-500); text-transform: uppercase;">Wishlist Items</span>
                <div style="font-size: 28px; font-weight: 800; color: #ec4899; margin-top: 4px;">{{ $wishlistCount }}</div>
            </div>
        </div>

        <!-- Recent Orders Table -->
        <div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 24px; margin-bottom: 28px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3 style="font-size: 17px; font-weight: 800;">Recent Orders</h3>
                <a href="{{ route('customer.orders') }}" style="color: var(--primary); font-weight: 700; font-size: 13px;">View All Orders &rarr;</a>
            </div>

            @if($recentOrders->isEmpty())
                <p style="color: var(--gray-500); font-size: 14px; text-align: center; padding: 20px;">You haven't placed any orders yet.</p>
            @else
                <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--gray-200); text-align: left; color: var(--gray-500); font-size: 12px; text-transform: uppercase;">
                            <th style="padding: 10px 0;">Order #</th>
                            <th style="padding: 10px;">Date</th>
                            <th style="padding: 10px;">Total</th>
                            <th style="padding: 10px;">Status</th>
                            <th style="padding: 10px; text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentOrders as $order)
                            <tr style="border-bottom: 1px solid var(--gray-100);">
                                <td style="padding: 14px 0; font-weight: 700;">{{ $order->order_number }}</td>
                                <td style="padding: 14px 10px; color: var(--gray-600);">{{ $order->created_at->format('M d, Y') }}</td>
                                <td style="padding: 14px 10px; font-weight: 700;">৳{{ number_format($order->total_amount, 2) }}</td>
                                <td style="padding: 14px 10px;">
                                    <span style="font-size: 12px; font-weight: 700; padding: 3px 8px; border-radius: 4px; text-transform: capitalize; background: {{ $order->status === 'delivered' ? '#ecfdf5; color:#065f46;' : ($order->status === 'cancelled' ? '#fef2f2; color:#991b1b;' : '#eff6ff; color:#1e40af;') }}">
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td style="padding: 14px 0; text-align: right;">
                                    <a href="{{ route('customer.orders.show', $order) }}" class="btn btn-outline btn-sm">Details</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <!-- Default Address Preview -->
        <div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <h3 style="font-size: 17px; font-weight: 800;">Primary Delivery Address</h3>
                <a href="{{ route('customer.addresses') }}" style="color: var(--primary); font-weight: 700; font-size: 13px;">Manage Addresses &rarr;</a>
            </div>
            @if($defaultAddress)
                <p style="font-size: 14px; line-height: 1.6; color: var(--gray-700);">
                    <strong>{{ $defaultAddress->full_name }}</strong> ({{ $defaultAddress->phone }})<br>
                    {{ $defaultAddress->formatted_address }}
                </p>
            @else
                <p style="color: var(--gray-500); font-size: 14px;">No saved address yet. <a href="{{ route('customer.addresses') }}" style="color: var(--primary); font-weight: 600;">Add your first address</a>.</p>
            @endif
        </div>
    </div>
</div>
@endsection
