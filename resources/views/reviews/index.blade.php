@extends('layouts.app')

@section('title', 'Customer Reviews Moderation')

@section('content')
<div class="container" style="max-width: 1400px;">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h1 style="text-align: left; margin: 0;">Customer Reviews Moderation</h1>
            <p style="color: #6b7280; font-size: 14px; margin-top: 4px;">Review, approve, and moderate customer product feedback and ratings before publishing.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="success-message">{{ session('success') }}</div>
    @endif

    <!-- Filter Bar -->
    <div class="table-container" style="padding: 16px 20px; margin-bottom: 24px; background: #f8fafc;">
        <form method="GET" action="{{ route('reviews.index') }}" style="display: flex; gap: 16px; align-items: flex-end;">
            <div>
                <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 4px;">Status Filter</label>
                <select name="status" style="padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;">
                    <option value="">All Reviews</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending Moderation</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved (Live)</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected / Hidden</option>
                </select>
            </div>

            <button type="submit" style="padding: 9px 20px; background: #2563eb; color: #fff; border: none; border-radius: 6px; font-weight: 700; cursor: pointer;">
                Filter
            </button>
            <a href="{{ route('reviews.index') }}" style="padding: 9px 16px; background: #e2e8f0; color: #334155; border-radius: 6px; font-weight: 600; text-decoration: none;">
                Reset
            </a>
        </form>
    </div>

    <!-- Reviews Table -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Customer</th>
                    <th>Rating</th>
                    <th>Review Content</th>
                    <th>Verified Purchase</th>
                    <th>Status</th>
                    <th>Submitted At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviews as $rev)
                    <tr>
                        <td style="max-width: 200px;">
                            <strong>{{ $rev->product->name ?? 'Product #' . $rev->product_id }}</strong>
                            @if($rev->product)
                                <div style="font-size: 12px;"><a href="{{ route('storefront.product', $rev->product->slug ?? $rev->product->id) }}" target="_blank" style="color: #2563eb;">View on Store</a></div>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $rev->user->name ?? 'Customer' }}</strong>
                            <div style="font-size: 11px; color: #64748b;">{{ $rev->user->email ?? '' }}</div>
                        </td>
                        <td>
                            <div style="color: #f59e0b; font-size: 15px; letter-spacing: 2px;">
                                @for($i = 1; $i <= 5; $i++)
                                    {{ $i <= $rev->rating ? '★' : '☆' }}
                                @endfor
                            </div>
                            <span style="font-size: 11px; font-weight: 700; color: #475569;">{{ $rev->rating }} / 5</span>
                        </td>
                        <td style="max-width: 320px;">
                            @if($rev->title)
                                <strong style="display: block; font-size: 13px; margin-bottom: 2px;">{{ $rev->title }}</strong>
                            @endif
                            <p style="font-size: 13px; color: #334155; margin: 0; line-height: 1.4;">{{ $rev->comment }}</p>
                        </td>
                        <td>
                            @if($rev->is_verified_purchase)
                                <span style="background: #ecfdf5; color: #047857; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 700;">Verified</span>
                            @else
                                <span style="color: #94a3b8; font-size: 12px;">Unverified</span>
                            @endif
                        </td>
                        <td>
                            <span class="status {{ $rev->status === 'approved' ? 'active' : ($rev->status === 'rejected' ? 'inactive' : '') }}">
                                {{ ucfirst($rev->status) }}
                            </span>
                        </td>
                        <td style="font-size: 12px; color: #64748b; white-space: nowrap;">
                            {{ $rev->created_at->format('M d, Y') }}
                        </td>
                        <td>
                            <div style="display: flex; gap: 6px; align-items: center;">
                                @if($rev->status !== 'approved')
                                    <form action="{{ route('reviews.status', $rev) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" style="padding: 4px 8px; background: #10b981; color: white; border: none; border-radius: 4px; font-size: 12px; font-weight: 700; cursor: pointer;">Approve</button>
                                    </form>
                                @endif

                                @if($rev->status !== 'rejected')
                                    <form action="{{ route('reviews.status', $rev) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" style="padding: 4px 8px; background: #f59e0b; color: white; border: none; border-radius: 4px; font-size: 12px; font-weight: 700; cursor: pointer;">Reject</button>
                                    </form>
                                @endif

                                <form action="{{ route('reviews.destroy', $rev) }}" method="POST" onsubmit="return confirm('Permanently delete this review?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="padding: 4px 8px; background: #ef4444; color: white; border: none; border-radius: 4px; font-size: 12px; font-weight: 700; cursor: pointer;">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="empty-message">No product reviews submitted yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $reviews->links() }}
        </div>
    </div>

</div>
@endsection
