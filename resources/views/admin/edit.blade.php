@extends('layouts.app')

@section('title', 'Edit Admin')

@section('content')

<div class="container">

    <h1>Edit Administrator</h1>

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
        action="{{ route('admin.update', $admin) }}"
    >
        @csrf
        @method('PUT')

         
        <div class="form-group">
            <label for="name">Full Name</label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $admin->name) }}"
                required
            >
        </div>

         
        <div class="form-group">
            <label for="email">Email Address</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email', $admin->email) }}"
                required
            >
        </div>

         
        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input
                type="text"
                id="phone"
                name="phone"
                value="{{ old('phone', $admin->phone) }}"
            >
        </div>

        
        <div class="form-group">
            <label for="status">Account Status</label>
            <select id="status" name="status">
                <option value="1" {{ old('status', $admin->status) == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ old('status', $admin->status) == 0 ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        
        <div class="form-group">
            <label for="password">New Password (Leave blank to keep current)</label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="Enter new password"
            >
        </div>

        
        <div class="form-group">
            <label for="password_confirmation">Confirm New Password</label>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                placeholder="Re-type new password"
            >
        </div>

        <button
            type="submit"
            class="primary-button"
        >
            Update Admin
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