<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SD Store - Quality Inventory & Online Shopping')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Storefront CSS -->
    <style>
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --primary-light: #eff6ff;
            --accent: #f59e0b;
            --dark: #0f172a;
            --gray-900: #1e293b;
            --gray-700: #334155;
            --gray-500: #64748b;
            --gray-300: #cbd5e1;
            --gray-100: #f1f5f9;
            --gray-50: #f8fafc;
            --white: #ffffff;
            --danger: #ef4444;
            --success: #10b981;
            --radius-sm: 6px;
            --radius: 10px;
            --radius-lg: 16px;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--gray-50);
            color: var(--gray-900);
            line-height: 1.5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        a {
            color: inherit;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        /* Top Announcement Bar */
        .top-bar {
            background-color: var(--dark);
            color: #94a3b8;
            font-size: 13px;
            padding: 8px 16px;
        }
        .top-bar-inner {
            max-width: 1280px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }
        .top-bar-links {
            display: flex;
            gap: 16px;
            align-items: center;
        }
        .top-bar a:hover {
            color: var(--white);
        }

        /* Main Header */
        .site-header {
            background-color: var(--white);
            border-bottom: 1px solid var(--gray-100);
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: var(--shadow-sm);
        }
        .header-inner {
            max-width: 1280px;
            margin: 0 auto;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            font-size: 22px;
            color: var(--dark);
            letter-spacing: -0.5px;
        }
        .brand-badge {
            background: linear-gradient(135deg, #2563eb, #3b82f6);
            color: white;
            padding: 4px 10px;
            border-radius: var(--radius-sm);
            font-size: 14px;
            font-weight: 700;
        }

        /* Search Bar */
        .header-search {
            flex: 1;
            max-width: 520px;
            position: relative;
        }
        .search-form {
            display: flex;
            align-items: center;
            background-color: var(--gray-100);
            border: 1.5px solid transparent;
            border-radius: 9999px;
            padding: 4px 6px 4px 18px;
            transition: all 0.2s ease;
        }
        .search-form:focus-within {
            background-color: var(--white);
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }
        .search-input {
            border: none;
            background: transparent;
            outline: none;
            width: 100%;
            font-size: 14px;
            color: var(--gray-900);
        }
        .search-btn {
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 9999px;
            padding: 8px 18px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s ease;
        }
        .search-btn:hover {
            background-color: var(--primary-hover);
        }

        /* Navigation & Actions */
        .header-actions {
            display: flex;
            align-items: center;
            gap: 18px;
        }
        .action-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 600;
            color: var(--gray-700);
            position: relative;
            padding: 6px 10px;
            border-radius: var(--radius-sm);
            transition: all 0.2s ease;
        }
        .action-item:hover {
            color: var(--primary);
            background-color: var(--primary-light);
        }
        .badge-count {
            background-color: var(--primary);
            color: white;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 9999px;
            min-width: 18px;
            text-align: center;
        }

        /* Secondary Nav */
        .nav-bar {
            background-color: var(--white);
            border-bottom: 1px solid var(--gray-200);
            padding: 0 20px;
        }
        .nav-inner {
            max-width: 1280px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            gap: 24px;
            overflow-x: auto;
            white-space: nowrap;
        }
        .nav-link {
            padding: 12px 4px;
            font-size: 14px;
            font-weight: 600;
            color: var(--gray-700);
            border-bottom: 2px solid transparent;
            transition: all 0.2s ease;
        }
        .nav-link:hover, .nav-link.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }

        /* Main Container */
        .main-content {
            flex: 1;
            max-width: 1280px;
            width: 100%;
            margin: 0 auto;
            padding: 30px 20px;
        }

        /* Alert notifications */
        .alert {
            padding: 14px 18px;
            border-radius: var(--radius);
            margin-bottom: 24px;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: space-between;
            animation: fadeIn 0.3s ease;
        }
        .alert-success {
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .alert-danger {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: var(--radius);
            font-size: 14px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        .btn-primary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
        }
        .btn-secondary {
            background-color: var(--gray-100);
            color: var(--gray-700);
        }
        .btn-secondary:hover {
            background-color: var(--gray-200);
        }
        .btn-outline {
            border: 1.5px solid var(--gray-300);
            background: transparent;
            color: var(--gray-700);
        }
        .btn-outline:hover {
            border-color: var(--primary);
            color: var(--primary);
        }
        .btn-danger {
            background-color: var(--danger);
            color: white;
        }
        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
        }
        .btn-lg {
            padding: 14px 28px;
            font-size: 16px;
        }

        /* Product Cards */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
            gap: 22px;
        }
        .product-card {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid var(--gray-200);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: all 0.25s ease;
            position: relative;
        }
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: #bfdbfe;
        }
        .product-badge-group {
            position: absolute;
            top: 12px;
            left: 12px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            z-index: 10;
        }
        .badge {
            font-size: 11px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: var(--radius-sm);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-sale {
            background-color: var(--danger);
            color: white;
        }
        .badge-category {
            background-color: rgba(15, 23, 42, 0.75);
            color: white;
            backdrop-filter: blur(4px);
        }
        .badge-stock-out {
            background-color: #64748b;
            color: white;
        }
        .product-img-wrap {
            position: relative;
            aspect-ratio: 1;
            background-color: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .product-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .product-card:hover .product-img {
            transform: scale(1.05);
        }
        .product-content {
            padding: 16px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .product-cat {
            font-size: 12px;
            color: var(--gray-500);
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 4px;
        }
        .product-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 8px;
            line-height: 1.35;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 40px;
        }
        .product-price-row {
            margin-top: auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 10px;
            border-top: 1px solid var(--gray-100);
        }
        .product-price {
            font-size: 18px;
            font-weight: 800;
            color: var(--primary);
        }
        .product-actions {
            display: flex;
            gap: 6px;
        }
        .btn-icon {
            width: 36px;
            height: 36px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--gray-100);
            border: none;
            color: var(--gray-700);
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .btn-icon:hover {
            background-color: var(--primary);
            color: white;
        }

        /* Footer */
        .site-footer {
            background-color: var(--dark);
            color: #94a3b8;
            padding: 60px 20px 24px;
            margin-top: auto;
        }
        .footer-grid {
            max-width: 1280px;
            margin: 0 auto 40px;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1.5fr;
            gap: 40px;
        }
        .footer-col h4 {
            color: white;
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 18px;
        }
        .footer-col ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .footer-col a:hover {
            color: white;
        }
        .footer-bottom {
            max-width: 1280px;
            margin: 0 auto;
            padding-top: 24px;
            border-top: 1px solid #1e293b;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            font-size: 13px;
        }

        /* Responsive Mobile Styles */
        @media (max-width: 860px) {
            .header-inner {
                flex-wrap: wrap;
            }
            .header-search {
                order: 3;
                max-width: 100%;
                width: 100%;
            }
            .footer-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    @stack('styles')
</head>
<body>

    <!-- Top Announcement Bar -->
    <div class="top-bar">
        <div class="top-bar-inner">
            <div>
                ⚡ <strong>Free Delivery</strong> on orders over ৳1,000 across Dhaka! | Fast Shipping &amp; Quality Guarantee
            </div>
            <div class="top-bar-links">
                @auth
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" style="color: #60a5fa; font-weight: 700;">Admin Dashboard ↗</a>
                    @endif
                    <a href="{{ route('customer.orders') }}">Track Order</a>
                    <a href="{{ route('customer.account') }}">My Account ({{ Auth::user()->name }})</a>
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" style="background:none; border:none; color: #f87171; cursor:pointer; font-size:13px; font-weight:600;">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}">Sign In</a>
                    <span>|</span>
                    <a href="{{ route('register') }}">Register</a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="site-header">
        <div class="header-inner">
            <!-- Brand -->
            <a href="{{ route('home') }}" class="brand">
                <span class="brand-badge">SD</span>
                <span>SD Store</span>
            </a>

            <!-- Search Bar -->
            <div class="header-search">
                <form action="{{ route('shop') }}" method="GET" class="search-form">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search products, brands, SKU..." class="search-input">
                    <button type="submit" class="search-btn">Search</button>
                </form>
            </div>

            <!-- Actions -->
            <div class="header-actions">
                @auth
                    <a href="{{ route('customer.wishlist') }}" class="action-item" title="My Wishlist">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                        <span class="badge-count">{{ Auth::user()->wishlists()->count() }}</span>
                    </a>
                @endauth

                <a href="{{ route('cart.index') }}" class="action-item" title="Shopping Cart">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <span>Cart</span>
                    @php
                        $cartService = app(\App\Services\CartService::class);
                        $cartTotals = $cartService->getCartTotals();
                    @endphp
                    <span class="badge-count">{{ $cartTotals['total_items_count'] }}</span>
                </a>

                @guest
                    <a href="{{ route('login') }}" class="btn btn-primary btn-sm">Login</a>
                @endguest
            </div>
        </div>

        <!-- Navigation Bar -->
        <nav class="nav-bar">
            <div class="nav-inner">
                <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
                <a href="{{ route('shop') }}" class="nav-link {{ request()->routeIs('shop') && !request('category') ? 'active' : '' }}">All Products</a>
                @php
                    $navCategories = \App\Models\Category::where('status', true)->take(6)->get();
                @endphp
                @foreach($navCategories as $cat)
                    <a href="{{ route('shop.category', $cat) }}" class="nav-link {{ request()->is('shop/category/' . $cat->id) ? 'active' : '' }}">{{ $cat->name }}</a>
                @endforeach
                <a href="{{ route('shop', ['in_stock' => 1]) }}" class="nav-link">In Stock</a>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        @if(session('success'))
            <div class="alert alert-success">
                <span>✓ {{ session('success') }}</span>
                <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; color:inherit; font-weight:700; cursor:pointer;">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                <span>✕ {{ session('error') }}</span>
                <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; color:inherit; font-weight:700; cursor:pointer;">&times;</button>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="alert alert-danger">
                <ul style="padding-left: 18px; margin: 0;">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
                <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; color:inherit; font-weight:700; cursor:pointer;">&times;</button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="footer-grid">
            <div class="footer-col">
                <div class="brand" style="color: white; margin-bottom: 14px;">
                    <span class="brand-badge">SD</span>
                    <span>SD Store</span>
                </div>
                <p style="font-size: 14px; line-height: 1.6; margin-bottom: 16px;">
                    Your trusted online destination for authentic products, transparent pricing, and swift delivery right to your doorstep.
                </p>
                <div style="font-size: 13px;">
                    <p>📍 Dhaka, Bangladesh</p>
                    <p>📞 Hotline: +880 1700-000000</p>
                    <p>✉️ support@sdinventory.com</p>
                </div>
            </div>

            <div class="footer-col">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="{{ route('shop') }}">Shop Products</a></li>
                    <li><a href="{{ route('cart.index') }}">Shopping Cart</a></li>
                    <li><a href="{{ route('customer.account') }}">My Account</a></li>
                    <li><a href="{{ route('customer.orders') }}">Order Tracking</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Categories</h4>
                <ul>
                    @foreach($navCategories as $cat)
                        <li><a href="{{ route('shop.category', $cat) }}">{{ $cat->name }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div class="footer-col">
                <h4>Safe &amp; Flexible Payments</h4>
                <p style="font-size: 13px; line-height: 1.6; margin-bottom: 16px;">
                    We support Cash on Delivery, manual bKash, and manual Nagad mobile payments with instant order verification.
                </p>
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <span style="background: #1e293b; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 700; color: #f43f5e;">bKash</span>
                    <span style="background: #1e293b; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 700; color: #ea580c;">Nagad</span>
                    <span style="background: #1e293b; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 700; color: #10b981;">Cash on Delivery</span>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div>
                &copy; {{ date('Y') }} SD Inventory &amp; E-commerce System. All rights reserved.
            </div>
            <div>
                Fast Fulfillment &bull; Secure Checkout &bull; Verified Reviews
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
