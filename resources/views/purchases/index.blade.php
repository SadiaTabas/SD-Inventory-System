@extends('layouts.app')

@section('title', 'Purchase Management')

@section('content')
<div class="container" style="max-width: 1280px;">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h1 style="text-align: left; margin: 0;">Purchase Orders</h1>
            <p style="color: #6b7280; font-size: 14px; margin-top: 4px;">Track supplier purchase orders, cost prices, and received incoming stock.</p>
        </div>
        <a href="{{ route('purchases.create') }}" class="create-button" style="margin-bottom: 0;">+ New Purchase Order</a>
    </div>

    @if (session('success'))
        <div class="success-message">{{ session('success') }}</div>
    @endif

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>PO Number</th>
                    <th>Supplier</th>
                    <th>Purchase Date</th>
                    <th>Total Amount</th>
                    <th>Items</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchases as $p)
                    <tr>
                        <td>
                            <a href="{{ route('purchases.show', $p) }}" style="font-weight: 700; color: #2563eb;">{{ $p->purchase_number }}</a>
                        </td>
                        <td><strong>{{ $p->supplier->name ?? 'Supplier Deleted' }}</strong></td>
                        <td>{{ $p->purchase_date->format('d M Y') }}</td>
                        <td><strong>৳{{ number_format($p->total_amount, 2) }}</strong></td>
                        <td>{{ $p->items->count() }} Items ({{ $p->items->sum('quantity') }} pcs)</td>
                        <td>
                            <span class="status {{ $p->status === 'received' ? 'active' : ($p->status === 'cancelled' ? 'inactive' : '') }}" style="background: {{ $p->status === 'received' ? '#ecfdf5; color:#065f46;' : '#fffbeb; color:#92400e;' }}">
                                {{ ucfirst(str_replace('_', ' ', $p->status)) }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('purchases.show', $p) }}" class="view-button">View Details</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-message">No purchase orders created yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $purchases->links() }}
        </div>
    </div>

</div>
@endsection
