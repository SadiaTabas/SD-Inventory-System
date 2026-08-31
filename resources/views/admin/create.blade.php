@extends('layouts.app')

@section('title', 'Create Admin')

@section('content')

<div class="container">

    <h1>Create New Administrator</h1>

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
        action="{{ route('admin.store') }}"
    >
        @csrf

       
        <div class="form-group">
            <label for="name">Full Name</label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name') }}"
                placeholder="Enter full name"
                required
            >
        </div>

        
        <div class="form-group">
            <label for="email">Email Address</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="Enter admin email"
                required
            >
        </div>

         
        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input
                type="text"
                id="phone"
                name="phone"
                value="{{ old('phone') }}"
                placeholder="Enter phone number"
            >
        </div>

        
        <div class="form-group">
            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="Min. 8 chars (uppercase, lowercase, number, symbol)"
                required
            >
        </div>

         
        <div class="form-group">
            <label for="password_confirmation">Confirm Password</label>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                placeholder="Re-type password"
                required
            >
        </div>

        <button
            type="submit"
            class="primary-button"
        >
            Create Admin
        </button>

    </form>

    <a
        class="back-link"
        href="{{ route('admin.index') }}"
    >
        ← Back to Admins
    </a>

</div>

@endsection