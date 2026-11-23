@extends('layouts.app')

@section('title', 'Centralized Payment Management')

@section('content')
<div class="container" style="max-width: 1380px;">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 14px;">
        <div>
            <h1 style="text-align: left; margin: 0;">Payment Management</h1>
            <p style="color: #6b7280; font-size: 14px; margin-top: 4px;">Track all incoming payment transactions, verify bKash/Nagad mobile payments, and monitor cash flow.</p>
        </div>
        <div style="display: flex; gap: 14px;">
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 10px 18px;">
                <span style="font-size: 11px; font-weight: 700; color: #6b7280; text-transform: uppercase;">Total Verified Collected</span>
                <div style="font-size: 20px; font-weight: 800; color: #10b981;">৳{{ number_format($totalCollected, 2) }}</div>
            </div>
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 10px 18px;">
                <span style="font-size: 11px; font-weight: 700; color: #6b7280; text-transform: uppercase;">Pending Verification</span>
                <div style="font-size: 20px; font-weight: 800; color: #f59e0b;">{{ $pendingVerificationCount }} Transactions</div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="success-message">{{ session('success') }}</div>
    @endif

    <!-- Filters -->
    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 18px; margin-bottom: 24px;">
        <form action="{{ route('payments.index') }}" method="GET" style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr 1fr auto; gap: 12px; align-items: end;">
            <div>
                <label style="display: block; font-size: 12px; font-weight: 700; color: #4b5563; margin-bottom: 4px;">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Transaction ID, Order #..." style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
            </div>
            <div>
                <label style="display: block; font-size: 12px; font-weight: 700; color: #4b5563; margin-bottom: 4px;">Method</label>
                <select name="payment_method" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; background: white;">
                    <option value="">All Methods</option>
                    <option value="cod" {{ request('payment_method') === 'cod' ? 'selected' : '' }}>Cash on Delivery</option>
                    <option value="bkash" {{ request('payment_method') === 'bkash' ? 'selected' : '' }}>bKash</option>
                    <option value="nagad" {{ request('payment_method') === 'nagad' ? 'selected' : '' }}>Nagad</option>
                    <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Direct Cash</option>
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 12px; font-weight: 700; color: #4b5563; margin-bottom: 4px;">Verification</label>
                <select name="verification_status" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; background: white;">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('verification_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="verified" {{ request('verification_status') === 'verified' ? 'selected' : '' }}>Verified</option>
                    <option value="rejected" {{ request('verification_status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
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
                <a href="{{ route('payments.index') }}" style="padding: 8px 12px; background-color: #f3f4f6; color: #4b5563; border-radius: 6px; font-weight: 600; font-size: 13px; text-decoration: none;">Reset</a>
            </div>
        </form>
    </div>

    <!-- Payments Table -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Method</th>
                    <th>Trx Reference</th>
                    <th>Amount</th>
                    <th>Verification</th>
                    <th>Notes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $p)
                    <tr>
                        <td style="color: #6b7280; font-size: 13px;">{{ $p->payment_date ? \Carbon\Carbon::parse($p->payment_date)->format('d M Y') : $p->created_at->format('d M Y') }}</td>
                        <td>
                            @if($p->order)
                                <a href="{{ route('order.show', $p->order) }}" style="font-weight: 700; color: #2563eb;">{{ $p->order->order_number }}</a>
                            @else
                                <span style="color: #9ca3af;">—</span>
                            @endif
                        </td>
                        <td>{{ $p->order->user->name ?? ($p->order->customer->name ?? 'Customer') }}</td>
                        <td><strong style="text-transform: uppercase;">{{ $p->payment_method }}</strong></td>
                        <td>
                            <code style="background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-weight: 700;">{{ $p->transaction_id ?? '—' }}</code>
                        </td>
                        <td><strong style="color: #10b981; font-size: 15px;">৳{{ number_format($p->amount, 2) }}</strong></td>
                        <td>
                            <span style="font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 4px; text-transform: uppercase; background: {{ $p->verification_status === 'verified' ? '#ecfdf5; color:#065f46;' : ($p->verification_status === 'rejected' ? '#fef2f2; color:#991b1b;' : '#fffbeb; color:#92400e;') }}">
                                {{ $p->verification_status }}
                            </span>
                        </td>
                        <td style="font-size: 13px; color: #6b7280; max-width: 200px;">{{ $p->note ?? '—' }}</td>
                        <td>
                            @if($p->verification_status === 'pending')
                                <div style="display: flex; gap: 6px;">
                                    <form action="{{ route('payments.verify', $p) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="action" value="verify">
                                        <button type="submit" style="background: #10b981; color: white; border: none; border-radius: 4px; padding: 4px 8px; font-size: 12px; font-weight: 700; cursor: pointer;">
                                            ✓ Verify
                                        </button>
                                    </form>
                                    <form action="{{ route('payments.verify', $p) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" style="background: #ef4444; color: white; border: none; border-radius: 4px; padding: 4px 8px; font-size: 12px; font-weight: 700; cursor: pointer;" onclick="return confirm('Reject this transaction reference?');">
                                            ✕ Reject
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span style="font-size: 12px; color: #9ca3af;">Processed</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="empty-message">No payment transactions recorded.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $payments->links() }}
        </div>
    </div>

</div>
@endsection
