@extends('layouts.customer')

@section('title', 'SD Store - Quality Products & Online Shopping')

@section('content')
<!-- Hero Section -->
<section style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); border-radius: var(--radius-lg); color: white; padding: 48px 40px; margin-bottom: 40px; position: relative; overflow: hidden; box-shadow: var(--shadow-lg);">
    <div style="max-width: 650px; position: relative; z-index: 2;">
        <span style="background: rgba(255,255,255,0.2); backdrop-filter: blur(4px); padding: 5px 12px; border-radius: 9999px; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Premium Collection 2026</span>
        <h1 style="font-size: 40px; font-weight: 800; line-height: 1.15; margin: 16px 0; color: white;">Smart Shopping with Real-Time Inventory</h1>
        <p style="font-size: 16px; line-height: 1.6; color: #dbeafe; margin-bottom: 24px;">
            Explore premium quality goods with instant order tracking, flexible mobile payments (bKash/Nagad), and swift delivery to your home.
        </p>
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="{{ route('shop') }}" class="btn btn-lg" style="background-color: white; color: var(--primary); font-weight: 700;">Explore Shop Now &rarr;</a>
            <a href="{{ route('customer.orders') }}" class="btn btn-outline btn-lg" style="border-color: rgba(255,255,255,0.4); color: white;">Track Existing Order</a>
        </div>
    </div>
</section>

<!-- Value Props -->
<section style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 48px;">
    <div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius); padding: 20px; display: flex; align-items: center; gap: 14px;">
        <div style="width: 48px; height: 48px; border-radius: 10px; background: #eff6ff; display: flex; align-items: center; justify-content: center; font-size: 22px; color: var(--primary);">🚚</div>
        <div>
            <h4 style="font-size: 15px; font-weight: 700;">Fast Delivery</h4>
            <p style="font-size: 13px; color: var(--gray-500);">Within 24-48 hours</p>
        </div>
    </div>
    <div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius); padding: 20px; display: flex; align-items: center; gap: 14px;">
        <div style="width: 48px; height: 48px; border-radius: 10px; background: #ecfdf5; display: flex; align-items: center; justify-content: center; font-size: 22px; color: var(--success);">🛡️</div>
        <div>
            <h4 style="font-size: 15px; font-weight: 700;">Authentic Products</h4>
            <p style="font-size: 13px; color: var(--gray-500);">100% verified stock</p>
        </div>
    </div>
    <div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius); padding: 20px; display: flex; align-items: center; gap: 14px;">
        <div style="width: 48px; height: 48px; border-radius: 10px; background: #fef3c7; display: flex; align-items: center; justify-content: center; font-size: 22px; color: var(--accent);">💳</div>
        <div>
            <h4 style="font-size: 15px; font-weight: 700;">Flexible Payment</h4>
            <p style="font-size: 13px; color: var(--gray-500);">COD, bKash &amp; Nagad</p>
        </div>
    </div>
    <div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius); padding: 20px; display: flex; align-items: center; gap: 14px;">
        <div style="width: 48px; height: 48px; border-radius: 10px; background: #fdf2f8; display: flex; align-items: center; justify-content: center; font-size: 22px; color: #ec4899;">🎧</div>
        <div>
            <h4 style="font-size: 15px; font-weight: 700;">Customer Care</h4>
            <p style="font-size: 13px; color: var(--gray-500);">Dedicated phone support</p>
        </div>
    </div>
</section>

<!-- Active Promotional Coupons -->
@if($coupons->isNotEmpty())
<section style="margin-bottom: 48px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 800;">Available Promotional Coupons</h2>
            <p style="font-size: 14px; color: var(--gray-500);">Apply these coupon codes during checkout for instant savings</p>
        </div>
    </div>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px;">
        @foreach($coupons as $coupon)
            <div style="background: white; border: 2px dashed #bfdbfe; border-radius: var(--radius); padding: 16px 20px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <span style="font-size: 18px; font-weight: 800; color: var(--primary); letter-spacing: 1px;">{{ $coupon->code }}</span>
                    <p style="font-size: 13px; color: var(--gray-500); margin-top: 4px;">
                        @if($coupon->type === 'percent')
                            Get <strong>{{ $coupon->value }}% OFF</strong>
                        @else
                            Flat <strong>৳{{ number_format($coupon->value, 2) }} OFF</strong>
                        @endif
                        @if($coupon->min_order_amount > 0)
                            <br><small>Min order: ৳{{ number_format($coupon->min_order_amount, 2) }}</small>
                        @endif
                    </p>
                </div>
                <button type="button" class="btn btn-outline btn-sm" onclick="navigator.clipboard.writeText('{{ $coupon->code }}'); alert('Copied code: {{ $coupon->code }}');">Copy</button>
            </div>
        @endforeach
    </div>
