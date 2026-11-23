@extends('layouts.customer')

@section('title', 'Saved Addresses - SD Store')

@section('content')
<div style="display: grid; grid-template-columns: 260px 1fr; gap: 30px; align-items: start;">
    <!-- Customer Sidebar -->
    <div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 24px;">
        <nav style="display: flex; flex-direction: column; gap: 6px;">
            <a href="{{ route('customer.account') }}" style="padding: 10px 14px; border-radius: var(--radius-sm); font-size: 14px; font-weight: 600; color: var(--gray-700);">📊 Dashboard</a>
            <a href="{{ route('customer.orders') }}" style="padding: 10px 14px; border-radius: var(--radius-sm); font-size: 14px; font-weight: 600; color: var(--gray-700);">📦 My Orders</a>
            <a href="{{ route('customer.addresses') }}" style="padding: 10px 14px; border-radius: var(--radius-sm); font-size: 14px; font-weight: 600; background: var(--primary-light); color: var(--primary);">📍 Saved Addresses</a>
            <a href="{{ route('customer.wishlist') }}" style="padding: 10px 14px; border-radius: var(--radius-sm); font-size: 14px; font-weight: 600; color: var(--gray-700);">♥ My Wishlist</a>
            <a href="{{ route('customer.reviews') }}" style="padding: 10px 14px; border-radius: var(--radius-sm); font-size: 14px; font-weight: 600; color: var(--gray-700);">★ My Reviews</a>
            <a href="{{ route('customer.profile') }}" style="padding: 10px 14px; border-radius: var(--radius-sm); font-size: 14px; font-weight: 600; color: var(--gray-700);">⚙️ Profile Settings</a>
        </nav>
    </div>

    <!-- Main Content -->
    <div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h1 style="font-size: 24px; font-weight: 800;">Saved Delivery Addresses</h1>
            <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('new-addr-form').style.display = document.getElementById('new-addr-form').style.display === 'none' ? 'block' : 'none'">+ Add New Address</button>
        </div>

        <!-- Add Address Form (Toggleable) -->
        <div id="new-addr-form" style="display: none; background: white; border: 1.5px solid var(--primary); border-radius: var(--radius-lg); padding: 24px; margin-bottom: 24px;">
            <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 16px;">Add New Address</h3>
            <form action="{{ route('customer.addresses.store') }}" method="POST">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 12px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 4px;">Contact Person Name *</label>
                        <input type="text" name="full_name" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--gray-300); border-radius: var(--radius-sm); font-size: 14px;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 4px;">Phone Number *</label>
                        <input type="text" name="phone" required placeholder="01712345678" style="width: 100%; padding: 8px 12px; border: 1px solid var(--gray-300); border-radius: var(--radius-sm); font-size: 14px;">
                    </div>
                </div>

                <div style="margin-bottom: 12px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 4px;">Street Address / Road / House *</label>
                    <textarea name="address_line" required rows="2" style="width: 100%; padding: 8px 12px; border: 1px solid var(--gray-300); border-radius: var(--radius-sm); font-size: 14px; font-family: inherit;"></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 4px;">Area</label>
                        <input type="text" name="area" placeholder="e.g. Uttara" style="width: 100%; padding: 8px 12px; border: 1px solid var(--gray-300); border-radius: var(--radius-sm); font-size: 14px;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 4px;">City *</label>
                        <input type="text" name="city" value="Dhaka" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--gray-300); border-radius: var(--radius-sm); font-size: 14px;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 4px;">District</label>
                        <input type="text" name="district" value="Dhaka" style="width: 100%; padding: 8px 12px; border: 1px solid var(--gray-300); border-radius: var(--radius-sm); font-size: 14px;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 4px;">Postal Code</label>
                        <input type="text" name="postal_code" placeholder="1230" style="width: 100%; padding: 8px 12px; border: 1px solid var(--gray-300); border-radius: var(--radius-sm); font-size: 14px;">
                    </div>
                </div>

                <div style="display: flex; gap: 20px; align-items: center; margin-bottom: 16px;">
                    <label style="font-size: 13px; font-weight: 700;">Address Type:</label>
                    <label style="font-size: 14px; display: flex; align-items: center; gap: 6px;"><input type="radio" name="type" value="home" checked> Home</label>
                    <label style="font-size: 14px; display: flex; align-items: center; gap: 6px;"><input type="radio" name="type" value="office"> Office</label>
                    <label style="font-size: 14px; display: flex; align-items: center; gap: 6px;"><input type="radio" name="type" value="other"> Other</label>
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                        <input type="checkbox" name="is_default" value="1">
                        <span>Set as default delivery address</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary btn-sm">Save Address</button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('new-addr-form').style.display='none'">Cancel</button>
            </form>
        </div>

        <!-- Addresses List -->
        @if($addresses->isEmpty())
            <div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 40px; text-align: center; color: var(--gray-500);">
                No addresses saved yet. Click "+ Add New Address" above to save your first address.
            </div>
        @else
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                @foreach($addresses as $addr)
                    <div style="background: white; border: 1.5px solid {{ $addr->is_default ? 'var(--primary)' : 'var(--gray-200)' }}; border-radius: var(--radius-lg); padding: 20px; position: relative;">
                        @if($addr->is_default)
                            <span style="position: absolute; top: 14px; right: 14px; background: #eff6ff; color: var(--primary); font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 4px; text-transform: uppercase;">Default</span>
                        @endif

                        <span style="background: #e2e8f0; font-size: 12px; font-weight: 700; padding: 2px 8px; border-radius: 4px; text-transform: capitalize; color: var(--gray-700);">{{ $addr->type }}</span>

                        <h4 style="font-size: 16px; font-weight: 700; margin: 10px 0 4px;">{{ $addr->full_name }}</h4>
                        <div style="font-size: 13px; color: var(--gray-500); margin-bottom: 8px;">📞 {{ $addr->phone }}</div>
                        <div style="font-size: 14px; color: var(--gray-700); line-height: 1.5; margin-bottom: 16px;">
                            {{ $addr->formatted_address }}
                        </div>

                        <div style="display: flex; gap: 10px; border-top: 1px solid var(--gray-100); padding-top: 12px;">
                            @if(!$addr->is_default)
                                <form action="{{ route('customer.addresses.default', $addr) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline btn-sm">Set as Default</button>
                                </form>
                            @endif
                            <form action="{{ route('customer.addresses.delete', $addr) }}" method="POST" onsubmit="return confirm('Delete this address?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
