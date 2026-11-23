@extends('layouts.app')

@section('title', 'Product Inventory')

@section('content')
<div class="container" style="max-width: 1380px;">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 14px;">
        <div>
            <h1 style="text-align: left; margin: 0;">Product Inventory Management</h1>
            <p style="color: #6b7280; font-size: 14px; margin-top: 4px;">Real-time stock levels, low-stock warnings, inventory valuation, and stock adjustments.</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('inventory.movements') }}" class="create-button" style="background-color: #6366f1; margin-bottom: 0;">📜 Stock Movement Log</a>
            <a href="{{ route('product.create') }}" class="create-button" style="margin-bottom: 0;">+ New Product</a>
        </div>
    </div>

    @if (session('success'))
        <div class="success-message">{{ session('success') }}</div>
    @endif

    <!-- Inventory Stats Cards -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;">
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px;">
            <span style="font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase;">Total Stock Count</span>
            <div style="font-size: 26px; font-weight: 800; color: #1e3a8a; margin-top: 4px;">{{ $totalStockQty }} units</div>
        </div>
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px;">
            <span style="font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase;">Low Stock Warning</span>
            <div style="font-size: 26px; font-weight: 800; color: #f59e0b; margin-top: 4px;">{{ $lowStockCount }} items</div>
        </div>
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px;">
            <span style="font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase;">Out of Stock</span>
            <div style="font-size: 26px; font-weight: 800; color: #ef4444; margin-top: 4px;">{{ $outOfStockCount }} items</div>
        </div>
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px;">
            <span style="font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase;">Inventory Valuation</span>
            <div style="font-size: 26px; font-weight: 800; color: #10b981; margin-top: 4px;">৳{{ number_format($inventoryValue, 2) }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 18px; margin-bottom: 24px;">
        <form action="{{ route('inventory.index') }}" method="GET" style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 12px; align-items: end;">
            <div>
                <label style="display: block; font-size: 12px; font-weight: 700; color: #4b5563; margin-bottom: 4px;">Search Product</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, SKU, Barcode..." style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
            </div>
            <div>
                <label style="display: block; font-size: 12px; font-weight: 700; color: #4b5563; margin-bottom: 4px;">Category</label>
                <select name="category_id" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; background: white;">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 12px; font-weight: 700; color: #4b5563; margin-bottom: 4px;">Stock Status</label>
                <select name="stock_status" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; background: white;">
                    <option value="">All Stock</option>
                    <option value="in_stock" {{ request('stock_status') === 'in_stock' ? 'selected' : '' }}>In Stock</option>
                    <option value="low_stock" {{ request('stock_status') === 'low_stock' ? 'selected' : '' }}>Low Stock (<= min)</option>
                    <option value="out_of_stock" {{ request('stock_status') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                </select>
            </div>
            <div style="display: flex; gap: 6px;">
                <button type="submit" style="padding: 8px 16px; background-color: #2563eb; color: white; border: none; border-radius: 6px; font-weight: 700; font-size: 13px; cursor: pointer;">Filter</button>
                <a href="{{ route('inventory.index') }}" style="padding: 8px 12px; background-color: #f3f4f6; color: #4b5563; border-radius: 6px; font-weight: 600; font-size: 13px; text-decoration: none;">Reset</a>
            </div>
        </form>
    </div>

    <!-- Inventory Table -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Product Details</th>
                    <th>Category</th>
                    <th>Cost Price</th>
                    <th>Selling Price</th>
                    <th>Current Stock</th>
                    <th>Safety Min</th>
                    <th>Stock Value</th>
                    <th>Quick Adjust</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $p)
                    <tr>
                        <td>
                            <strong>{{ $p->name }}</strong>
                            <div style="font-size: 12px; color: #6b7280;">SKU: {{ $p->sku ?? '—' }} | Barcode: {{ $p->barcode ?? '—' }}</div>
                        </td>
                        <td>{{ $p->category->name ?? 'None' }}</td>
                        <td>৳{{ number_format($p->cost_price, 2) }}</td>
                        <td><strong>৳{{ number_format($p->price, 2) }}</strong></td>
                        <td>
                            @if($p->qty <= 0)
                                <span class="stock-danger">0 Out of Stock</span>
                            @elseif($p->qty <= $p->min_stock_level)
                                <span class="stock-warning">{{ $p->qty }} Low Stock</span>
                            @else
                                <span class="status active">{{ $p->qty }} In Stock</span>
                            @endif
                        </td>
                        <td>{{ $p->min_stock_level }}</td>
                        <td><strong>৳{{ number_format($p->qty * ($p->cost_price > 0 ? $p->cost_price : $p->price), 2) }}</strong></td>
                        <td>
                            <form action="{{ route('inventory.adjust', $p) }}" method="POST" style="display: flex; gap: 6px; align-items: center;">
                                @csrf
                                <select name="adjustment_type" style="padding: 4px; font-size: 12px; border: 1px solid #d1d5db; border-radius: 4px;">
                                    <option value="add">+ Add</option>
                                    <option value="subtract">- Deduct</option>
                                </select>
                                <input type="number" name="quantity" min="1" placeholder="Qty" required style="width: 60px; padding: 4px; font-size: 12px; border: 1px solid #d1d5db; border-radius: 4px;">
                                <input type="text" name="reason" placeholder="Reason..." required style="width: 100px; padding: 4px; font-size: 12px; border: 1px solid #d1d5db; border-radius: 4px;">
                                <button type="submit" style="padding: 4px 8px; background: #3b82f6; color: white; border: none; border-radius: 4px; font-size: 12px; font-weight: 700; cursor: pointer;">Save</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="empty-message">No inventory records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $products->links() }}
        </div>
    </div>

</div>
@endsection
