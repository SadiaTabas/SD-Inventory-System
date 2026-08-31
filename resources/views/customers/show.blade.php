@extends('layouts.app')

@section('title', 'Customer Details')

@section('content')

<div class="container">

    <div class="top-actions">
        <h1>Customer Details</h1>
        <a
            class="edit-button"
            href="{{ route('customer.edit', $customer) }}"
        >
            Edit Customer
        </a>
    </div>

    <div class="dashboard-box" style="margin-bottom: 25px;">

        <div class="box-header">
            <div>
                <h2 style="margin: 0;">{{ $customer->name }}</h2>
                <p style="margin: 4px 0 0; color: #6b7280;">Customer Account #{{ $customer->id }}</p>
            </div>

            @if ($customer->status)
                <span class="status active">Active</span>
            @else
                <span class="status inactive">Inactive</span>
            @endif
        </div>

        @php
            $primaryAddr = $customer->addresses->firstWhere('is_default', 1) ?? $customer->addresses->first();
        @endphp

        <div class="dashboard-main-grid" style="margin-top: 15px; margin-bottom: 0;">
            <div>
                <p><strong>Email:</strong> {{ $customer->email }}</p>
                <p><strong>Phone:</strong> {{ $customer->phone ?: 'N/A' }}</p>
                <p><strong>Role:</strong> {{ ucfirst($customer->role) }}</p>
            </div>
            <div>
                <p><strong>Primary City:</strong> {{ $primaryAddr->city ?? 'N/A' }}</p>
                <p><strong>Primary Address:</strong> {{ $primaryAddr->address ?? 'No address registered' }}</p>
                <p><strong>Registered On:</strong> {{ $customer->created_at ? $customer->created_at->format('d M Y, h:i A') : 'N/A' }}</p>
            </div>
        </div>

    </div>

    {{-- Orders Section --}}
    <div class="table-container">

        <div class="box-header" style="margin-bottom: 15px;">
            <h2>Customer Orders</h2>
            <span class="badge" style="background:#e0f2fe; color:#0369a1; padding: 4px 10px; border-radius: 12px; font-weight: bold;">
                {{ $customer->orders->count() }} Total
            </span>
        </div>

        @if ($customer->orders->count() > 0)

            <table>

                <thead>
                    <tr>
                        <th>Order Number</th>
                        <th>Date</th>
                        <th>Payment Status</th>
                        <th>Delivery Status</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach ($customer->orders as $order)

                        <tr>
                            <td>
                                <strong>{{ $order->order_number ?? ('#' . $order->id) }}</strong>
                            </td>

                            <td>
                                {{ $order->created_at ? $order->created_at->format('d M Y') : 'N/A' }}
                            </td>

                            <td>
                                <span class="status {{ $order->payment_status === 'paid' ? 'active' : 'inactive' }}">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </td>

                            <td>
                                <span class="status {{ $order->delivery_status === 'delivered' ? 'active' : 'inactive' }}">
                                    {{ ucfirst(str_replace('_', ' ', $order->delivery_status)) }}
                                </span>
                            </td>

                            <td>
                                <strong>৳{{ number_format($order->total_amount, 2) }}</strong>
                            </td>

                            <td>
                                <a
                                    class="view-button"
                                    href="{{ route('order.show', $order) }}"
                                >
                                    View
                                </a>
                            </td>
                        </tr>

                    @endforeach

                </tbody>

            </table>

        @else

            <div class="empty-message">
                This customer has not placed any orders yet.
            </div>

        @endif

    </div>

    <a
        class="back-link"
        href="{{ route('customer.index') }}"
    >
        ← Back to Customers
    </a>

</div>

@endsection