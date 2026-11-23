@extends('layouts.app')

@section('title', 'Register Delivery Rider')

@section('content')
<div class="container" style="max-width: 800px;">
    <h1>Register New Delivery Rider</h1>

    @if ($errors->any())
        <div class="error-box">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-container">
        <form action="{{ route('deliveries.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Rider Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Full Name" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label>Phone Number *</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="017xxxxxxxx" required>
                </div>
                <div class="form-group">
                    <label>Emergency Contact</label>
                    <input type="text" name="emergency_contact" value="{{ old('emergency_contact') }}" placeholder="018xxxxxxxx">
                </div>
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="rider@example.com">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label>Vehicle Type</label>
                    <select name="vehicle_type">
                        <option value="Motorcycle" {{ old('vehicle_type') === 'Motorcycle' ? 'selected' : '' }}>Motorcycle</option>
                        <option value="Bicycle" {{ old('vehicle_type') === 'Bicycle' ? 'selected' : '' }}>Bicycle</option>
                        <option value="Van" {{ old('vehicle_type') === 'Van' ? 'selected' : '' }}>Delivery Van</option>
                        <option value="Walking" {{ old('vehicle_type') === 'Walking' ? 'selected' : '' }}>On Foot</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Vehicle / Registration Number</label>
                    <input type="text" name="vehicle_number" value="{{ old('vehicle_number') }}" placeholder="Dhaka Metro HA-1234">
                </div>
            </div>

            <div class="form-group">
                <label>Residential / Operating Address</label>
                <textarea name="address" rows="3" placeholder="Rider permanent or present address">{{ old('address') }}</textarea>
            </div>

            <div class="form-group">
                <label>Initial Status *</label>
                <select name="status" required>
                    <option value="available" {{ old('status') === 'available' ? 'selected' : '' }}>Available for Deliveries</option>
                    <option value="busy" {{ old('status') === 'busy' ? 'selected' : '' }}>Busy</option>
                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive / Off Duty</option>
                </select>
            </div>

            <div class="button-group" style="display: flex; gap: 12px; margin-top: 24px;">
                <button type="submit" class="submit-button" style="background: #2563eb; color: #fff; padding: 10px 20px; border: none; border-radius: 6px; font-weight: 700; cursor: pointer;">Register Rider</button>
                <a href="{{ route('deliveries.index') }}" class="cancel-button" style="background: #e2e8f0; color: #334155; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center;">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
