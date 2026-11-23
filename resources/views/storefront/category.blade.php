@extends('layouts.customer')

@section('title', $category->name . ' - SD Store')

@section('content')
<div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius); padding: 32px 28px; margin-bottom: 30px;">
    <div style="display: flex; align-items: center; gap: 14px;">
        <div style="width: 56px; height: 56px; border-radius: 12px; background: #eff6ff; color: var(--primary); font-size: 24px; font-weight: 800; display: flex; align-items: center; justify-content: center;">
            {{ strtoupper(substr($category->name, 0, 1)) }}
        </div>
        <div>
            <h1 style="font-size: 26px; font-weight: 800; margin-bottom: 4px;">{{ $category->name }}</h1>
            <p style="color: var(--gray-500); font-size: 14px;">
                {{ $category->description ?? 'Explore all available products in ' . $category->name . '.' }} &bull; {{ $products->total() }} Products
            </p>
        </div>
    </div>
</div>

@if($products->isEmpty())
    <div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius); padding: 60px 20px; text-align: center;">
        <h3 style="font-size: 18px; font-weight: 800; margin-bottom: 8px;">No products currently in this category</h3>
        <p style="color: var(--gray-500); font-size: 14px; margin-bottom: 20px;">Please check back soon or browse our full store catalog.</p>
        <a href="{{ route('shop') }}" class="btn btn-primary">Browse All Products</a>
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
                    <div class="product-cat">{{ $category->name }}</div>
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
                                <button class="btn btn-secondary btn-sm" disabled>Sold Out</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div style="margin-top: 36px;">
        {{ $products->links() }}
    </div>
@endif
@endsection
