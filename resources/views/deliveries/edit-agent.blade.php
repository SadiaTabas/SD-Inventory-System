@extends('layouts.app')

@section('title', 'Edit Delivery Rider')

@section('content')
<div class="container" style="max-width: 800px;">
    <h1>Edit Delivery Rider: {{ $agent->name }}</h1>

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
        <form action="{{ route('deliveries.update', $agent) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Rider Name *</label>
                <input type="text" name="name" value="{{ old('name', $agent->name) }}" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label>Phone Number *</label>
                    <input type="text" name="phone" value="{{ old('phone', $agent->phone) }}" required>
                </div>
                <div class="form-group">
                    <label>Emergency Contact</label>
                    <input type="text" name="emergency_contact" value="{{ old('emergency_contact', $agent->emergency_contact) }}">
                </div>
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" value="{{ old('email', $agent->email) }}">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label>Vehicle Type</label>
                    <select name="vehicle_type">
                        <option value="Motorcycle" {{ old('vehicle_type', $agent->vehicle_type) === 'Motorcycle' ? 'selected' : '' }}>Motorcycle</option>
                        <option value="Bicycle" {{ old('vehicle_type', $agent->vehicle_type) === 'Bicycle' ? 'selected' : '' }}>Bicycle</option>
                        <option value="Van" {{ old('vehicle_type', $agent->vehicle_type) === 'Van' ? 'selected' : '' }}>Delivery Van</option>
                        <option value="Walking" {{ old('vehicle_type', $agent->vehicle_type) === 'Walking' ? 'selected' : '' }}>On Foot</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Vehicle / Registration Number</label>
                    <input type="text" name="vehicle_number" value="{{ old('vehicle_number', $agent->vehicle_number) }}">
                </div>
            </div>

            <div class="form-group">
                <label>Residential / Operating Address</label>
                <textarea name="address" rows="3">{{ old('address', $agent->address) }}</textarea>
            </div>

            <div class="form-group">
                <label>Status *</label>
                <select name="status" required>
                    <option value="available" {{ old('status', $agent->status) === 'available' ? 'selected' : '' }}>Available for Deliveries</option>
                    <option value="busy" {{ old('status', $agent->status) === 'busy' ? 'selected' : '' }}>Busy</option>
                    <option value="inactive" {{ old('status', $agent->status) === 'inactive' ? 'selected' : '' }}>Inactive / Off Duty</option>
                </select>
            </div>

            <div class="button-group" style="display: flex; gap: 12px; margin-top: 24px;">
                <button type="submit" class="submit-button" style="background: #2563eb; color: #fff; padding: 10px 20px; border: none; border-radius: 6px; font-weight: 700; cursor: pointer;">Update Rider</button>
                <a href="{{ route('deliveries.index') }}" class="cancel-button" style="background: #e2e8f0; color: #334155; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center;">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
