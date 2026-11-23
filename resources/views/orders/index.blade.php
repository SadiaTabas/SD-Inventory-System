@extends('layouts.app')

@section('title', 'Order Management')

@section('content')

<div class="container" style="max-width: 1380px;">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 14px;">
        <div>
            <h1 style="text-align: left; margin: 0;">Order Management</h1>
            <p style="color: #6b7280; font-size: 14px; margin-top: 4px;">Track, verify payments, assign riders, and manage online storefront and admin orders.</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('order.create') }}" class="create-button" style="margin-bottom: 0;">+ Create Manual Order</a>
            <a href="{{ route('reports.index', ['type' => 'orders']) }}" class="create-button" style="background-color: #6366f1; margin-bottom: 0;">📊 Orders Report</a>
        </div>
    </div>

    @if (session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="error-box" style="max-width: 100%;">
            {{ session('error') }}
        </div>
    @endif

    <!-- Filters Section -->
    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; margin-bottom: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <form action="{{ route('order.index') }}" method="GET" style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr 1fr auto; gap: 12px; align-items: end;">
            <div>
                <label style="display: block; font-size: 12px; font-weight: 700; color: #4b5563; margin-bottom: 4px;">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Order #, Customer, Phone..." style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
            </div>

            <div>
                <label style="display: block; font-size: 12px; font-weight: 700; color: #4b5563; margin-bottom: 4px;">Order Source</label>
                <select name="order_source" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; background: white;">
                    <option value="">All Sources</option>
                    <option value="online" {{ request('order_source') === 'online' ? 'selected' : '' }}>Online Storefront</option>
                    <option value="admin" {{ request('order_source') === 'admin' ? 'selected' : '' }}>Admin Manual</option>
                </select>
            </div>

            <div>
                <label style="display: block; font-size: 12px; font-weight: 700; color: #4b5563; margin-bottom: 4px;">Order Status</label>
                <select name="status" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; background: white;">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Shipped</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div>
                <label style="display: block; font-size: 12px; font-weight: 700; color: #4b5563; margin-bottom: 4px;">Payment</label>
                <select name="payment_status" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; background: white;">
                    <option value="">All Payments</option>
                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="partially_paid" {{ request('payment_status') === 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                    <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>

            <div>
                <label style="display: block; font-size: 12px; font-weight: 700; color: #4b5563; margin-bottom: 4px;">From Date</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" style="width: 100%; padding: 7px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px;">
            </div>

            <div style="display: flex; gap: 6px;">
                <button type="submit" style="padding: 8px 16px; background-color: #2563eb; color: white; border: none; border-radius: 6px; font-weight: 700; font-size: 13px; cursor: pointer;">Filter</button>
                <a href="{{ route('order.index') }}" style="padding: 8px 12px; background-color: #f3f4f6; color: #4b5563; border-radius: 6px; font-weight: 600; font-size: 13px; text-decoration: none;">Reset</a>
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Order No</th>
                    <th>Source</th>
                    <th>Customer</th>
                    <th>Rider</th>
                    <th>Total</th>
                    <th>Paid</th>
                    <th>Due</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td>
                            <strong>{{ $order->order_number ?? ('#' . $order->id) }}</strong>
                            <br>
                            <small style="color: #6b7280;">{{ $order->created_at->format('d M Y, h:i A') }}</small>
                        </td>

                        <td>
                            <span style="display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; text-transform: uppercase; background: {{ $order->order_source === 'online' ? '#eff6ff; color:#1e40af;' : '#f5f3ff; color:#6d28d9;' }}">
                                {{ $order->order_source ?? 'Admin' }}
                            </span>
                        </td>

                        <td>
                            <strong>{{ $order->user->name ?? ($order->customer->name ?? 'User Deleted') }}</strong>
                            <br>
                            <small style="color: #6b7280;">{{ $order->user->phone ?? ($order->customer->phone ?? '') }}</small>
                        </td>

                        <td>
                            @if($order->deliveryAgent)
                                <strong>{{ $order->deliveryAgent->name }}</strong>
                                <br><small style="color: #6b7280;">{{ $order->deliveryAgent->phone }}</small>
                            @elseif($order->delivery_man_name)
                                <strong>{{ $order->delivery_man_name }}</strong>
                            @else
                                <span style="color: #9ca3af; font-size: 12px;">Unassigned</span>
                            @endif
                        </td>

                        <td>
                            <strong>৳{{ number_format($order->total_amount, 2) }}</strong>
                            @if (($order->discount_amount ?? 0) > 0)
                                <br><small style="color: #e74c3c;">(-৳{{ number_format($order->discount_amount, 2) }})</small>
                            @endif
                        </td>

                        <td>
                            <strong style="color: #27ae60;">৳{{ number_format($order->paid_amount ?? 0, 2) }}</strong>
                        </td>

                        <td>
                            @if (($order->due_amount ?? 0) > 0)
                                <strong style="color: #e74c3c;">৳{{ number_format($order->due_amount, 2) }}</strong>
                            @else
                                <span style="color: #27ae60; font-weight: bold;">৳0.00</span>
                            @endif
                        </td>

                        <td>
                            <span class="status {{ $order->payment_status === 'paid' ? 'active' : 'inactive' }}" style="font-size: 12px;">
                                {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}
                            </span>
                            <br>
                            <small style="font-weight: 600; color: #4b5563;">{{ strtoupper($order->payment_method ?? 'COD') }}</small>
                        </td>

                        <td>
                            <span class="status {{ $order->status === 'delivered' ? 'active' : ($order->status === 'cancelled' ? 'inactive' : '') }}" style="font-size: 12px; background: {{ $order->status === 'cancelled' ? '#fef2f2; color:#991b1b;' : ($order->status === 'delivered' ? '#ecfdf5; color:#065f46;' : '#eff6ff; color:#1e40af;') }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>

                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('order.show', $order) }}" class="view-button" title="View Order">
                                    View
                                </a>

                                <a href="{{ route('order.invoice', $order) }}" class="view-button" style="background-color: #2ecc71;" title="Download PDF Invoice">
                                    PDF
                                </a>

                                @php
                                    $hoursSinceCreation = $order->created_at->diffInHours(now());
                                    $isEditable = ($hoursSinceCreation <= 48) && !in_array($order->status, ['delivered', 'cancelled']);
                                @endphp

                                @if ($isEditable)
                                    <a href="{{ route('order.edit', $order) }}" class="edit-button" title="Edit Products (48h Window Active)">
                                        Edit
                                    </a>
                                @endif

                                <form action="{{ route('order.destroy', $order) }}" method="POST" class="delete-form" onsubmit="return confirm('Delete this order? (Stock will be automatically restocked)');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-button">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="empty-message">No orders match your filter criteria.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection
