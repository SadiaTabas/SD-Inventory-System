@extends('layouts.app')

@section('title', 'Admin Dashboard - SD Inventory System')

@section('content')
<div class="dashboard-container">

    <!-- Top Header -->
    <div class="dashboard-header" style="flex-wrap: wrap; gap: 16px;">
        <div>
            <h1>Executive Dashboard</h1>
            <p>Real-time analytics, inventory valuation, online store sales, and operations overview.</p>
        </div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('product.create') }}" class="add-product-btn">+ New Product</a>
            <a href="{{ route('order.create') }}" class="add-product-btn" style="background: #10b981;">+ Create Manual Order</a>
            <a href="{{ route('reports.index') }}" class="add-product-btn" style="background: #6366f1;">📊 Sales Reports</a>
        </div>
    </div>

    <!-- 1. Sales & Revenue Overview -->
    <div style="margin-bottom: 28px;">
        <h3 style="font-size: 16px; font-weight: 800; color: #374151; margin-bottom: 14px; text-transform: uppercase; letter-spacing: 0.5px;">💰 Revenue &amp; Financial Overview</h3>
        <div style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 16px;">
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <span style="font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase;">Total Sales</span>
                <div style="font-size: 22px; font-weight: 800; color: #1e3a8a; margin-top: 4px;">৳{{ number_format($totalRevenue, 2) }}</div>
                <small style="color: #9ca3af; font-size: 11px;">Excludes cancelled</small>
            </div>
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <span style="font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase;">Today's Revenue</span>
                <div style="font-size: 22px; font-weight: 800; color: #059669; margin-top: 4px;">৳{{ number_format($todayRevenue, 2) }}</div>
                <small style="color: #059669; font-size: 11px;">Active orders</small>
            </div>
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <span style="font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase;">This Week</span>
                <div style="font-size: 22px; font-weight: 800; color: #2563eb; margin-top: 4px;">৳{{ number_format($weekRevenue, 2) }}</div>
                <small style="color: #6b7280; font-size: 11px;">Monday to Date</small>
            </div>
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <span style="font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase;">This Month</span>
                <div style="font-size: 22px; font-weight: 800; color: #7c3aed; margin-top: 4px;">৳{{ number_format($monthRevenue, 2) }}</div>
                <small style="color: #6b7280; font-size: 11px;">Current month total</small>
            </div>
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <span style="font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase;">Collected (Paid)</span>
                <div style="font-size: 22px; font-weight: 800; color: #10b981; margin-top: 4px;">৳{{ number_format($totalPaidAmount, 2) }}</div>
                <small style="color: #10b981; font-size: 11px;">Verified revenue</small>
            </div>
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <span style="font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase;">Due / Receivables</span>
                <div style="font-size: 22px; font-weight: 800; color: #dc2626; margin-top: 4px;">৳{{ number_format($totalDueAmount, 2) }}</div>
                <small style="color: #dc2626; font-size: 11px;">Pending collection</small>
            </div>
        </div>
    </div>

    <!-- 2. Operations & Inventory Stats -->
    <div style="margin-bottom: 28px;">
        <h3 style="font-size: 16px; font-weight: 800; color: #374151; margin-bottom: 14px; text-transform: uppercase; letter-spacing: 0.5px;">📦 Inventory &amp; Order Pipeline</h3>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon products-icon">📦</div>
                <div class="stat-info">
                    <p>Total Products / Units</p>
                    <h2>{{ $totalProducts }} <span style="font-size: 14px; color: #6b7280; font-weight: normal;">({{ $totalStockQty }} pcs)</span></h2>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon categories-icon">💰</div>
                <div class="stat-info">
                    <p>Inventory Valuation</p>
                    <h2>৳{{ number_format($inventoryValuation, 2) }}</h2>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon customers-icon">👥</div>
                <div class="stat-info">
                    <p>Total Customers</p>
                    <h2>{{ $totalCustomers }} <span style="font-size: 13px; color: #10b981; font-weight: normal;">(+{{ $newCustomersCount }} new)</span></h2>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon admins-icon">🛒</div>
                <div class="stat-info">
                    <p>Total Orders</p>
                    <h2>{{ $totalOrders }} <span style="font-size: 13px; color: #f59e0b; font-weight: normal;">({{ $pendingOrders }} pending)</span></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Monthly Sales Visual Chart -->
    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 14px; padding: 24px; margin-bottom: 30px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div>
                <h2 style="font-size: 18px; font-weight: 800; margin: 0; color: #111827;">Monthly Sales Revenue ({{ now()->year }})</h2>
                <p style="margin: 4px 0 0; color: #6b7280; font-size: 13px;">Real sales revenue generated across all storefront and admin orders</p>
            </div>
            <span style="background: #eff6ff; color: #2563eb; font-weight: 700; font-size: 12px; padding: 4px 10px; border-radius: 6px;">BDT (৳) Currency</span>
        </div>

        @php
            $maxBarValue = max(array_merge([1000], $chartData));
        @endphp
        <div style="display: flex; align-items: flex-end; gap: 14px; height: 180px; padding-top: 20px; border-bottom: 1px solid #e5e7eb;">
            @foreach($chartLabels as $index => $label)
                @php
                    $val = $chartData[$index] ?? 0;
                    $heightPercent = $maxBarValue > 0 ? max(5, round(($val / $maxBarValue) * 100)) : 5;
                @endphp
                <div style="flex: 1; display: flex; flex-direction: column; align-items: center; height: 100%; justify-content: flex-end;">
                    <span style="font-size: 11px; font-weight: 700; color: #4b5563; margin-bottom: 4px;">{{ $val > 0 ? '৳' . number_format($val) : '-' }}</span>
                    <div style="width: 100%; max-width: 38px; height: {{ $heightPercent }}%; background: {{ $val > 0 ? 'linear-gradient(180deg, #3b82f6, #1d4ed8)' : '#e2e8f0' }}; border-radius: 6px 6px 0 0; transition: height 0.3s ease;"></div>
                    <span style="font-size: 12px; font-weight: 600; color: #6b7280; margin-top: 8px;">{{ $label }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- 4. Main Two Column Grid: Low Stock Alert & Recent Orders -->
    <div class="dashboard-main-grid">
        <!-- Low Stock Alert Box -->
        <div class="dashboard-box">
            <div class="box-header">
                <div>
                    <h2>Low Stock Warnings</h2>
                    <p>Products with inventory below minimum safety threshold (<= 5)</p>
                </div>
                <span class="warning-badge">{{ $lowStockProducts->count() }} Alert{{ $lowStockProducts->count() != 1 ? 's' : '' }}</span>
            </div>

            <div class="table-wrapper">
                @if($lowStockProducts->isEmpty())
                    <p class="empty-message">All products are healthy and adequately stocked!</p>
                @else
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Stock Qty</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lowStockProducts as $product)
                                <tr>
                                    <td>
                                        <strong>{{ $product->name }}</strong>
                                        @if($product->sku)
                                            <div style="font-size: 11px; color: #9ca3af;">SKU: {{ $product->sku }}</div>
                                        @endif
                                    </td>
                                    <td>{{ $product->category->name ?? 'None' }}</td>
                                    <td>
                                        <span class="{{ $product->qty <= 0 ? 'stock-danger' : 'stock-warning' }}">
                                            {{ $product->qty }} remaining
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('product.edit', $product) }}" style="color: #2563eb; font-weight: 700; font-size: 13px;">Restock &rarr;</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        <!-- Recent Orders Box with Online Store vs Admin Badge -->
        <div class="dashboard-box">
            <div class="box-header">
                <div>
                    <h2>Recent Orders</h2>
                    <p>Real-time customer online orders &amp; staff created orders</p>
                </div>
                <a href="{{ route('order.index') }}" style="color: #2563eb; font-weight: 700; font-size: 13px;">View All &rarr;</a>
            </div>

            <div class="table-wrapper">
                @if($recentOrders->isEmpty())
                    <p class="empty-message">No orders placed yet.</p>
                @else
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Source</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentOrders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('order.show', $order) }}" style="font-weight: 700; color: #2563eb;">
                                            {{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td>
                                        <span style="font-size: 11px; font-weight: 700; padding: 2px 6px; border-radius: 4px; text-transform: uppercase; background: {{ $order->order_source === 'online' ? '#eff6ff; color:#1e40af;' : '#f5f3ff; color:#6d28d9;' }}">
                                            {{ $order->order_source ?? 'Admin' }}
                                        </span>
                                    </td>
                                    <td>{{ $order->user->name ?? $order->customer->name ?? 'Walk-in' }}</td>
                                    <td><strong>৳{{ number_format($order->total_amount, 2) }}</strong></td>
                                    <td>
                                        <span style="font-size: 12px; font-weight: 700; padding: 2px 6px; border-radius: 4px; text-transform: capitalize; background: {{ $order->status === 'delivered' ? '#ecfdf5; color:#065f46;' : ($order->status === 'cancelled' ? '#fef2f2; color:#991b1b;' : '#fffbeb; color:#92400e;') }}">
                                            {{ $order->status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>

    <!-- 5. Recent Stock Movements Audit Trail -->
    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 14px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <div>
                <h3 style="font-size: 17px; font-weight: 800; margin: 0; color: #111827;">Recent Stock Movements</h3>
                <p style="margin: 4px 0 0; color: #6b7280; font-size: 13px;">Full traceable inventory history of sales, purchases, adjustments, and cancellations</p>
            </div>
            <a href="{{ route('inventory.movements') }}" style="color: #2563eb; font-weight: 700; font-size: 13px;">View Full Audit Log &rarr;</a>
        </div>

        @if($recentStockMovements->isEmpty())
            <p class="empty-message">No stock movements recorded yet.</p>
        @else
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Date &amp; Time</th>
                        <th>Product</th>
                        <th>Type</th>
                        <th>Quantity</th>
                        <th>Stock (Prev &rarr; New)</th>
                        <th>User / Ref</th>
                        <th>Reason</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentStockMovements as $move)
                        <tr>
                            <td style="color: #6b7280; font-size: 12px;">{{ $move->created_at->format('M d, Y h:i A') }}</td>
                            <td><strong>{{ $move->product->name ?? 'Product' }}</strong></td>
                            <td>
                                <span style="font-size: 11px; font-weight: 700; padding: 2px 6px; border-radius: 4px; text-transform: uppercase; background: {{ $move->quantity > 0 ? '#ecfdf5; color:#065f46;' : '#fef2f2; color:#991b1b;' }}">
                                    {{ $move->type }}
                                </span>
                            </td>
                            <td>
                                <strong style="color: {{ $move->quantity > 0 ? '#10b981' : '#ef4444' }};">
                                    {{ $move->quantity > 0 ? '+' : '' }}{{ $move->quantity }}
                                </strong>
                            </td>
                            <td>{{ $move->previous_stock }} &rarr; <strong>{{ $move->new_stock }}</strong></td>
                            <td style="font-size: 13px;">{{ $move->user_name ?? 'System' }} ({{ $move->reference_type ?? 'N/A' }})</td>
                            <td style="font-size: 13px; color: #4b5563;">{{ $move->reason }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

</div>
@endsection
