@extends('layouts.app')

@section('title', 'Edit Customer')

@section('content')

<div class="container">

 
<h1>Edit Customer</h1>

{{-- Validation Errors --}}
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
    action="{{ route('customer.update', $customer) }}"
>

    @csrf
    @method('PUT')

    {{-- Name --}}
    <div class="form-group">

        <label for="name">
            Customer Name
        </label>

        <input
            type="text"
            id="name"
            name="name"
            value="{{ old('name', $customer->name) }}"
            placeholder="Enter customer name"
            required
        >

    </div>

    {{-- Email --}}
    <div class="form-group">

        <label for="email">
            Email
        </label>

        <input
            type="email"
            id="email"
            name="email"
            value="{{ old('email', $customer->email) }}"
            placeholder="Enter email address"
            required
        >

    </div>

    {{-- Phone --}}
    <div class="form-group">

        <label for="phone">
            Phone
        </label>

        <input
            type="text"
            id="phone"
            name="phone"
            value="{{ old('phone', $customer->phone) }}"
            placeholder="Enter phone number"
            required
        >

    </div>

    {{-- Address --}}
    <div class="form-group">

        <label for="address">
            Address
        </label>

        <textarea
            id="address"
            name="address"
            placeholder="Enter customer address"
        >{{ old('address', $customer->address) }}</textarea>

    </div>

    {{-- City --}}
    <div class="form-group">

        <label for="city">
            City
        </label>

        <input
            type="text"
            id="city"
            name="city"
            value="{{ old('city', $customer->city) }}"
            placeholder="Enter city"
        >

    </div>

    {{-- Status --}}
    <div class="form-group">

        <label for="status">
            Status
        </label>

        <select id="status" name="status">

            <option
                value="1"
                {{ old('status', $customer->status) == 1 ? 'selected' : '' }}
            >
                Active
            </option>

            <option
                value="0"
                {{ old('status', $customer->status) == 0 ? 'selected' : '' }}
            >
                Inactive
            </option>

        </select>

    </div>

    {{-- Update --}}
    <button
        type="submit"
        class="primary-button"
    >
        Update Customer
    </button>

</form>

<a
    class="back-link"
    href="{{ route('customer.index') }}"
>
    ← Back to Customers
</a>
 

</div>

@endsection
