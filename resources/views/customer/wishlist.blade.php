@extends('layouts.customer')

@section('title', 'My Wishlist - SD Store')

@section('content')
<div style="display: grid; grid-template-columns: 260px 1fr; gap: 30px; align-items: start;">
    <!-- Customer Sidebar -->
    <div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 24px;">
        <nav style="display: flex; flex-direction: column; gap: 6px;">
            <a href="{{ route('customer.account') }}" style="padding: 10px 14px; border-radius: var(--radius-sm); font-size: 14px; font-weight: 600; color: var(--gray-700);">📊 Dashboard</a>
            <a href="{{ route('customer.orders') }}" style="padding: 10px 14px; border-radius: var(--radius-sm); font-size: 14px; font-weight: 600; color: var(--gray-700);">📦 My Orders</a>
            <a href="{{ route('customer.addresses') }}" style="padding: 10px 14px; border-radius: var(--radius-sm); font-size: 14px; font-weight: 600; color: var(--gray-700);">📍 Saved Addresses</a>
            <a href="{{ route('customer.wishlist') }}" style="padding: 10px 14px; border-radius: var(--radius-sm); font-size: 14px; font-weight: 600; background: var(--primary-light); color: var(--primary);">♥ My Wishlist</a>
            <a href="{{ route('customer.reviews') }}" style="padding: 10px 14px; border-radius: var(--radius-sm); font-size: 14px; font-weight: 600; color: var(--gray-700);">★ My Reviews</a>
            <a href="{{ route('customer.profile') }}" style="padding: 10px 14px; border-radius: var(--radius-sm); font-size: 14px; font-weight: 600; color: var(--gray-700);">⚙️ Profile Settings</a>
        </nav>
    </div>

    <!-- Main Content -->
    <div>
        <h1 style="font-size: 24px; font-weight: 800; margin-bottom: 24px;">My Wishlist</h1>

        @if($wishlists->isEmpty())
            <div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 50px 20px; text-align: center;">
                <div style="font-size: 40px; margin-bottom: 12px;">♥</div>
                <h3 style="font-size: 18px; font-weight: 800; margin-bottom: 8px;">Your wishlist is empty</h3>
                <p style="color: var(--gray-500); font-size: 14px; margin-bottom: 20px;">Save items you want to buy later while shopping.</p>
                <a href="{{ route('shop') }}" class="btn btn-primary">Browse Products &rarr;</a>
            </div>
        @else
            <div class="product-grid">
                @foreach($wishlists as $item)
                    @php $product = $item->product; @endphp
                    @if($product)
                        <div class="product-card">
                            <a href="{{ route('product.details', $product) }}" class="product-img-wrap">
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="product-img" onerror="this.src='/uploads/products/default.png'">
                            </a>

                            <div class="product-content">
                                <div class="product-cat">{{ $product->category->name ?? 'General' }}</div>
                                <a href="{{ route('product.details', $product) }}" class="product-title">{{ $product->name }}</a>

                                <div class="product-price-row">
                                    <span class="product-price">৳{{ number_format($product->price, 2) }}</span>
                                    <span style="font-size: 12px; font-weight: 700; color: {{ $product->qty > 0 ? 'var(--success)' : 'var(--danger)' }};">
                                        {{ $product->qty > 0 ? 'In Stock' : 'Out of Stock' }}
                                    </span>
                                </div>

                                <div style="display: flex; gap: 8px; margin-top: 14px;">
                                    @if($product->qty > 0)
                                        <form action="{{ route('wishlist.moveToCart', $product) }}" method="POST" style="flex: 1;">
                                            @csrf
                                            <button type="submit" class="btn btn-primary btn-sm" style="width: 100%;">Move to Cart</button>
                                        </form>
                                    @endif
                                    <form action="{{ route('wishlist.remove', $product) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-secondary btn-sm" title="Remove">✕</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
