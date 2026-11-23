@extends('layouts.app')

@section('title', 'Add Supplier')

@section('content')
<div class="container">
    <h1>Add New Supplier</h1>

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
        <form action="{{ route('suppliers.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Supplier Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required>
            </div>

            <div class="form-group">
                <label>Company Name</label>
                <input type="text" name="company_name" value="{{ old('company_name') }}">
            </div>

            <div class="form-group">
                <label>Contact Person</label>
                <input type="text" name="contact_person" value="{{ old('contact_person') }}">
            </div>

            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" value="{{ old('phone') }}">
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}">
            </div>

            <div class="form-group">
                <label>Address</label>
                <textarea name="address">{{ old('address') }}</textarea>
            </div>

            <div class="form-group">
                <label>Status *</label>
                <select name="status">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>

            <div class="form-group">
                <label>Internal Notes</label>
                <textarea name="notes" placeholder="Payment terms, delivery cycles...">{{ old('notes') }}</textarea>
            </div>

            <button type="submit" class="primary-button">Save Supplier</button>
        </form>
    </div>

    <a href="{{ route('suppliers.index') }}" class="back-link">← Back to Suppliers</a>
</div>
@endsection
