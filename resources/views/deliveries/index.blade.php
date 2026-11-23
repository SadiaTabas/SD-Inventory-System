@extends('layouts.app')

@section('title', 'Deliveries & Riders Management')

@section('content')
<div class="container" style="max-width: 1380px;">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h1 style="text-align: left; margin: 0;">Deliveries &amp; Rider Operations</h1>
            <p style="color: #6b7280; font-size: 14px; margin-top: 4px;">Register delivery riders, dispatch parcels, and monitor in-transit deliveries.</p>
        </div>
        <a href="{{ route('deliveries.create') }}" class="create-button" style="margin-bottom: 0;">+ Register Delivery Rider</a>
    </div>

    @if (session('success'))
        <div class="success-message">{{ session('success') }}</div>
    @endif

    <!-- Pending Deliveries Dispatch Box -->
    @if($pendingDeliveries->isNotEmpty())
        <div class="table-container" style="margin-bottom: 28px; border-left: 4px solid #3b82f6;">
            <h2 style="font-size: 18px; margin-bottom: 14px;">Active Deliveries In Transit</h2>
            <table>
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Assigned Rider</th>
                        <th>Shipping Address</th>
                        <th>Delivery Status</th>
                        <th>Update Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingDeliveries as $ord)
                        <tr>
                            <td>
                                <a href="{{ route('order.show', $ord) }}" style="font-weight: 700; color: #2563eb;">{{ $ord->order_number }}</a>
                            </td>
                            <td>{{ $ord->user->name ?? ($ord->customer->name ?? 'Customer') }} ({{ $ord->user->phone ?? '' }})</td>
                            <td><strong>{{ $ord->deliveryAgent->name ?? ($ord->delivery_man_name ?? 'Rider') }}</strong> ({{ $ord->deliveryAgent->phone ?? '' }})</td>
                            <td style="font-size: 13px; max-width: 250px;">{{ $ord->shipping_address }}</td>
                            <td>
                                <span class="status {{ $ord->delivery_status === 'delivered' ? 'active' : 'inactive' }}">
                                    {{ ucfirst(str_replace('_', ' ', $ord->delivery_status)) }}
                                </span>
                            </td>
                            <td>
                                <form action="{{ route('deliveries.order.status', $ord) }}" method="POST" style="display: flex; gap: 6px; align-items: center;">
                                    @csrf
                                    <select name="delivery_status" style="padding: 4px; font-size: 12px; border: 1px solid #d1d5db; border-radius: 4px;">
                                        <option value="assigned" {{ $ord->delivery_status === 'assigned' ? 'selected' : '' }}>Assigned</option>
                                        <option value="out_for_delivery" {{ $ord->delivery_status === 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                                        <option value="delivered" {{ $ord->delivery_status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                        <option value="failed" {{ $ord->delivery_status === 'failed' ? 'selected' : '' }}>Failed</option>
                                    </select>
                                    <button type="submit" style="padding: 4px 8px; background: #3b82f6; color: white; border: none; border-radius: 4px; font-size: 12px; font-weight: 700; cursor: pointer;">Update</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- Delivery Riders Roster Table -->
    <div class="table-container">
        <h2>Registered Delivery Riders Roster</h2>
        <table>
            <thead>
                <tr>
                    <th>Rider Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Vehicle Details</th>
                    <th>Assigned Deliveries</th>
                    <th>Availability Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($agents as $agent)
                    <tr>
                        <td><strong>{{ $agent->name }}</strong></td>
                        <td>{{ $agent->phone }}</td>
                        <td>{{ $agent->email ?? '—' }}</td>
                        <td>{{ $agent->vehicle_type ?? 'Motorcycle' }} ({{ $agent->vehicle_number ?? 'N/A' }})</td>
                        <td><strong>{{ $agent->orders_count }} Deliveries</strong></td>
                        <td>
                            <span class="status {{ $agent->status === 'available' ? 'active' : 'inactive' }}">
                                {{ ucfirst($agent->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('deliveries.edit', $agent) }}" class="edit-button">Edit Rider</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-message">No delivery riders registered. Click "+ Register Delivery Rider" above to add riders.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $agents->links() }}
        </div>
    </div>

</div>
@endsection