</section>
@endif

<!-- Featured Categories -->
<section style="margin-bottom: 48px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 800;">Featured Categories</h2>
            <p style="font-size: 14px; color: var(--gray-500);">Browse products organized by category</p>
        </div>
        <a href="{{ route('shop') }}" style="color: var(--primary); font-weight: 700; font-size: 14px;">View All &rarr;</a>
    </div>

    @if($categories->isEmpty())
        <div style="background: white; padding: 30px; border-radius: var(--radius); text-align: center; color: var(--gray-500);">
            No categories available at the moment.
        </div>
    @else
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 16px;">
            @foreach($categories as $cat)
                <a href="{{ route('shop.category', $cat) }}" style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius); padding: 20px 16px; text-align: center; transition: all 0.2s ease; display: block;" onmouseover="this.style.borderColor='var(--primary)'; this.style.transform='translateY(-2px)'" onmouseout="this.style.borderColor='var(--gray-200)'; this.style.transform='none'">
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: #eff6ff; color: var(--primary); font-size: 20px; font-weight: 800; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;">
                        {{ strtoupper(substr($cat->name, 0, 1)) }}
                    </div>
                    <h3 style="font-size: 15px; font-weight: 700; color: var(--gray-900); margin-bottom: 4px;">{{ $cat->name }}</h3>
                    <p style="font-size: 12px; color: var(--gray-500);">{{ $cat->products_count }} Products</p>
                </a>
            @endforeach
        </div>
    @endif
</section>

<!-- Featured Products -->
<section style="margin-bottom: 48px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 800;">Featured Products</h2>
            <p style="font-size: 14px; color: var(--gray-500);">Hand-picked top tier items ready to order</p>
        </div>
        <a href="{{ route('shop') }}" style="color: var(--primary); font-weight: 700; font-size: 14px;">See More &rarr;</a>
    </div>

    @if($featuredProducts->isEmpty())
        <div style="background: white; padding: 40px; border-radius: var(--radius); text-align: center; color: var(--gray-500);">
            <p style="font-size: 16px; font-weight: 600;">No products found in the catalog.</p>
        </div>
    @else
        <div class="product-grid">
            @foreach($featuredProducts as $product)
                <div class="product-card">
                    <div class="product-badge-group">
                        @if($product->qty <= 0)
                            <span class="badge badge-stock-out">Out of Stock</span>
                        @else
                            <span class="badge" style="background-color: #10b981; color: white;">In Stock</span>
                        @endif
                    </div>

                    <a href="{{ route('product.details', $product) }}" class="product-img-wrap">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="product-img" onerror="this.src='/uploads/products/default.png'">
                    </a>

                    <div class="product-content">
                        <div class="product-cat">{{ $product->category->name ?? 'General' }}</div>
                        <a href="{{ route('product.details', $product) }}" class="product-title">{{ $product->name }}</a>

                        <div class="product-price-row">
                            <span class="product-price">৳{{ number_format($product->price, 2) }}</span>
                            <div class="product-actions">
                                @auth
                                    <form action="{{ route('wishlist.toggle', $product) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn-icon" title="Add to Wishlist">♥</button>
                                    </form>
                                @endauth

                                @if($product->qty > 0)
                                    <form action="{{ route('cart.add', $product) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm" title="Add to Cart">+ Cart</button>
                                    </form>
                                @else
                                    <button class="btn btn-secondary btn-sm" disabled>Sold Out</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</section>

<!-- Latest Arrivals -->
@if($newArrivals->isNotEmpty())
<section style="margin-bottom: 48px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 800;">New Arrivals</h2>
            <p style="font-size: 14px; color: var(--gray-500);">Freshly added inventory</p>
        </div>
        <a href="{{ route('shop') }}" style="color: var(--primary); font-weight: 700; font-size: 14px;">Browse Catalog &rarr;</a>
    </div>

    <div class="product-grid">
        @foreach($newArrivals as $product)
            <div class="product-card">
                <a href="{{ route('product.details', $product) }}" class="product-img-wrap">
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="product-img" onerror="this.src='/uploads/products/default.png'">
                </a>

                <div class="product-content">
                    <div class="product-cat">{{ $product->category->name ?? 'General' }}</div>
                    <a href="{{ route('product.details', $product) }}" class="product-title">{{ $product->name }}</a>

                    <div class="product-price-row">
                        <span class="product-price">৳{{ number_format($product->price, 2) }}</span>
                        <div class="product-actions">
                            @if($product->qty > 0)
                                <form action="{{ route('cart.add', $product) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm">+ Cart</button>
                                </form>
                            @else
                                <span style="font-size: 12px; color: var(--gray-500);">Out of Stock</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endif

@endsection
