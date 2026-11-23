@extends('layouts.app')

@section('title', 'Stock Movements Audit Log')

@section('content')
<div class="container" style="max-width: 1380px;">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 14px;">
        <div>
            <h1 style="text-align: left; margin: 0;">Stock Movements Audit Trail</h1>
            <p style="color: #6b7280; font-size: 14px; margin-top: 4px;">Traceable log of every single stock addition, deduction, customer order, cancellation, and manual adjustment.</p>
        </div>
        <a href="{{ route('inventory.index') }}" class="create-button" style="margin-bottom: 0;">&larr; Back to Inventory</a>
    </div>

    <!-- Filters -->
    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 18px; margin-bottom: 24px;">
        <form action="{{ route('inventory.movements') }}" method="GET" style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr auto; gap: 12px; align-items: end;">
            <div>
                <label style="display: block; font-size: 12px; font-weight: 700; color: #4b5563; margin-bottom: 4px;">Filter by Product</label>
                <select name="product_id" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; background: white;">
                    <option value="">All Products</option>
                    @foreach($products as $prod)
                        <option value="{{ $prod->id }}" {{ request('product_id') == $prod->id ? 'selected' : '' }}>{{ $prod->name }} ({{ $prod->sku ?? 'No SKU' }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 12px; font-weight: 700; color: #4b5563; margin-bottom: 4px;">Movement Type</label>
                <select name="type" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; background: white;">
                    <option value="">All Types</option>
                    <option value="sale" {{ request('type') === 'sale' ? 'selected' : '' }}>Sale / Order</option>
                    <option value="purchase" {{ request('type') === 'purchase' ? 'selected' : '' }}>Purchase Received</option>
                    <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>Manual Adjustment</option>
                    <option value="cancellation" {{ request('type') === 'cancellation' ? 'selected' : '' }}>Order Cancellation</option>
                    <option value="initial" {{ request('type') === 'initial' ? 'selected' : '' }}>Initial Stock</option>
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 12px; font-weight: 700; color: #4b5563; margin-bottom: 4px;">From Date</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" style="width: 100%; padding: 7px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px;">
            </div>
            <div>
                <label style="display: block; font-size: 12px; font-weight: 700; color: #4b5563; margin-bottom: 4px;">To Date</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" style="width: 100%; padding: 7px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px;">
            </div>
            <div style="display: flex; gap: 6px;">
                <button type="submit" style="padding: 8px 16px; background-color: #2563eb; color: white; border: none; border-radius: 6px; font-weight: 700; font-size: 13px; cursor: pointer;">Filter</button>
                <a href="{{ route('inventory.movements') }}" style="padding: 8px 12px; background-color: #f3f4f6; color: #4b5563; border-radius: 6px; font-weight: 600; font-size: 13px; text-decoration: none;">Reset</a>
            </div>
        </form>
    </div>

    <!-- Movement Table -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>Product</th>
                    <th>Movement Type</th>
                    <th>Quantity Change</th>
                    <th>Previous Stock</th>
                    <th>New Stock</th>
                    <th>Reference</th>
                    <th>User Responsible</th>
                    <th>Reason</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $m)
                    <tr>
                        <td style="color: #6b7280; font-size: 13px;">{{ $m->created_at->format('d M Y, h:i A') }}</td>
                        <td>
                            <strong>{{ $m->product->name ?? 'Product Deleted' }}</strong>
                            @if($m->product && $m->product->sku)
                                <div style="font-size: 11px; color: #9ca3af;">SKU: {{ $m->product->sku }}</div>
                            @endif
                        </td>
                        <td>
                            <span style="font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 4px; text-transform: uppercase; background: {{ in_array($m->type, ['sale']) ? '#eff6ff; color:#1e40af;' : (in_array($m->type, ['purchase', 'initial', 'cancellation']) ? '#ecfdf5; color:#065f46;' : '#fef3c7; color:#92400e;') }}">
                                {{ $m->type }}
                            </span>
                        </td>
                        <td>
                            <strong style="color: {{ $m->quantity > 0 ? '#10b981' : '#ef4444' }};">
                                {{ $m->quantity > 0 ? '+' : '' }}{{ $m->quantity }}
                            </strong>
                        </td>
                        <td>{{ $m->previous_stock }}</td>
                        <td><strong>{{ $m->new_stock }}</strong></td>
                        <td><small>{{ $m->reference_type ?? 'N/A' }} #{{ $m->reference_id ?? '' }}</small></td>
                        <td><small>{{ $m->user_name ?? ($m->user->name ?? 'System') }}</small></td>
                        <td style="font-size: 13px; color: #4b5563;">{{ $m->reason }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="empty-message">No stock movements found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $movements->links() }}
        </div>
    </div>

</div>
@endsection
