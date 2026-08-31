@extends('layouts.app')

@section('title', 'Create Customer')

@section('content')

<div class="container">

 
<h1>Create New Customer</h1>

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
    action="{{ route('customer.store') }}"
>

    @csrf

    <div class="form-group">

        <label for="name">Customer Name</label>

        <input
            type="text"
            id="name"
            name="name"
            value="{{ old('name') }}"
            placeholder="Enter customer name"
            required
        >

    </div>

    <div class="form-group">

        <label for="email">Email</label>

        <input
            type="email"
            id="email"
            name="email"
            value="{{ old('email') }}"
            placeholder="Enter email address"
            required
        >

    </div>

    <div class="form-group">

        <label for="phone">Phone</label>

        <input
            type="text"
            id="phone"
            name="phone"
            value="{{ old('phone') }}"
            placeholder="Enter phone number"
            required
        >

    </div>

    <div class="form-group">

        <label for="address">Address</label>

        <textarea
            id="address"
            name="address"
            placeholder="Enter customer address"
        >{{ old('address') }}</textarea>

    </div>

    <div class="form-group">

        <label for="city">City</label>

        <input
            type="text"
            id="city"
            name="city"
            value="{{ old('city') }}"
            placeholder="Enter city"
        >

    </div>

    <div class="form-group">

        <label for="status">Status</label>

        <select id="status" name="status">

            <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>
                Active
            </option>

            <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>
                Inactive
            </option>

        </select>

    </div>

    <button
        type="submit"
        class="primary-button"
    >
        Save Customer
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
