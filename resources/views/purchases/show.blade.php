@extends('layouts.app')

@section('title', 'Purchase Order ' . $purchase->purchase_number)

@section('content')
<div class="container" style="max-width: 1000px;">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
        <div>
            <h1 style="text-align: left; margin: 0;">Purchase Order #{{ $purchase->purchase_number }}</h1>
            <p style="color: #6b7280; font-size: 14px; margin-top: 4px;">Created on {{ $purchase->created_at->format('d M Y') }} by {{ $purchase->created_by ?? 'Admin' }}</p>
        </div>

        @if($purchase->status !== 'received')
            <form action="{{ route('purchases.receive', $purchase) }}" method="POST" onsubmit="return confirm('Confirm receipt of these goods? This will automatically increment product stock and record stock movements.');">
                @csrf
                <button type="submit" style="background: #10b981; color: white; padding: 10px 18px; border: none; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer;">
                    ✓ Mark Goods as Received (Update Stock)
                </button>
            </form>
        @else
            <span style="background: #ecfdf5; color: #065f46; padding: 8px 16px; border-radius: 8px; font-weight: 700; font-size: 14px;">
                ✓ Goods Received on {{ $purchase->received_date ? $purchase->received_date->format('d M Y') : 'N/A' }}
            </span>
        @endif
    </div>

    @if (session('success'))
        <div class="success-message">{{ session('success') }}</div>
    @endif

    <div class="dashboard-main-grid" style="margin-bottom: 24px;">
        <div class="dashboard-box">
            <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 12px;">Supplier Information</h3>
            <p><strong>Name:</strong> {{ $purchase->supplier->name }}</p>
            <p><strong>Company:</strong> {{ $purchase->supplier->company_name ?? '—' }}</p>
            <p><strong>Phone:</strong> {{ $purchase->supplier->phone ?? '—' }}</p>
            <p><strong>Email:</strong> {{ $purchase->supplier->email ?? '—' }}</p>
        </div>

        <div class="dashboard-box">
            <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 12px;">Order Summary</h3>
            <p><strong>Status:</strong> <span style="text-transform: capitalize; font-weight: 700;">{{ $purchase->status }}</span></p>
            <p><strong>Date:</strong> {{ $purchase->purchase_date->format('d M Y') }}</p>
            <p><strong>Total Amount:</strong> <strong style="font-size: 18px; color: #2563eb;">৳{{ number_format($purchase->total_amount, 2) }}</strong></p>
            <p><strong>Notes:</strong> {{ $purchase->notes ?? 'None' }}</p>
        </div>
    </div>

    <!-- Items Table -->
    <div class="table-container" style="margin-bottom: 24px;">
        <h2>Purchased Products</h2>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Ordered Qty</th>
                    <th>Unit Cost</th>
                    <th>Subtotal</th>
                    <th>Received Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchase->items as $item)
                    <tr>
                        <td><strong>{{ $item->product->name ?? 'Product Deleted' }}</strong></td>
                        <td>{{ $item->quantity }} units</td>
                        <td>৳{{ number_format($item->unit_cost, 2) }}</td>
                        <td><strong>৳{{ number_format($item->subtotal, 2) }}</strong></td>
                        <td>
                            @if($purchase->status === 'received')
                                <span style="color: #10b981; font-weight: 700;">✓ {{ $item->quantity }} received</span>
                            @else
                                <span style="color: #f59e0b;">Pending</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <a href="{{ route('purchases.index') }}" class="back-link">← Back to Purchases</a>
</div>
@endsection
