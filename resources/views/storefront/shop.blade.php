@extends('layouts.customer')

@section('title', 'Shop Products - SD Store')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
    <div>
        <h1 style="font-size: 28px; font-weight: 800;">All Products</h1>
        <p style="color: var(--gray-500); font-size: 14px;">Showing {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} products</p>
    </div>

    <!-- Sorting -->
    <form action="{{ route('shop') }}" method="GET" style="display: flex; align-items: center; gap: 10px;">
        @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
        @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
        @if(request('min_price')) <input type="hidden" name="min_price" value="{{ request('min_price') }}"> @endif
        @if(request('max_price')) <input type="hidden" name="max_price" value="{{ request('max_price') }}"> @endif
        @if(request('in_stock')) <input type="hidden" name="in_stock" value="{{ request('in_stock') }}"> @endif

        <label for="sort" style="font-size: 14px; font-weight: 600; color: var(--gray-700);">Sort by:</label>
        <select name="sort" id="sort" onchange="this.form.submit()" style="padding: 8px 14px; border: 1.5px solid var(--gray-300); border-radius: var(--radius); font-size: 14px; background: white;">
            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
            <option value="popularity" {{ request('sort') == 'popularity' ? 'selected' : '' }}>Popularity</option>
        </select>
    </form>
</div>

<div style="display: grid; grid-template-columns: 260px 1fr; gap: 30px;">
    <!-- Sidebar Filters -->
    <aside style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius); padding: 22px; height: fit-content;">
        <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 16px; border-bottom: 1px solid var(--gray-100); padding-bottom: 10px;">Filters</h3>

        <form action="{{ route('shop') }}" method="GET">
            @if(request('q'))
                <input type="hidden" name="q" value="{{ request('q') }}">
            @endif

            <!-- Categories -->
            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 13px; font-weight: 700; text-transform: uppercase; color: var(--gray-500); margin-bottom: 10px;">Category</label>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <label style="font-size: 14px; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="radio" name="category" value="" {{ !request('category') ? 'checked' : '' }} onchange="this.form.submit()">
                        <span>All Categories</span>
                    </label>
                    @foreach($categories as $cat)
                        <label style="font-size: 14px; display: flex; align-items: center; justify-content: space-between; cursor: pointer;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <input type="radio" name="category" value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'checked' : '' }} onchange="this.form.submit()">
                                <span>{{ $cat->name }}</span>
                            </div>
                            <span style="font-size: 12px; color: var(--gray-400);">({{ $cat->products_count }})</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Price Range -->
            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 13px; font-weight: 700; text-transform: uppercase; color: var(--gray-500); margin-bottom: 10px;">Price (৳)</label>
                <div style="display: flex; gap: 8px; align-items: center;">
                    <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" style="width: 100%; padding: 8px; border: 1px solid var(--gray-300); border-radius: var(--radius-sm); font-size: 13px;">
                    <span>-</span>
                    <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" style="width: 100%; padding: 8px; border: 1px solid var(--gray-300); border-radius: var(--radius-sm); font-size: 13px;">
                </div>
            </div>

            <!-- In Stock Only -->
            <div style="margin-bottom: 24px;">
                <label style="font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="in_stock" value="1" {{ request('in_stock') == '1' ? 'checked' : '' }}>
                    <span>In Stock Only</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Apply Filters</button>
            <a href="{{ route('shop') }}" class="btn btn-secondary" style="width: 100%; margin-top: 8px; font-size: 13px;">Clear All</a>
        </form>
    </aside>

    <!-- Product Grid -->
    <div>
        @if($products->isEmpty())
            <div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius); padding: 60px 20px; text-align: center;">
                <div style="font-size: 40px; margin-bottom: 12px;">🔍</div>
                <h3 style="font-size: 18px; font-weight: 800; margin-bottom: 6px;">No products match your criteria</h3>
                <p style="color: var(--gray-500); font-size: 14px; margin-bottom: 20px;">Try adjusting your search keywords, price range, or category filter.</p>
                <a href="{{ route('shop') }}" class="btn btn-primary">Reset Filters</a>
            </div>
        @else
            <div class="product-grid">
                @foreach($products as $product)
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
                                            <button type="submit" class="btn-icon" title="Wishlist">♥</button>
                                        </form>
                                    @endauth

                                    @if($product->qty > 0)
                                        <form action="{{ route('cart.add', $product) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-primary btn-sm">+ Cart</button>
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

            <!-- Pagination -->
            <div style="margin-top: 36px;">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
