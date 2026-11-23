@extends('layouts.app')

@section('title', 'Edit Supplier')

@section('content')
<div class="container">
    <h1>Edit Supplier</h1>

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
        <form action="{{ route('suppliers.update', $supplier) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Supplier Name *</label>
                <input type="text" name="name" value="{{ old('name', $supplier->name) }}" required>
            </div>

            <div class="form-group">
                <label>Company Name</label>
                <input type="text" name="company_name" value="{{ old('company_name', $supplier->company_name) }}">
            </div>

            <div class="form-group">
                <label>Contact Person</label>
                <input type="text" name="contact_person" value="{{ old('contact_person', $supplier->contact_person) }}">
            </div>

            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" value="{{ old('phone', $supplier->phone) }}">
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" value="{{ old('email', $supplier->email) }}">
            </div>

            <div class="form-group">
                <label>Address</label>
                <textarea name="address">{{ old('address', $supplier->address) }}</textarea>
            </div>

            <div class="form-group">
                <label>Status *</label>
                <select name="status">
                    <option value="1" {{ $supplier->status ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ !$supplier->status ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="form-group">
                <label>Internal Notes</label>
                <textarea name="notes">{{ old('notes', $supplier->notes) }}</textarea>
            </div>

            <button type="submit" class="primary-button">Update Supplier</button>
        </form>
    </div>

    <a href="{{ route('suppliers.index') }}" class="back-link">← Back to Suppliers</a>
</div>
@endsection
