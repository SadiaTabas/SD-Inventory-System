@extends('layouts.app')

@section('title', 'Orders')

@section('content')

<div class="container">

    <h1>Order Management</h1>

    @if (session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
    @endif

    <div class="top-actions">
        <a
            href="{{ route('order.create') }}"
            class="create-button"
        >
            + Create New Order
        </a>
    </div>

    <div class="table-container">

        <table>

            <thead>
                <tr>
                    <th>Order No</th>
                    <th>Customer</th>
                    <th>Rider</th>
                    <th>Total</th>
                    <th>Paid</th>
                    <th>Due</th>
                    <th>Payment</th>
                    <th>Delivery Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($orders as $order)

                    <tr>

                        <td>
                            <strong>{{ $order->order_number ?? ('#' . $order->id) }}</strong>
                            <br>
                            <small class="text-muted">{{ $order->created_at->format('d M Y, h:i A') }}</small>
                        </td>

                        <td>
                            <strong>{{ $order->user->name ?? ($order->customer->name ?? 'User Deleted') }}</strong>
                            <br>
                            <small>{{ $order->user->phone ?? ($order->customer->phone ?? '') }}</small>
                        </td>

                        <td>
                            {{ $order->deliveryAgent->name ?? ($order->delivery_man_name ?? 'Unassigned') }}
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
                            <span class="status {{ $order->payment_status === 'paid' ? 'active' : 'inactive' }}">
                                {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}
                            </span>
                            <br>
                            <small>{{ strtoupper($order->payment_method ?? 'COD') }}</small>
                        </td>

                        <td>
                            <span class="status {{ $order->delivery_status === 'delivered' ? 'active' : 'inactive' }}">
                                {{ ucfirst(str_replace('_', ' ', $order->delivery_status)) }}
                            </span>
                        </td>

                        <td>

                            <div class="action-buttons" style="display: flex; gap: 6px; align-items: center;">

                                <a
                                    class="view-button"
                                    href="{{ route('order.show', $order) }}"
                                >
                                    View
                                </a>

                                <a
                                    class="view-button"
                                    href="{{ route('order.invoice', $order) }}"
                                    style="background-color: #27ae60;"
                                    title="Download PDF Invoice"
                                >
                                    PDF
                                </a>

                                <a
                                    class="edit-button"
                                    href="{{ route('order.edit', $order) }}"
                                >
                                    Edit
                                </a>

                                <form
                                    class="delete-form"
                                    method="POST"
                                    action="{{ route('order.destroy', $order) }}"
                                    onsubmit="return confirm('Are you sure you want to delete this order? Stock will be restored.');"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        class="delete-button"
                                        type="submit"
                                    >
                                        Delete
                                    </button>
                                </form>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="9" class="empty-message" style="text-align: center; padding: 20px;">
                            No orders found.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection