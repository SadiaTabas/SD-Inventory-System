@extends('layouts.customer')

@section('title', 'My Product Reviews - SD Store')

@section('content')
<div style="display: grid; grid-template-columns: 260px 1fr; gap: 30px; align-items: start;">
    <!-- Customer Sidebar -->
    <div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 24px;">
        <nav style="display: flex; flex-direction: column; gap: 6px;">
            <a href="{{ route('customer.account') }}" style="padding: 10px 14px; border-radius: var(--radius-sm); font-size: 14px; font-weight: 600; color: var(--gray-700);">📊 Dashboard</a>
            <a href="{{ route('customer.orders') }}" style="padding: 10px 14px; border-radius: var(--radius-sm); font-size: 14px; font-weight: 600; color: var(--gray-700);">📦 My Orders</a>
            <a href="{{ route('customer.addresses') }}" style="padding: 10px 14px; border-radius: var(--radius-sm); font-size: 14px; font-weight: 600; color: var(--gray-700);">📍 Saved Addresses</a>
            <a href="{{ route('customer.wishlist') }}" style="padding: 10px 14px; border-radius: var(--radius-sm); font-size: 14px; font-weight: 600; color: var(--gray-700);">♥ My Wishlist</a>
            <a href="{{ route('customer.reviews') }}" style="padding: 10px 14px; border-radius: var(--radius-sm); font-size: 14px; font-weight: 600; background: var(--primary-light); color: var(--primary);">★ My Reviews</a>
            <a href="{{ route('customer.profile') }}" style="padding: 10px 14px; border-radius: var(--radius-sm); font-size: 14px; font-weight: 600; color: var(--gray-700);">⚙️ Profile Settings</a>
        </nav>
    </div>

    <!-- Main Content -->
    <div>
        <h1 style="font-size: 24px; font-weight: 800; margin-bottom: 24px;">My Reviews &amp; Ratings</h1>

        @if($reviews->isEmpty())
            <div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 50px 20px; text-align: center; color: var(--gray-500);">
                You haven't submitted any product reviews yet. Share your feedback on purchased products!
            </div>
        @else
            <div style="display: flex; flex-direction: column; gap: 16px;">
                @foreach($reviews as $rev)
                    <div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 22px;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                            <div>
                                <h3 style="font-size: 16px; font-weight: 700;">
                                    <a href="{{ route('product.details', $rev->product_id) }}" style="color: var(--primary);">{{ $rev->product->name ?? 'Product' }}</a>
                                </h3>
                                <div style="color: #f59e0b; font-size: 14px; margin-top: 4px;">
                                    @for($i = 1; $i <= 5; $i++)
                                        {{ $i <= $rev->rating ? '★' : '☆' }}
                                    @endfor
                                    @if($rev->title)
                                        <strong style="color: var(--gray-900); margin-left: 6px;">{{ $rev->title }}</strong>
                                    @endif
                                </div>
                            </div>
                            <span style="font-size: 12px; font-weight: 700; padding: 2px 8px; border-radius: 4px; text-transform: uppercase; background: {{ $rev->status === 'approved' ? '#ecfdf5; color:#065f46;' : '#fffbeb; color:#92400e;' }}">
                                {{ $rev->status }}
                            </span>
                        </div>

                        <p style="font-size: 14px; color: var(--gray-700); line-height: 1.6; margin-bottom: 10px;">
                            {{ $rev->comment }}
                        </p>

                        <div style="font-size: 12px; color: var(--gray-400);">
                            Submitted on {{ $rev->created_at->format('M d, Y') }}
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="margin-top: 24px;">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
