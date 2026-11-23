@extends('layouts.customer')

@section('title', 'Profile Settings - SD Store')

@section('content')
<div style="display: grid; grid-template-columns: 260px 1fr; gap: 30px; align-items: start;">
    <!-- Customer Sidebar -->
    <div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 24px;">
        <nav style="display: flex; flex-direction: column; gap: 6px;">
            <a href="{{ route('customer.account') }}" style="padding: 10px 14px; border-radius: var(--radius-sm); font-size: 14px; font-weight: 600; color: var(--gray-700);">📊 Dashboard</a>
            <a href="{{ route('customer.orders') }}" style="padding: 10px 14px; border-radius: var(--radius-sm); font-size: 14px; font-weight: 600; color: var(--gray-700);">📦 My Orders</a>
            <a href="{{ route('customer.addresses') }}" style="padding: 10px 14px; border-radius: var(--radius-sm); font-size: 14px; font-weight: 600; color: var(--gray-700);">📍 Saved Addresses</a>
            <a href="{{ route('customer.wishlist') }}" style="padding: 10px 14px; border-radius: var(--radius-sm); font-size: 14px; font-weight: 600; color: var(--gray-700);">♥ My Wishlist</a>
            <a href="{{ route('customer.reviews') }}" style="padding: 10px 14px; border-radius: var(--radius-sm); font-size: 14px; font-weight: 600; color: var(--gray-700);">★ My Reviews</a>
            <a href="{{ route('customer.profile') }}" style="padding: 10px 14px; border-radius: var(--radius-sm); font-size: 14px; font-weight: 600; background: var(--primary-light); color: var(--primary);">⚙️ Profile Settings</a>
        </nav>
    </div>

    <!-- Forms Area -->
    <div style="display: flex; flex-direction: column; gap: 24px;">
        <!-- Personal Information -->
        <div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 28px;">
            <h2 style="font-size: 18px; font-weight: 800; margin-bottom: 20px; border-bottom: 1px solid var(--gray-100); padding-bottom: 12px;">Personal Information</h2>

            <form action="{{ route('customer.profile.update') }}" method="POST">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 18px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Full Name *</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required style="width: 100%; padding: 10px 12px; border: 1.5px solid var(--gray-300); border-radius: var(--radius-sm); font-size: 14px;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Phone Number *</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required style="width: 100%; padding: 10px 12px; border: 1.5px solid var(--gray-300); border-radius: var(--radius-sm); font-size: 14px;">
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Email Address *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required style="width: 100%; padding: 10px 12px; border: 1.5px solid var(--gray-300); border-radius: var(--radius-sm); font-size: 14px;">
                </div>

                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>

        <!-- Security / Password -->
        <div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 28px;">
            <h2 style="font-size: 18px; font-weight: 800; margin-bottom: 20px; border-bottom: 1px solid var(--gray-100); padding-bottom: 12px;">Change Password</h2>

            <form action="{{ route('customer.password.update') }}" method="POST">
                @csrf
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Current Password *</label>
                    <input type="password" name="current_password" required style="width: 100%; max-width: 400px; padding: 10px 12px; border: 1.5px solid var(--gray-300); border-radius: var(--radius-sm); font-size: 14px;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 20px; max-width: 600px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">New Password * (Min 8 chars)</label>
                        <input type="password" name="password" required style="width: 100%; padding: 10px 12px; border: 1.5px solid var(--gray-300); border-radius: var(--radius-sm); font-size: 14px;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Confirm New Password *</label>
                        <input type="password" name="password_confirmation" required style="width: 100%; padding: 10px 12px; border: 1.5px solid var(--gray-300); border-radius: var(--radius-sm); font-size: 14px;">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update Password</button>
            </form>
        </div>
    </div>
</div>
@endsection
