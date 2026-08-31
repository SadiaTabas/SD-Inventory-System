@extends('layouts.app')

@section('title', 'Customer Details')

@section('content')

<div class="container">

 
<h1>Customer Details</h1>

<div class="details-card">

    <div class="details-header">
        <div>
            <h2>{{ $customer->name }}</h2>
            <p>Customer #{{ $customer->id }}</p>
        </div>

        @if ($customer->status)

            <span class="status active">
                Active
            </span>

        @else

            <span class="status inactive">
                Inactive
            </span>

        @endif
    </div>

    <div class="details-grid">

        <div class="detail-item">
            <span class="detail-label">Email</span>
            <span class="detail-value">
                {{ $customer->email }}
            </span>
        </div>

        <div class="detail-item">
            <span class="detail-label">Phone</span>
            <span class="detail-value">
                {{ $customer->phone }}
            </span>
        </div>

        <div class="detail-item">
            <span class="detail-label">City</span>
            <span class="detail-value">
                {{ $customer->city ?: 'N/A' }}
            </span>
        </div>

        <div class="detail-item">
            <span class="detail-label">Address</span>
            <span class="detail-value">
                {{ $customer->address ?: 'N/A' }}
            </span>
        </div>

    </div>

</div>

{{-- Orders Section --}}
<div class="orders-card">

    <div class="section-header">

        <h2>Customer Orders</h2>

        <span class="order-count">
            {{ $customer->orders->count() }} Orders
        </span>

    </div>

    @if ($customer->orders->count() > 0)

        <div class="table-container">

            <table>

                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Total</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach ($customer->orders as $order)

                        <tr>

                            <td>
                                #{{ $order->id }}
                            </td>

                            <td>
                                {{ $order->created_at->format('d M Y') }}
                            </td>

                            <td>
                                {{ $order->status }}
                            </td>

                            <td>
                                {{ $order->total }}
                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    @else

        <div class="empty-message">
            This customer has no orders yet.
        </div>

    @endif

</div>

<div class="details-actions">

    <a
        class="edit-button"
        href="{{ route('customer.edit', $customer) }}"
    >
        Edit Customer
    </a>

    <a
        class="back-link"
        href="{{ route('customer.index') }}"
    >
        ← Back to Customers
    </a>

</div>
 

</div>

@endsection
