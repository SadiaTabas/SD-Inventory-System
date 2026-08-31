@extends('layouts.app')

@section('title', 'Create Order')

@section('content')

<div class="container">

    <h1>Create New Order</h1>

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
        action="{{ route('order.store') }}"
    >
        @csrf

        {{-- Customer Selection --}}
        <div class="form-group">
            <label for="user_id">Customer</label>
            <select
                id="user_id"
                name="user_id"
                required
                onchange="loadCustomerAddress(this)"
            >
                <option value="">Select Customer</option>
                @foreach ($customers as $customer)
                    <option
                        value="{{ $customer->id }}"
                        data-addresses='@json($customer->addresses)'
                        {{ old('user_id') == $customer->id ? 'selected' : '' }}
                    >
                        {{ $customer->name }} - {{ $customer->email }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Product Selection --}}
        <div class="form-group">
            <label for="product_id">Product</label>
            <select
                id="product_id"
                name="product_id"
                required
            >
                <option value="">Select Product</option>
                @foreach ($products as $product)
                    <option
                        value="{{ $product->id }}"
                        {{ old('product_id') == $product->id ? 'selected' : '' }}
                    >
                        {{ $product->name }} - ৳{{ number_format($product->price, 2) }} (Stock: {{ $product->qty }})
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Quantity --}}
        <div class="form-group">
            <label for="quantity">Quantity</label>
            <input
                type="number"
                id="quantity"
                name="quantity"
                value="{{ old('quantity', 1) }}"
                min="1"
                required
            >
        </div>

        {{-- Payment Method --}}
        <div class="form-group">
            <label for="payment_method">Payment Method</label>
            <select id="payment_method" name="payment_method">
                <option value="cod" {{ old('payment_method') == 'cod' ? 'selected' : '' }}>Cash on Delivery (COD)</option>
                <option value="bkash" {{ old('payment_method') == 'bkash' ? 'selected' : '' }}>bKash</option>
                <option value="nagad" {{ old('payment_method') == 'nagad' ? 'selected' : '' }}>Nagad</option>
                <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Card</option>
            </select>
        </div>

        {{-- Assign Delivery Agent (Rider) --}}
        <div class="form-group">
            <label for="delivery_agent_id">Assign Delivery Rider (Optional)</label>
            <select id="delivery_agent_id" name="delivery_agent_id">
                <option value="">-- Assign Later --</option>
                @foreach ($deliveryAgents as $agent)
                    <option value="{{ $agent->id }}" {{ old('delivery_agent_id') == $agent->id ? 'selected' : '' }}>
                        {{ $agent->name }} ({{ ucfirst($agent->vehicle_type) }} - {{ $agent->phone }})
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Delivery Slot --}}
        <div class="form-group">
            <label for="delivery_slot">Delivery Slot</label>
            <input
                type="text"
                id="delivery_slot"
                name="delivery_slot"
                placeholder="e.g., Morning (9 AM - 12 PM)"
                value="{{ old('delivery_slot') }}"
            >
        </div>

        {{-- Shipping Address --}}
        <div class="form-group">
            <label for="shipping_address">Shipping Address</label>
            <textarea
                id="shipping_address"
                name="shipping_address"
                placeholder="Enter complete shipping address"
                required
            >{{ old('shipping_address') }}</textarea>
        </div>

        {{-- Delivery Notes --}}
        <div class="form-group">
            <label for="delivery_notes">Delivery Notes / Instructions</label>
            <textarea
                id="delivery_notes"
                name="delivery_notes"
                placeholder="Special delivery notes"
            >{{ old('delivery_notes') }}</textarea>
        </div>

        <button
            type="submit"
            class="primary-button"
        >
            Create Order
        </button>

    </form>

    <a
        class="back-link"
        href="{{ route('order.index') }}"
    >
        ← Back to Orders
    </a>

</div>

<script>
function loadCustomerAddress(select) {
    const selectedOption = select.options[select.selectedIndex];
    const rawData = selectedOption.getAttribute('data-addresses');
    const addressField = document.getElementById('shipping_address');

    if (rawData) {
        try {
            const addresses = JSON.parse(rawData);
            if (addresses.length > 0) {
                const defaultAddr = addresses.find(a => a.is_default) || addresses[0];
                addressField.value = defaultAddr.address + ', ' + defaultAddr.city;
            }
        } catch (e) {
            console.error('Error parsing address data', e);
        }
    }
}
</script>

@endsection