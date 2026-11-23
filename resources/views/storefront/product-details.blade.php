@extends('layouts.customer')

@section('title', $product->name . ' - SD Store')

@section('content')
<div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 36px; margin-bottom: 40px; box-shadow: var(--shadow-sm);">
    <div style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 40px;">
        <!-- Image Area -->
        <div style="background: #f8fafc; border: 1px solid var(--gray-100); border-radius: var(--radius); padding: 20px; display: flex; align-items: center; justify-content: center; aspect-ratio: 1; max-height: 480px;">
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" style="max-width: 100%; max-height: 100%; object-fit: contain;" onerror="this.src='/uploads/products/default.png'">
        </div>

        <!-- Product Info Area -->
        <div style="display: flex; flex-direction: column;">
            <div style="font-size: 13px; font-weight: 700; text-transform: uppercase; color: var(--primary); margin-bottom: 8px;">
                <a href="{{ route('shop.category', $product->category_id) }}">{{ $product->category->name ?? 'General Category' }}</a>
            </div>

            <h1 style="font-size: 28px; font-weight: 800; line-height: 1.25; margin-bottom: 12px; color: var(--gray-900);">
                {{ $product->name }}
            </h1>

            <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 20px; font-size: 14px;">
                @if($product->sku)
                    <span style="color: var(--gray-500);">SKU: <strong>{{ $product->sku }}</strong></span>
                @endif

                @if($product->reviews_count > 0)
                    <span style="color: #f59e0b; font-weight: 700;">
                        ★ {{ $product->average_rating }} ({{ $product->reviews_count }} reviews)
                    </span>
                @else
                    <span style="color: var(--gray-400);">No reviews yet</span>
                @endif
            </div>

            <!-- Price -->
            <div style="background: #f8fafc; border: 1px solid var(--gray-200); border-radius: var(--radius); padding: 16px 20px; margin-bottom: 24px; display: flex; align-items: baseline; gap: 12px;">
                <span style="font-size: 32px; font-weight: 800; color: var(--primary);">
                    ৳{{ number_format($product->price, 2) }}
                </span>
                <span style="font-size: 13px; color: var(--gray-500);">Inclusive of all taxes</span>
            </div>

            <!-- Stock Availability -->
            <div style="margin-bottom: 24px; display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 14px; font-weight: 600;">Availability:</span>
                @if($product->qty > 0 && $product->status)
                    <span style="background: #ecfdf5; color: #065f46; font-size: 13px; font-weight: 700; padding: 4px 10px; border-radius: var(--radius-sm);">
                        ✓ In Stock ({{ $product->qty }} units ready to ship)
                    </span>
                @else
                    <span style="background: #fef2f2; color: #991b1b; font-size: 13px; font-weight: 700; padding: 4px 10px; border-radius: var(--radius-sm);">
                        ✕ Currently Out of Stock
                    </span>
                @endif
            </div>

            <!-- Purchase Form -->
            @if($product->qty > 0 && $product->status)
                <form action="{{ route('cart.add', $product) }}" method="POST" style="margin-bottom: 24px;">
                    @csrf
                    <div style="display: flex; gap: 14px; align-items: center; margin-bottom: 16px;">
                        <label for="quantity" style="font-size: 14px; font-weight: 700;">Quantity:</label>
                        <input type="number" id="quantity" name="quantity" value="1" min="1" max="{{ $product->qty }}" style="width: 80px; padding: 10px; border: 1.5px solid var(--gray-300); border-radius: var(--radius); font-size: 15px; font-weight: 600; text-align: center;">
                        <span style="font-size: 13px; color: var(--gray-500);">Max {{ $product->qty }}</span>
                    </div>

                    <div style="display: flex; gap: 12px;">
                        <button type="submit" class="btn btn-primary btn-lg" style="flex: 1;">
                            Add to Cart
                        </button>
                        <button type="submit" name="buy_now" value="1" class="btn btn-lg" style="background-color: var(--dark); color: white; flex: 1;">
                            Buy Now
                        </button>
                    </div>
                </form>
            @endif

            <!-- Wishlist Button -->
            @auth
                <form action="{{ route('wishlist.toggle', $product) }}" method="POST" style="margin-bottom: 24px;">
                    @csrf
                    <button type="submit" class="btn btn-outline" style="width: 100%;">
                        ♥ Add to Wishlist
                    </button>
                </form>
            @endauth

            <!-- Description -->
            <div style="border-top: 1px solid var(--gray-100); padding-top: 20px; margin-top: auto;">
                <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 8px;">Product Description</h3>
                <div style="font-size: 14px; color: var(--gray-700); line-height: 1.7; white-space: pre-line;">
                    {{ $product->description ?: 'No detailed description provided for this product.' }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Customer Reviews Section -->
<section style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 36px; margin-bottom: 40px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; border-bottom: 1px solid var(--gray-100); padding-bottom: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 800;">Customer Reviews</h2>
            <p style="font-size: 14px; color: var(--gray-500);">
                Average Rating: <strong>{{ $product->average_rating }} / 5</strong> &bull; {{ $product->reviews_count }} verified reviews
            </p>
        </div>
    </div>

    <!-- Review Submission Form -->
    @auth
        <div style="background: #f8fafc; border: 1px solid var(--gray-200); border-radius: var(--radius); padding: 20px; margin-bottom: 30px;">
            <h4 style="font-size: 16px; font-weight: 700; margin-bottom: 12px;">Write a Review</h4>
            <form action="{{ route('customer.review.store', $product) }}" method="POST">
                @csrf
                <div style="display: grid; grid-template-columns: 180px 1fr; gap: 16px; margin-bottom: 14px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Rating</label>
                        <select name="rating" required style="width: 100%; padding: 8px; border: 1px solid var(--gray-300); border-radius: var(--radius-sm); font-size: 14px; background: white;">
                            <option value="5">★★★★★ (5 Stars)</option>
                            <option value="4">★★★★☆ (4 Stars)</option>
                            <option value="3">★★★☆☆ (3 Stars)</option>
                            <option value="2">★★☆☆☆ (2 Stars)</option>
                            <option value="1">★☆☆☆☆ (1 Star)</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Review Title (Optional)</label>
                        <input type="text" name="title" placeholder="e.g. Excellent build quality!" style="width: 100%; padding: 8px 12px; border: 1px solid var(--gray-300); border-radius: var(--radius-sm); font-size: 14px;">
                    </div>
                </div>

                <div style="margin-bottom: 14px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Your Feedback</label>
                    <textarea name="comment" required rows="3" placeholder="Share your experience with this product..." style="width: 100%; padding: 10px 12px; border: 1px solid var(--gray-300); border-radius: var(--radius-sm); font-size: 14px; font-family: inherit;"></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-sm">Submit Review</button>
            </form>
        </div>
    @else
        <div style="background: #f8fafc; border: 1px solid var(--gray-200); border-radius: var(--radius); padding: 16px 20px; margin-bottom: 24px; font-size: 14px; color: var(--gray-700);">
            Please <a href="{{ route('login') }}" style="color: var(--primary); font-weight: 700;">sign in</a> to submit a customer review.
        </div>
    @endauth

    <!-- List of Reviews -->
    @if($product->reviews->isEmpty())
        <p style="color: var(--gray-500); font-size: 14px; text-align: center; padding: 20px;">No reviews yet. Be the first to review this product!</p>
    @else
        <div style="display: flex; flex-direction: column; gap: 18px;">
            @foreach($product->reviews as $rev)
                <div style="border-bottom: 1px solid var(--gray-100); padding-bottom: 18px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="font-weight: 700; font-size: 14px;">{{ $rev->user->name ?? 'Verified Customer' }}</span>
                            @if($rev->is_verified_purchase)
                                <span style="background: #ecfdf5; color: #065f46; font-size: 11px; font-weight: 700; padding: 2px 6px; border-radius: 4px;">✓ Verified Purchase</span>
                            @endif
                        </div>
                        <span style="font-size: 12px; color: var(--gray-400);">{{ $rev->created_at->format('M d, Y') }}</span>
                    </div>

                    <div style="color: #f59e0b; font-size: 14px; margin-bottom: 4px;">
                        @for($i = 1; $i <= 5; $i++)
                            {{ $i <= $rev->rating ? '★' : '☆' }}
                        @endfor
                        @if($rev->title)
                            <strong style="color: var(--gray-900); margin-left: 6px;">{{ $rev->title }}</strong>
                        @endif
                    </div>

                    <p style="font-size: 14px; color: var(--gray-700); line-height: 1.6;">
                        {{ $rev->comment }}
                    </p>
                </div>
            @endforeach
        </div>
    @endif
</section>

<!-- Related Products -->
@if($relatedProducts->isNotEmpty())
<section>
    <h2 style="font-size: 22px; font-weight: 800; margin-bottom: 20px;">Related Products</h2>
    <div class="product-grid">
        @foreach($relatedProducts as $rel)
            <div class="product-card">
                <a href="{{ route('product.details', $rel) }}" class="product-img-wrap">
                    <img src="{{ $rel->image_url }}" alt="{{ $rel->name }}" class="product-img" onerror="this.src='/uploads/products/default.png'">
                </a>
                <div class="product-content">
                    <div class="product-cat">{{ $rel->category->name ?? 'General' }}</div>
                    <a href="{{ route('product.details', $rel) }}" class="product-title">{{ $rel->name }}</a>
                    <div class="product-price-row">
                        <span class="product-price">৳{{ number_format($rel->price, 2) }}</span>
                        <a href="{{ route('product.details', $rel) }}" class="btn btn-outline btn-sm">View</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endif

@endsection
