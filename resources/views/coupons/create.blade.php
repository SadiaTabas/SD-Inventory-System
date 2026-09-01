@extends('layouts.app')

@section('title', 'Create Coupon')

@section('content')

<div class="container">

    <h1>Create New Promo Coupon</h1>

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
        action="{{ route('coupons.store') }}"
    >
        @csrf

        {{-- Coupon Code --}}
        <div class="form-group">
            <label for="code">Coupon Code (e.g. SUMMER20, WELCOME50)</label>
            <input
                type="text"
                id="code"
                name="code"
                placeholder="PROMOCODE"
                value="{{ old('code') }}"
                required
                style="text-transform: uppercase;"
            >
        </div>

        {{-- Discount Type --}}
        <div class="form-group">
            <label for="type">Discount Type</label>
            <select id="type" name="type" required onchange="updateValueLabel(this.value)">
                <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Fixed Amount (৳)</option>
                <option value="percent" {{ old('type') == 'percent' ? 'selected' : '' }}>Percentage (%)</option>
            </select>
        </div>

        {{-- Discount Value --}}
        <div class="form-group">
            <label for="value" id="value-label">Discount Value (৳)</label>
            <input
                type="number"
                step="0.01"
                min="0.01"
                id="value"
                name="value"
                placeholder="e.g. 50 or 10"
                value="{{ old('value') }}"
                required
            >
        </div>

        {{-- Minimum Order Amount --}}
        <div class="form-group">
            <label for="min_order_amount">Minimum Order Amount (৳)</label>
            <input
                type="number"
                step="0.01"
                min="0"
                id="min_order_amount"
                name="min_order_amount"
                placeholder="0.00 (Minimum cart total required)"
                value="{{ old('min_order_amount', '0.00') }}"
            >
        </div>

        {{-- Status --}}
        <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status" required>
                <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive / Expired</option>
            </select>
        </div>

        <button
            type="submit"
            class="primary-button"
        >
            Save Coupon
        </button>

    </form>

    <a
        class="back-link"
        href="{{ route('coupons.index') }}"
    >
        ← Back to Coupons
    </a>

</div>

<script>
function updateValueLabel(type) {
    const label = document.getElementById('value-label');
    if (type === 'percent') {
        label.innerText = 'Discount Value (%)';
    } else {
        label.innerText = 'Discount Value (৳)';
    }
}
</script>

@endsection