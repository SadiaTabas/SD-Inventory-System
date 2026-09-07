@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')

<div class="dashboard-container">

    
    <div class="dashboard-header">
        <div>
            <h1>Admin Dashboard</h1>
            <p>Welcome back! Here's an overview of your inventory, orders, and logistics.</p>
        </div>

        <div style="display: flex; gap: 10px;">
            <a href="{{ route('order.create') }}" class="add-product-btn" style="background: #10b981;">
                + New Order
            </a>
            <a href="{{ route('product.create') }}" class="add-product-btn">
                + Add Product
            </a>
        </div>
    </div>

   
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

    
        <div class="stat-card">
            <div class="stat-icon categories-icon">
                🛒
            </div>
            <div class="stat-info">
                <p>Total Orders</p>
                <h2>{{ $totalOrders }}</h2>
            </div>
        </div>

        
        <div class="stat-card">
            <div class="stat-icon customers-icon">
                👥
            </div>
            <div class="stat-info">
                <p>Total Customers</p>
                <h2>{{ $totalCustomers }}</h2>
            </div>
        </div>

       
        <div class="stat-card">
            <div class="stat-icon admins-icon">
                🚚
            </div>
            <div class="stat-info">
                <p>Pending Orders Status</p>
                <h2>{{ $pendingOrders }}</h2>
            </div>
        </div>

    </div>

    
    <div class="dashboard-main-grid">

      
        <div class="dashboard-box">

            <div class="box-header">
                <div>
                    <h2>Low Stock Products</h2>
                    <p>Products requiring immediate restocking</p>
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
                                    <td>{{ $product->name }}</td>
                                    <td><strong>{{ $product->qty }}</strong></td>
                                    <td>
                                        @if($product->qty <= 2)
                                            <span class="stock-danger">Critical</span>
                                        @else
                                            <span class="stock-warning">Low Stock</span>
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

        <!-- Recent Orders -->
        <div class="dashboard-box">

            <div class="box-header">
                <div>
                    <h2>Recent Orders</h2>
                    <p>Latest customer purchases</p>
                </div>
                <a href="{{ route('order.index') }}" class="view-all">
                    View All
                </a>
            </div>

            @if(isset($recentOrders) && $recentOrders->count() > 0)
                <div class="recent-products">
                    @foreach($recentOrders as $order)
                        <div class="recent-product">
                            <div class="product-icon">
                                🛍️
                            </div>
                            <div class="product-details">
                                <h3>{{ $order->order_number ?? ('#' . $order->id) }}</h3>
                                <p>{{ $order->customer->name ?? 'User' }} &bull; {{ ucfirst($order->delivery_status) }}</p>
                            </div>
                            <div class="product-price">
                                BDT{{ number_format($order->total_amount, 2) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div>🛒</div>
                    <p>No orders recorded yet.</p>
                </div>
            @endif

        </div>

    </div>

    
    <div class="quick-actions">

        <h2>Inquiry</h2>

        <div class="actions-grid">

            <a href="{{ route('order.create') }}" class="action-card">
                <span>🛒</span>
                <div>
                    <h3>Create Order</h3>
                    <p>Place a manual order</p>
                </div>
            </a>

            <a href="{{ route('product.create') }}" class="action-card">
                <span>📦</span>
                <div>
                    <h3>Add Product</h3>
                    <p>Add new stock to inventory</p>
                </div>
            </a>

            <a href="{{ route('category.create') }}" class="action-card">
                <span>🗂️</span>
                <div>
                    <h3>Add Category</h3>
                    <p>Create a product category</p>
                </div>
            </a>

            <a href="{{ route('admin.index') }}" class="action-card">
                <span>👨‍💼</span>
                <div>
                    <h3>Manage Admins</h3>
                    <p>View and manage system administrators</p>
                </div>
            </a>

            <a href="{{ route('coupons.index') }}" class="action-card">
    <span>🏷️</span>
    <div>
        <h3>Manage Coupons</h3>
        <p>Create and manage promo discount offers</p>
    </div>
</a>

        </div>

    </div>

</div>

@endsection