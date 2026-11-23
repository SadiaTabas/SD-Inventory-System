@extends('layouts.customer')

@section('title', 'Shopping Cart - SD Store')

@section('content')
<h1 style="font-size: 28px; font-weight: 800; margin-bottom: 24px;">Shopping Cart</h1>

@if($items->isEmpty())
    <div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 60px 20px; text-align: center;">
        <div style="font-size: 48px; margin-bottom: 16px;">🛒</div>
        <h2 style="font-size: 20px; font-weight: 800; margin-bottom: 8px;">Your shopping cart is empty</h2>
        <p style="color: var(--gray-500); font-size: 14px; margin-bottom: 24px;">Browse our wide collection of verified products and find something you love!</p>
        <a href="{{ route('shop') }}" class="btn btn-primary btn-lg">Start Shopping Now &rarr;</a>
    </div>
@else
    <div style="display: grid; grid-template-columns: 1fr 360px; gap: 30px; align-items: start;">
        <!-- Cart Items Table -->
        <div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 24px; overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; min-width: 550px;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--gray-100); text-align: left; font-size: 13px; color: var(--gray-500); text-transform: uppercase;">
                        <th style="padding: 12px 0;">Product</th>
                        <th style="padding: 12px;">Price</th>
                        <th style="padding: 12px; text-align: center;">Quantity</th>
                        <th style="padding: 12px; text-align: right;">Subtotal</th>
                        <th style="padding: 12px; text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                        @php
                            $product = $item->product;
                            $unitPrice = $product ? $product->price : $item->price;
                            $lineTotal = $unitPrice * $item->quantity;
                        @endphp
                        <tr style="border-bottom: 1px solid var(--gray-100);">
                            <td style="padding: 16px 0;">
                                <div style="display: flex; align-items: center; gap: 14px;">
                                    <div style="width: 60px; height: 60px; border-radius: 8px; background: #f8fafc; border: 1px solid var(--gray-200); overflow: hidden; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <img src="{{ $product ? $product->image_url : '/uploads/products/default.png' }}" alt="{{ $product ? $product->name : 'Product' }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    <div>
                                        <a href="{{ $product ? route('product.details', $product) : '#' }}" style="font-weight: 700; font-size: 15px; color: var(--gray-900);">
                                            {{ $product ? $product->name : 'Product unavailable' }}
                                        </a>
                                        @if($product && $product->sku)
                                            <div style="font-size: 12px; color: var(--gray-500);">SKU: {{ $product->sku }}</div>
                                        @endif
                                        @if($product && $product->qty < $item->quantity)
                                            <div style="font-size: 12px; color: var(--danger); font-weight: 700;">Only {{ $product->qty }} left!</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 16px 12px; font-weight: 600; font-size: 15px;">
                                ৳{{ number_format($unitPrice, 2) }}
                            </td>
                            <td style="padding: 16px 12px; text-align: center;">
                                <form action="{{ route('cart.update', $item) }}" method="POST" style="display: inline-flex; align-items: center; border: 1.5px solid var(--gray-300); border-radius: var(--radius-sm); overflow: hidden;">
                                    @csrf
                                    <button type="submit" name="quantity" value="{{ max(1, $item->quantity - 1) }}" style="padding: 6px 10px; background: #f8fafc; border: none; cursor: pointer; font-weight: 700;">-</button>
                                    <span style="padding: 6px 14px; font-weight: 700; font-size: 14px; min-width: 32px; text-align: center;">{{ $item->quantity }}</span>
                                    <button type="submit" name="quantity" value="{{ $item->quantity + 1 }}" style="padding: 6px 10px; background: #f8fafc; border: none; cursor: pointer; font-weight: 700;" {{ $product && $item->quantity >= $product->qty ? 'disabled' : '' }}>+</button>
                                </form>
                            </td>
                            <td style="padding: 16px 12px; text-align: right; font-weight: 800; font-size: 16px; color: var(--primary);">
                                ৳{{ number_format($lineTotal, 2) }}
                            </td>
                            <td style="padding: 16px 12px; text-align: center;">
                                <form action="{{ route('cart.remove', $item) }}" method="POST">
                                    @csrf
                                    <button type="submit" style="background: none; border: none; color: var(--danger); font-size: 18px; cursor: pointer;" title="Remove Item">&times;</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--gray-100); flex-wrap: wrap; gap: 12px;">
                <a href="{{ route('shop') }}" class="btn btn-outline">&larr; Continue Shopping</a>
                <form action="{{ route('cart.clear') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-secondary" onclick="return confirm('Are you sure you want to clear your cart?');">Clear Cart</button>
                </form>
            </div>
        </div>

        <!-- Order Summary & Coupons -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <!-- Coupon Card -->
            <div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 22px;">
                <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 12px;">Promo Code</h3>
                @if($coupon_code)
                    <div style="background: #eff6ff; border: 1.5px solid #bfdbfe; border-radius: var(--radius-sm); padding: 12px 14px; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <span style="font-weight: 800; color: var(--primary);">{{ $coupon_code }}</span>
                            <div style="font-size: 12px; color: #1e40af;">Applied: -৳{{ number_format($discount, 2) }}</div>
                        </div>
                        <form action="{{ route('cart.coupon.remove') }}" method="POST">
                            @csrf
                            <button type="submit" style="background: none; border: none; color: var(--danger); font-size: 13px; font-weight: 700; cursor: pointer;">Remove</button>
                        </form>
                    </div>
                @else
                    <form action="{{ route('cart.coupon.apply') }}" method="POST" style="display: flex; gap: 8px;">
                        @csrf
                        <input type="text" name="coupon_code" placeholder="Enter coupon code" required style="flex: 1; padding: 10px 12px; border: 1px solid var(--gray-300); border-radius: var(--radius); font-size: 14px; text-transform: uppercase;">
                        <button type="submit" class="btn btn-primary btn-sm">Apply</button>
                    </form>
                @endif
            </div>

            <!-- Totals Card -->
            <div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 24px;">
                <h3 style="font-size: 18px; font-weight: 800; margin-bottom: 16px; border-bottom: 1px solid var(--gray-100); padding-bottom: 10px;">Order Summary</h3>

                <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px; color: var(--gray-700);">
                    <span>Subtotal</span>
                    <span style="font-weight: 700;">৳{{ number_format($subtotal, 2) }}</span>
                </div>

                @if($discount > 0)
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px; color: var(--success);">
                        <span>Coupon Discount</span>
                        <span style="font-weight: 700;">-৳{{ number_format($discount, 2) }}</span>
                    </div>
                @endif

                <div style="display: flex; justify-content: space-between; margin-bottom: 16px; font-size: 14px; color: var(--gray-700);">
                    <span>Delivery Charge</span>
                    <span style="font-weight: 700;">
                        @if($delivery_charge > 0)
                            ৳{{ number_format($delivery_charge, 2) }}
                        @else
                            <strong style="color: var(--success);">FREE</strong>
                        @endif
                    </span>
                </div>

                <div style="border-top: 2px solid var(--gray-100); padding-top: 16px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: baseline;">
                    <span style="font-size: 16px; font-weight: 800;">Estimated Total</span>
                    <span style="font-size: 24px; font-weight: 800; color: var(--primary);">৳{{ number_format($total, 2) }}</span>
                </div>

                <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-lg" style="width: 100%;">
                    Proceed to Checkout &rarr;
                </a>
            </div>
        </div>
    </div>
@endif
@endsection
