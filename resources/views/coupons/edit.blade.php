@extends('layouts.app')

@section('title', 'Edit Coupon')

@section('content')

<div class="container">

    <h1>Edit Coupon: {{ $coupon->code }}</h1>

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
        action="{{ route('coupons.update', $coupon) }}"
    >
        @csrf
        @method('PUT')

        {{-- Coupon Code --}}
        <div class="form-group">
            <label for="code">Coupon Code</label>
            <input
                type="text"
                id="code"
                name="code"
                value="{{ old('code', $coupon->code) }}"
                required
                style="text-transform: uppercase;"
            >
        </div>

        {{-- Discount Type --}}
        <div class="form-group">
            <label for="type">Discount Type</label>
            <select id="type" name="type" required onchange="updateValueLabel(this.value)">
                <option value="fixed" {{ old('type', $coupon->type) == 'fixed' ? 'selected' : '' }}>Fixed Amount (৳)</option>
                <option value="percent" {{ old('type', $coupon->type) == 'percent' ? 'selected' : '' }}>Percentage (%)</option>
            </select>
        </div>

        {{-- Discount Value --}}
        <div class="form-group">
            <label for="value" id="value-label">Discount Value ({{ $coupon->type === 'percent' ? '%' : '৳' }})</label>
            <input
                type="number"
                step="0.01"
                min="0.01"
                id="value"
                name="value"
                value="{{ old('value', $coupon->value) }}"
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
                value="{{ old('min_order_amount', $coupon->min_order_amount) }}"
            >
        </div>

        {{-- Status --}}
        <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status" required>
                <option value="1" {{ old('status', $coupon->status) == '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ old('status', $coupon->status) == '0' ? 'selected' : '' }}>Inactive / Expired</option>
            </select>
        </div>

        <button
            type="submit"
            class="primary-button"
        >
            Update Coupon
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