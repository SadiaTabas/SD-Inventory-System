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

    <a
        href="{{ route('order.create') }}"
        class="create-button"
    >
        + Create New Order
    </a>

    <div class="table-container">

        <table>

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Order Status</th>
                    <th>Payment</th>
                    <th>Delivery</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($orders as $order)

                    <tr>

                        <td>
                            {{ $order->id }}
                        </td>

                        <td>
                            <strong>
                                {{ $order->customer->name }}
                            </strong>
                        </td>

                        <td>
                            ৳{{ number_format($order->total_amount, 2) }}
                        </td>

                        <td>
                            {{ ucfirst($order->status) }}
                        </td>

                        <td>
                            {{ ucfirst($order->payment_status) }}
                        </td>

                        <td>
                            {{ ucfirst($order->delivery_status) }}
                        </td>

                        <td>

                            <div class="action-buttons">

                                <a
                                    class="edit-button"
                                    href="{{ route('order.show', $order) }}"
                                >
                                    View
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
                                    onsubmit="return confirm('Are you sure you want to delete this order?');"
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
                        <td colspan="7" class="empty-message">
                            No orders found.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection