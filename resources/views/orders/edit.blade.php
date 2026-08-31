@extends('layouts.app')

@section('title', 'Edit Order')

@section('content')

<div class="container">

    <h1>Edit Order: {{ $order->order_number ?? ('#' . $order->id) }}</h1>

    @if ($errors->any())
        <div class="error-box">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        class="form-container"
        method="POST"
        action="{{ route('order.update', $order) }}"
    >
        @csrf
        @method('PUT')

        
        <div class="form-group">
            <label for="status">Order Status</label>
            <select id="status" name="status" required>
                <option value="pending" {{ old('status', $order->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="processing" {{ old('status', $order->status) == 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="out_for_delivery" {{ old('status', $order->status) == 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                <option value="delivered" {{ old('status', $order->status) == 'delivered' ? 'selected' : '' }}>Delivered</option>
                <option value="cancelled" {{ old('status', $order->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>

       
        <div class="form-group">
            <label for="payment_status">Payment Status</label>
            <select id="payment_status" name="payment_status" required>
                <option value="pending" {{ old('payment_status', $order->payment_status) == 'pending' ? 'selected' : '' }}>Pending / Unpaid</option>
                <option value="paid" {{ old('payment_status', $order->payment_status) == 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="failed" {{ old('payment_status', $order->payment_status) == 'failed' ? 'selected' : '' }}>Failed</option>
            </select>
        </div>

        
        <div class="form-group">
            <label for="payment_method">Payment Method</label>
            <input
                type="text"
                id="payment_method"
                name="payment_method"
                value="{{ old('payment_method', $order->payment_method) }}"
            >
        </div>

         
        <div class="form-group">
            <label for="delivery_status">Delivery Status</label>
            <select id="delivery_status" name="delivery_status" required>
                <option value="pending" {{ old('delivery_status', $order->delivery_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="processing" {{ old('delivery_status', $order->delivery_status) == 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="shipped" {{ old('delivery_status', $order->delivery_status) == 'shipped' ? 'selected' : '' }}>Shipped / Dispatched</option>
                <option value="delivered" {{ old('delivery_status', $order->delivery_status) == 'delivered' ? 'selected' : '' }}>Delivered</option>
                <option value="returned" {{ old('delivery_status', $order->delivery_status) == 'returned' ? 'selected' : '' }}>Returned</option>
            </select>
        </div>

        
        <div class="form-group">
            <label for="delivery_agent_id">Assign Rider / Agent</label>
            <select id="delivery_agent_id" name="delivery_agent_id">
                <option value="">-- None / Unassigned --</option>
                @foreach ($deliveryAgents as $agent)
                    <option
                        value="{{ $agent->id }}"
                        {{ old('delivery_agent_id', $order->delivery_agent_id) == $agent->id ? 'selected' : '' }}
                    >
                        {{ $agent->name }} ({{ $agent->phone }})
                    </option>
                @endforeach
            </select>
        </div>

       
        <div class="form-group">
            <label for="delivery_slot">Delivery Slot</label>
            <input
                type="text"
                id="delivery_slot"
                name="delivery_slot"
                value="{{ old('delivery_slot', $order->delivery_slot) }}"
            >
        </div>

        
        <div class="form-group">
            <label for="shipping_address">Shipping Address</label>
            <textarea
                id="shipping_address"
                name="shipping_address"
                required
            >{{ old('shipping_address', $order->shipping_address) }}</textarea>
        </div>

        
        <div class="form-group">
            <label for="delivery_notes">Delivery Notes</label>
            <textarea
                id="delivery_notes"
                name="delivery_notes"
            >{{ old('delivery_notes', $order->delivery_notes) }}</textarea>
        </div>

        <button
            type="submit"
            class="primary-button"
        >
            Update Order
        </button>

    </form>

    <a
        class="back-link"
        href="{{ route('order.index') }}"
    >
        ← Back to Orders
    </a>

</div>

@endsection