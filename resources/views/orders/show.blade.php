@extends('layouts.app')

@section('title', 'Order Details')

@section('content')

<div class="container">

    <div class="top-actions">
        <h1>Order: {{ $order->order_number ?? ('#' . $order->id) }}</h1>
        <a class="edit-button" href="{{ route('order.edit', $order) }}">
            Edit Order
        </a>
    </div>

    <div class="dashboard-main-grid" style="margin-bottom: 25px;">

       
        <div class="dashboard-box">
            <div class="box-header">
                <h2>Customer Details</h2>
            </div>
            <p><strong>Name:</strong> {{ $order->customer->name ?? 'User Deleted' }}</p>
            <p><strong>Email:</strong> {{ $order->customer->email ?? 'N/A' }}</p>
            <p><strong>Phone:</strong> {{ $order->customer->phone ?? 'N/A' }}</p>
            <p><strong>Shipping Address:</strong> {{ $order->shipping_address }}</p>
            <p><strong>Delivery Slot:</strong> {{ $order->delivery_slot ?? 'Not specified' }}</p>
            <p><strong>Order Source:</strong> {{ ucfirst($order->order_placed_by) }} (Audit: {{ $order->created_by ?? 'System' }})</p>
        </div>

         
        <div class="dashboard-box">
            <div class="box-header">
                <h2>Logistics & Payment</h2>
            </div>
            <p><strong>Assigned Rider:</strong> {{ $order->deliveryAgent->name ?? ($order->delivery_man_name ?? 'Unassigned') }}</p>
            <p><strong>Rider Contact:</strong> {{ $order->deliveryAgent->phone ?? ($order->delivery_man_phone ?? 'N/A') }}</p>
            <p>
                <strong>Delivery Status:</strong>
                <span class="status {{ $order->delivery_status === 'delivered' ? 'active' : 'inactive' }}">
                    {{ ucfirst(str_replace('_', ' ', $order->delivery_status)) }}
                </span>
            </p>
            <p>
                <strong>Payment:</strong>
                <span class="status {{ $order->payment_status === 'paid' ? 'active' : 'inactive' }}">
                    {{ ucfirst($order->payment_status) }}
                </span>
                ({{ strtoupper($order->payment_method ?? 'COD') }})
            </p>
            <p><strong>Delivery Notes:</strong> {{ $order->delivery_notes ?? 'None' }}</p>
            <p><strong>Total Amount:</strong> <strong style="font-size: 18px; color: #27ae60;">৳{{ number_format($order->total_amount, 2) }}</strong></p>
        </div>

    </div>

    <div class="table-container">

        <h2>Order Items</h2>

        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Unit Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($order->items as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->product->name ?? 'Product Removed' }}</strong>
                        </td>
                        <td>৳{{ number_format($item->price, 2) }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>৳{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="empty-message">No item details found for this order.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>

    <a
        class="back-link"
        href="{{ route('order.index') }}"
    >
        ← Back to Orders
    </a>

</div>

@endsection