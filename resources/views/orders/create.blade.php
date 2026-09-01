@extends('layouts.app')

@section('title', 'Create Order')

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

        {{-- Searchable Customer Selection --}}
        <div class="form-group">
            <label for="user_id">Customer</label>
            <select
                id="user_id"
                name="user_id"
                placeholder="Search or select customer..."
                required
            >
                <option value="">Search customer by name or email...</option>
                @foreach ($customers as $customer)
                    <option
                        value="{{ $customer->id }}"
                        data-addresses='@json($customer->addresses)'
                        {{ old('user_id') == $customer->id ? 'selected' : '' }}
                    >
                        {{ $customer->name }} ({{ $customer->email }})
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Searchable Product Selection --}}
        <div class="form-group">
            <label for="product_id">Product</label>
            <select
                id="product_id"
                name="product_id"
                placeholder="Search product by name..."
                required
            >
                <option value="">Search and select product...</option>
                @foreach ($products as $product)
                    <option
                        value="{{ $product->id }}"
                        data-price="{{ $product->price }}"
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
                oninput="calculateOrderSummary()"
            >
        </div>

        {{-- Searchable Coupon / Offer Selection --}}
        <div class="form-group">
            <label for="coupon_id">Apply Coupon / Offer (Optional)</label>
            <select
                id="coupon_id"
                name="coupon_id"
                placeholder="Search promo code..."
            >
                <option value="" data-type="none" data-value="0" data-min="0">-- No Coupon Selected --</option>
                @if(isset($coupons))
                    @foreach ($coupons as $coupon)
                        <option
                            value="{{ $coupon->id }}"
                            data-type="{{ $coupon->type }}"
                            data-value="{{ $coupon->value }}"
                            data-min="{{ $coupon->min_order_amount }}"
                            {{ old('coupon_id') == $coupon->id ? 'selected' : '' }}
                        >
                            {{ $coupon->code }} - ({{ $coupon->type === 'percent' ? $coupon->value . '%' : '৳' . $coupon->value }} OFF, Min Order: ৳{{ $coupon->min_order_amount }})
                        </option>
                    @endforeach
                @endif
            </select>
        </div>

        {{-- Advance / Partial Payment Inputs --}}
        <div style="background: #f1f5f9; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #e2e8f0;">
            <div class="form-group" style="margin-bottom: 12px;">
                <label for="advance_payment">Advance / Initial Paid Amount (৳)</label>
                <input
                    type="number"
                    step="0.01"
                    min="0"
                    id="advance_payment"
                    name="advance_payment"
                    placeholder="0.00 (Optional advance payment)"
                    value="{{ old('advance_payment', 0) }}"
                    oninput="calculateOrderSummary()"
                >
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label for="transaction_id">Transaction ID / Reference (Optional)</label>
                <input
                    type="text"
                    id="transaction_id"
                    name="transaction_id"
                    placeholder="e.g. TRX1092837 or Cash Receipt #"
                    value="{{ old('transaction_id') }}"
                >
            </div>
        </div>

        {{-- Calculation Summary Box --}}
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                <span>Subtotal:</span>
                <strong id="display_subtotal">৳0.00</strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 6px; color: #e74c3c;">
                <span>Discount Applied:</span>
                <strong id="display_discount">- ৳0.00</strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 6px; font-weight: bold; color: #1e293b;">
                <span>Grand Total:</span>
                <strong id="display_total">৳0.00</strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 6px; color: #10b981;">
                <span>Advance Paid:</span>
                <strong id="display_advance">৳0.00</strong>
            </div>
            <hr style="border: 0; border-top: 1px solid #cbd5e1; margin: 10px 0;">
            <div style="display: flex; justify-content: space-between; font-size: 17px; color: #e11d48;">
                <span><strong>Remaining Due:</strong></span>
                <strong id="display_due">৳0.00</strong>
            </div>
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
            <select id="delivery_agent_id" name="delivery_agent_id" placeholder="Search rider...">
                <option value="">-- Assign Later --</option>
                @foreach ($deliveryAgents as $agent)
                    <option value="{{ $agent->id }}" {{ old('delivery_agent_id') == $agent->id ? 'selected' : '' }}>
                        {{ $agent->name }} ({{ ucfirst($agent->vehicle_type ?? 'Rider') }} - {{ $agent->phone }})
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Searchable Delivery Slot Dropdown --}}
        <div class="form-group">
            <label for="delivery_slot">Delivery Slot</label>
            <select id="delivery_slot" name="delivery_slot" placeholder="Search or select slot...">
                <option value="">Select or type delivery slot...</option>
                <option value="Morning (9:00 AM - 12:00 PM)" {{ old('delivery_slot') == 'Morning (9:00 AM - 12:00 PM)' ? 'selected' : '' }}>Morning (9:00 AM - 12:00 PM)</option>
                <option value="Afternoon (12:00 PM - 3:00 PM)" {{ old('delivery_slot') == 'Afternoon (12:00 PM - 3:00 PM)' ? 'selected' : '' }}>Afternoon (12:00 PM - 3:00 PM)</option>
                <option value="Evening (3:00 PM - 6:00 PM)" {{ old('delivery_slot') == 'Evening (3:00 PM - 6:00 PM)' ? 'selected' : '' }}>Evening (3:00 PM - 6:00 PM)</option>
                <option value="Night (6:00 PM - 9:00 PM)" {{ old('delivery_slot') == 'Night (6:00 PM - 9:00 PM)' ? 'selected' : '' }}>Night (6:00 PM - 9:00 PM)</option>
                <option value="Express (Within 2 Hours)" {{ old('delivery_slot') == 'Express (Within 2 Hours)' ? 'selected' : '' }}>Express (Within 2 Hours)</option>
            </select>
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

 
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Initialize Customer searchable select
    const customerSelect = new TomSelect('#user_id', {
        create: false,
        sortField: { field: "text", direction: "asc" },
        onChange: function(value) {
            if (!value) return;
            const originalOption = document.querySelector(`#user_id option[value="${value}"]`);
            const rawData = originalOption?.getAttribute('data-addresses');
            const addressField = document.getElementById('shipping_address');

            if (rawData) {
                try {
                    const addresses = JSON.parse(rawData);
                    if (addresses.length > 0) {
                        const defaultAddr = addresses.find(a => a.is_default) || addresses[0];
                        const addressLine = defaultAddr.address || defaultAddr.address_line || '';
                        addressField.value = addressLine + (defaultAddr.city ? ', ' + defaultAddr.city : '');
                    }
                } catch (e) {
                    console.error('Error parsing address', e);
                }
            }
        }
    });

    // 2. Initialize Product searchable select
    const productSelect = new TomSelect('#product_id', {
        create: false,
        sortField: { field: "text", direction: "asc" },
        onChange: function() {
            calculateOrderSummary();
        }
    });

    // 3. Initialize Coupon searchable select
    const couponSelect = new TomSelect('#coupon_id', {
        create: false,
        onChange: function() {
            calculateOrderSummary();
        }
    });

    // 4. Initialize Delivery Slot searchable select
    new TomSelect('#delivery_slot', {
        create: true,
        persist: false
    });

    // 5. Initialize Delivery Rider searchable select
    new TomSelect('#delivery_agent_id', {
        create: false
    });

    // Calculation function
    window.calculateOrderSummary = function() {
        const productVal = productSelect.getValue();
        const qty = parseInt(document.getElementById('quantity').value) || 0;
        
        let unitPrice = 0;
        if (productVal) {
            const opt = document.querySelector(`#product_id option[value="${productVal}"]`);
            unitPrice = parseFloat(opt?.getAttribute('data-price') || 0);
        }

        const subtotal = unitPrice * qty;
        let discount = 0;

        const couponVal = couponSelect.getValue();
        if (couponVal) {
            const couponOpt = document.querySelector(`#coupon_id option[value="${couponVal}"]`);
            const couponType = couponOpt?.getAttribute('data-type');
            const couponValue = parseFloat(couponOpt?.getAttribute('data-value') || 0);
            const minOrder = parseFloat(couponOpt?.getAttribute('data-min') || 0);

            if (subtotal >= minOrder) {
                if (couponType === 'percent') {
                    discount = subtotal * (couponValue / 100);
                } else if (couponType === 'fixed') {
                    discount = couponValue;
                }
            }
        }

        discount = Math.min(discount, subtotal);
        const grandTotal = Math.max(0, subtotal - discount);

        const advanceInput = parseFloat(document.getElementById('advance_payment').value) || 0;
        const advancePaid = Math.min(advanceInput, grandTotal);
        const dueBalance = Math.max(0, grandTotal - advancePaid);

        document.getElementById('display_subtotal').innerText = '৳' + subtotal.toFixed(2);
        document.getElementById('display_discount').innerText = '- ৳' + discount.toFixed(2);
        document.getElementById('display_total').innerText = '৳' + grandTotal.toFixed(2);
        document.getElementById('display_advance').innerText = '৳' + advancePaid.toFixed(2);
        document.getElementById('display_due').innerText = '৳' + dueBalance.toFixed(2);
    };

    calculateOrderSummary();
});
</script>

@endsection