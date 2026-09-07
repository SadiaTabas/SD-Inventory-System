@extends('layouts.app')

@section('title', 'Edit Order')

@section('content')

 
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<style>
    .ts-control {
        border-radius: 6px !important;
        padding: 10px 12px !important;
        border: 1px solid #cbd5e1 !important;
        font-size: 14px !important;
    }
    .ts-dropdown {
        border-radius: 6px !important;
        border: 1px solid #cbd5e1 !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
    }
    .ts-dropdown .active {
        background-color: #3b82f6 !important;
        color: white !important;
    }
</style>

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

    {{-- Order Financial Quick Info --}}
    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
        @php
            $deliveryCharge = (float) (
                $order->delivery_charge 
                ?? ($order->total_amount - ($order->subtotal_amount ?? 0) + ($order->discount_amount ?? 0))
            );
            if ($deliveryCharge <= 0 && ((float)$order->total_amount > (float)($order->subtotal_amount ?? 0))) {
                $deliveryCharge = (float)$order->total_amount - (float)($order->subtotal_amount ?? 0) + (float)($order->discount_amount ?? 0);
            }
        @endphp

        @if ($deliveryCharge > 0)
            <div style="display: flex; justify-content: space-between; margin-bottom: 6px; color: #0284c7;">
                <span>Delivery Charge:</span>
                <strong>+ ৳{{ number_format($deliveryCharge, 2) }}</strong>
            </div>
        @endif

        <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
            <span>Grand Total:</span>
            <strong>৳{{ number_format($order->total_amount, 2) }}</strong>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 6px; color: #27ae60;">
            <span>Total Paid:</span>
            <strong>৳{{ number_format($order->paid_amount ?? 0, 2) }}</strong>
        </div>
        <div style="display: flex; justify-content: space-between; color: #e74c3c;">
            <span>Current Due:</span>
            <strong>৳{{ number_format($order->due_amount ?? 0, 2) }}</strong>
        </div>
    </div>

    <form
        class="form-container"
        method="POST"
        action="{{ route('order.update', $order) }}"
    >
        @csrf
        @method('PUT')

        {{-- Order Status --}}
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

        {{-- Payment Status --}}
        <div class="form-group">
            <label for="payment_status">Payment Status</label>
            <select id="payment_status" name="payment_status" required>
                <option value="pending" {{ old('payment_status', $order->payment_status) == 'pending' ? 'selected' : '' }}>Pending / Unpaid</option>
                <option value="partially_paid" {{ old('payment_status', $order->payment_status) == 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                <option value="paid" {{ old('payment_status', $order->payment_status) == 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="failed" {{ old('payment_status', $order->payment_status) == 'failed' ? 'selected' : '' }}>Failed</option>
            </select>
        </div>

        {{-- Payment Method --}}
        <div class="form-group">
            <label for="payment_method">Payment Method</label>
            <select id="payment_method" name="payment_method">
                <option value="cod" {{ old('payment_method', $order->payment_method) == 'cod' ? 'selected' : '' }}>Cash on Delivery (COD)</option>
                <option value="bkash" {{ old('payment_method', $order->payment_method) == 'bkash' ? 'selected' : '' }}>bKash</option>
                <option value="nagad" {{ old('payment_method', $order->payment_method) == 'nagad' ? 'selected' : '' }}>Nagad</option>
                <option value="card" {{ old('payment_method', $order->payment_method) == 'card' ? 'selected' : '' }}>Card</option>
            </select>
        </div>

        {{-- Delivery Status --}}
        <div class="form-group">
            <label for="delivery_status">Delivery Status</label>
            <select id="delivery_status" name="delivery_status" required>
                <option value="pending" {{ old('delivery_status', $order->delivery_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="processing" {{ old('delivery_status', $order->delivery_status) == 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="shipped" {{ old('delivery_status', $order->delivery_status) == 'shipped' ? 'selected' : '' }}>Shipped / Dispatched</option>
                <option value="out_for_delivery" {{ old('delivery_status', $order->delivery_status) == 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                <option value="delivered" {{ old('delivery_status', $order->delivery_status) == 'delivered' ? 'selected' : '' }}>Delivered</option>
                <option value="returned" {{ old('delivery_status', $order->delivery_status) == 'returned' ? 'selected' : '' }}>Returned</option>
            </select>
        </div>

        {{-- Assign Rider / Delivery Agent (Searchable) --}}
        <div class="form-group">
            <label for="delivery_agent_id">Assign Rider / Agent</label>
            <select id="delivery_agent_id" name="delivery_agent_id" placeholder="Search rider...">
                <option value="">-- None / Unassigned --</option>
                @foreach ($deliveryAgents as $agent)
                    <option
                        value="{{ $agent->id }}"
                        {{ old('delivery_agent_id', $order->delivery_agent_id) == $agent->id ? 'selected' : '' }}
                    >
                        {{ $agent->name }} ({{ ucfirst($agent->vehicle_type ?? 'Rider') }} - {{ $agent->phone }})
                    </option>
                @endforeach
            </select>
        </div>

        
        <div class="form-group">
            <label for="delivery_slot">Delivery Slot</label>
            <select id="delivery_slot" name="delivery_slot" placeholder="Search or select slot...">
                <option value="">Select or type delivery slot...</option>
                <option value="Morning (9:00 AM - 12:00 PM)" {{ old('delivery_slot', $order->delivery_slot) == 'Morning (9:00 AM - 12:00 PM)' ? 'selected' : '' }}>Morning (9:00 AM - 12:00 PM)</option>
                <option value="Afternoon (12:00 PM - 3:00 PM)" {{ old('delivery_slot', $order->delivery_slot) == 'Afternoon (12:00 PM - 3:00 PM)' ? 'selected' : '' }}>Afternoon (12:00 PM - 3:00 PM)</option>
                <option value="Evening (3:00 PM - 6:00 PM)" {{ old('delivery_slot', $order->delivery_slot) == 'Evening (3:00 PM - 6:00 PM)' ? 'selected' : '' }}>Evening (3:00 PM - 6:00 PM)</option>
                <option value="Night (6:00 PM - 9:00 PM)" {{ old('delivery_slot', $order->delivery_slot) == 'Night (6:00 PM - 9:00 PM)' ? 'selected' : '' }}>Night (6:00 PM - 9:00 PM)</option>
                <option value="Express (Within 2 Hours)" {{ old('delivery_slot', $order->delivery_slot) == 'Express (Within 2 Hours)' ? 'selected' : '' }}>Express (Within 2 Hours)</option>
                @if($order->delivery_slot && !in_array($order->delivery_slot, [
                    'Morning (9:00 AM - 12:00 PM)',
                    'Afternoon (12:00 PM - 3:00 PM)',
                    'Evening (3:00 PM - 6:00 PM)',
                    'Night (6:00 PM - 9:00 PM)',
                    'Express (Within 2 Hours)'
                ]))
                    <option value="{{ $order->delivery_slot }}" selected>{{ $order->delivery_slot }}</option>
                @endif
            </select>
        </div>

        {{-- Shipping Address --}}
        <div class="form-group">
            <label for="shipping_address">Shipping Address</label>
            <textarea
                id="shipping_address"
                name="shipping_address"
                required
            >{{ old('shipping_address', $order->shipping_address) }}</textarea>
        </div>

        {{-- Delivery Notes --}}
        <div class="form-group">
            <label for="delivery_notes">Delivery Notes / Instructions</label>
            <textarea
                id="delivery_notes"
                name="delivery_notes"
                placeholder="Special delivery instructions"
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

 
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    
    new TomSelect('#delivery_agent_id', {
        create: false
    });

    // Initialize Delivery Slot searchable select 
    new TomSelect('#delivery_slot', {
        create: true,
        persist: false
    });
});
</script>

@endsection