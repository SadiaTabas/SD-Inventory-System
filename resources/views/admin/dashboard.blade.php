@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')

<div class="dashboard-container">

    <!-- Header -->
    <div class="dashboard-header">
        <div>
            <h1>Admin Dashboard</h1>
            <p>Welcome back! Here's what's happening with your inventory.</p>
        </div>

        <a href="{{ route('product.create') }}" class="add-product-btn">
            + Add Product
        </a>
    </div>


    <!-- Statistics Cards -->
    <div class="stats-grid">

        <!-- Products -->
        <div class="stat-card">
            <div class="stat-icon products-icon">
                📦
            </div>

            <div class="stat-info">
                <p>Total Products</p>
                <h2>{{ $totalProducts }}</h2>
            </div>
        </div>


        <!-- Categories -->
        <div class="stat-card">
            <div class="stat-icon categories-icon">
                🗂️
            </div>

            <div class="stat-info">
                <p>Total Categories</p>
                <h2>{{ $totalCategories }}</h2>
            </div>
        </div>


        <!-- Customers -->
        <div class="stat-card">
            <div class="stat-icon customers-icon">
                👥
            </div>

            <div class="stat-info">
                <p>Total Customers</p>
                <h2>{{ $totalCustomers }}</h2>
            </div>
        </div>


        <!-- Admins -->
        <div class="stat-card">
            <div class="stat-icon admins-icon">
                👨‍💼
            </div>

            <div class="stat-info">
                <p>Total Admins</p>
                <h2>{{ $totalAdmins }}</h2>
            </div>
        </div>

    </div>


    <!-- Main Dashboard Grid -->
    <div class="dashboard-main-grid">

        <!-- Low Stock -->
        <div class="dashboard-box">

            <div class="box-header">
                <div>
                    <h2>Low Stock Products</h2>
                    <p>Products that need attention</p>
                </div>

                <span class="warning-badge">
                    {{ $lowStockProducts->count() }}
                </span>
            </div>


            @if($lowStockProducts->count() > 0)

                <div class="table-wrapper">

                    <table class="dashboard-table">

                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($lowStockProducts as $product)

                                <tr>

                                    <td>
                                        {{ $product->name }}
                                    </td>

                                    <td>
                                        <strong>{{ $product->qty }}</strong>
                                    </td>

                                    <td>

                                        @if($product->qty <= 2)

                                            <span class="stock-danger">
                                                Critical
                                            </span>

                                        @else

                                            <span class="stock-warning">
                                                Low Stock
                                            </span>

                                        @endif

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div class="empty-state">
                    <div>✅</div>
                    <p>All products have sufficient stock.</p>
                </div>

            @endif

        </div>


        <!-- Recent Products -->
        <div class="dashboard-box">

            <div class="box-header">

                <div>
                    <h2>Recent Products</h2>
                    <p>Recently added products</p>
                </div>

                <a href="{{ route('product.index') }}" class="view-all">
                    View All
                </a>

            </div>


            @if($recentProducts->count() > 0)

                <div class="recent-products">

                    @foreach($recentProducts as $product)

                        <div class="recent-product">

                            <div class="product-icon">
                                📦
                            </div>

                            <div class="product-details">

                                <h3>
                                    {{ $product->name }}
                                </h3>

                                <p>
                                    Quantity: {{ $product->qty }}
                                </p>

                            </div>

                            <div class="product-price">
                                ${{ number_format($product->price, 2) }}
                            </div>

                        </div>

                    @endforeach

                </div>

            @else

                <div class="empty-state">
                    <div>📦</div>
                    <p>No products available.</p>
                </div>

            @endif

        </div>

    </div>


    <!-- Quick Actions -->
    <div class="quick-actions">

        <h2>Quick Actions</h2>

        <div class="actions-grid">

            <a href="{{ route('product.create') }}" class="action-card">
                <span>📦</span>
                <div>
                    <h3>Add Product</h3>
                    <p>Add a new product to inventory</p>
                </div>
            </a>


            <a href="{{ route('category.create') }}" class="action-card">
                <span>🗂️</span>
                <div>
                    <h3>Add Category</h3>
                    <p>Create a new product category</p>
                </div>
            </a>


            <a href="{{ route('customer.index') }}" class="action-card">
                <span>👥</span>
                <div>
                    <h3>Customers</h3>
                    <p>Manage registered customers</p>
                </div>
            </a>


            <a href="{{ route('admin.create') }}" class="action-card">
                <span>👨‍💼</span>
                <div>
                    <h3>Add Admin</h3>
                    <p>Create another administrator</p>
                </div>
            </a>

        </div>

    </div>

</div>

@endsection