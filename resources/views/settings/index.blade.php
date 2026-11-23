@extends('layouts.app')

@section('title', 'Business & Store Settings')

@section('content')
<div class="container" style="max-width: 1100px;">

    <div style="margin-bottom: 24px;">
        <h1 style="text-align: left; margin: 0;">Business &amp; E-Commerce Settings</h1>
        <p style="color: #6b7280; font-size: 14px; margin-top: 4px;">Configure store identity, regional currencies, delivery fees, and automated payment gateways.</p>
    </div>

    @if (session('success'))
        <div class="success-message">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="error-box">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-container" style="background: #ffffff; padding: 30px; border-radius: 8px; border: 1px solid #e2e8f0;">
        <form action="{{ route('settings.update') }}" method="POST">
            @csrf

            <!-- Section 1: Business Profile -->
            <h2 style="font-size: 18px; color: #1e293b; border-bottom: 2px solid #f1f5f9; padding-bottom: 8px; margin-bottom: 20px;">
                1. Store Identity &amp; Contact Info
            </h2>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>Store / Business Name *</label>
                    <input type="text" name="business_name" value="{{ old('business_name', $settings['business_name'] ?? 'SD Inventory Store') }}" required>
                </div>
                <div class="form-group">
                    <label>Support Email Address</label>
                    <input type="email" name="business_email" value="{{ old('business_email', $settings['business_email'] ?? 'support@sdinventory.com') }}">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>Support Phone / Hotline</label>
                    <input type="text" name="business_phone" value="{{ old('business_phone', $settings['business_phone'] ?? '+880 1700-000000') }}">
                </div>
                <div class="form-group">
                    <label>Store Physical Address</label>
                    <input type="text" name="business_address" value="{{ old('business_address', $settings['business_address'] ?? 'Dhaka, Bangladesh') }}">
                </div>
            </div>

            <!-- Section 2: Currency & Shipping -->
            <h2 style="font-size: 18px; color: #1e293b; border-bottom: 2px solid #f1f5f9; padding-bottom: 8px; margin: 30px 0 20px;">
                2. Currency &amp; Delivery Settings
            </h2>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>Currency Symbol *</label>
                    <input type="text" name="currency_symbol" value="{{ old('currency_symbol', $settings['currency_symbol'] ?? '৳') }}" required>
                </div>
                <div class="form-group">
                    <label>Currency Code *</label>
                    <input type="text" name="currency_code" value="{{ old('currency_code', $settings['currency_code'] ?? 'BDT') }}" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>Default Delivery Charge (৳) *</label>
                    <input type="number" step="0.01" name="default_delivery_charge" value="{{ old('default_delivery_charge', $settings['default_delivery_charge'] ?? '60') }}" required>
                </div>
                <div class="form-group">
                    <label>Free Delivery Minimum (৳)</label>
                    <input type="number" step="0.01" name="free_delivery_threshold" value="{{ old('free_delivery_threshold', $settings['free_delivery_threshold'] ?? '1500') }}" placeholder="Leave blank to disable">
                </div>
                <div class="form-group">
                    <label>Minimum Order Amount (৳)</label>
                    <input type="number" step="0.01" name="min_order_amount" value="{{ old('min_order_amount', $settings['min_order_amount'] ?? '0') }}">
                </div>
            </div>

            <!-- Section 3: Payment Gateways -->
            <h2 style="font-size: 18px; color: #1e293b; border-bottom: 2px solid #f1f5f9; padding-bottom: 8px; margin: 30px 0 20px;">
                3. Payment Methods &amp; Gateways
            </h2>

            <div style="background: #f8fafc; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                <div style="margin-bottom: 16px;">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-weight: 700; color: #1e293b;">
                        <input type="checkbox" name="enable_cod" value="1" {{ old('enable_cod', $settings['enable_cod'] ?? 1) ? 'checked' : '' }} style="width: 18px; height: 18px;">
                        Enable Cash on Delivery (COD)
                    </label>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 16px;">
                    <div>
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-weight: 700; color: #1e293b; margin-bottom: 8px;">
                            <input type="checkbox" name="enable_bkash" value="1" {{ old('enable_bkash', $settings['enable_bkash'] ?? 1) ? 'checked' : '' }} style="width: 18px; height: 18px;">
                            Enable bKash Payment
                        </label>
                        <div class="form-group">
                            <label>bKash Merchant / Personal Number</label>
                            <input type="text" name="bkash_number" value="{{ old('bkash_number', $settings['bkash_number'] ?? '01700000000') }}" placeholder="017xxxxxxxx">
                        </div>
                    </div>

                    <div>
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-weight: 700; color: #1e293b; margin-bottom: 8px;">
                            <input type="checkbox" name="enable_nagad" value="1" {{ old('enable_nagad', $settings['enable_nagad'] ?? 1) ? 'checked' : '' }} style="width: 18px; height: 18px;">
                            Enable Nagad Payment
                        </label>
                        <div class="form-group">
                            <label>Nagad Merchant / Personal Number</label>
                            <input type="text" name="nagad_number" value="{{ old('nagad_number', $settings['nagad_number'] ?? '01800000000') }}" placeholder="018xxxxxxxx">
                        </div>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label>Customer Payment Instructions (shown on checkout)</label>
                    <textarea name="payment_instructions" rows="3" placeholder="Instructions for bKash / Nagad send money / cash on delivery">{{ old('payment_instructions', $settings['payment_instructions'] ?? 'Please send money to our personal bKash/Nagad number and enter the transaction ID. For COD, pay cash to the rider.') }}</textarea>
                </div>
            </div>

            <div style="margin-top: 30px;">
                <button type="submit" class="submit-button" style="background: #2563eb; color: #fff; padding: 12px 28px; border: none; border-radius: 6px; font-weight: 700; font-size: 15px; cursor: pointer;">
                    Save All Settings
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
