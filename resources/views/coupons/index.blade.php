@extends('layouts.app')

@section('title', 'Manage Coupons')

@section('content')

<div class="container">

    <h1>Coupon & Discount Management</h1>

    @if (session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
    @endif

    <div class="top-actions">
        <a
            href="{{ route('coupons.create') }}"
            class="create-button"
        >
            + Create New Coupon
        </a>
    </div>

    <div class="table-container">

        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Type</th>
                    <th>Discount Value</th>
                    <th>Min Order Requirement</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($coupons as $coupon)
                    <tr>
                        <td>
                            <strong style="background: #e2e8f0; padding: 4px 8px; border-radius: 4px; font-family: monospace; font-size: 14px;">
                                {{ $coupon->code }}
                            </strong>
                        </td>
                        <td>{{ ucfirst($coupon->type) }}</td>
                        <td>
                            <strong>
                                {{ $coupon->type === 'percent' ? $coupon->value . '%' : '৳' . number_format($coupon->value, 2) }}
                            </strong>
                        </td>
                        <td>৳{{ number_format($coupon->min_order_amount, 2) }}</td>
                        <td>
                            <span class="status {{ $coupon->status ? 'active' : 'inactive' }}">
                                {{ $coupon->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons" style="display: flex; gap: 6px; align-items: center;">
                                <a
                                    class="edit-button"
                                    href="{{ route('coupons.edit', $coupon) }}"
                                >
                                    Edit
                                </a>

                                <form
                                    class="delete-form"
                                    method="POST"
                                    action="{{ route('coupons.destroy', $coupon) }}"
                                    onsubmit="return confirm('Are you sure you want to delete this coupon?');"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        class="delete-button"
                                        type="submit"
                                    >
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-message" style="text-align: center; padding: 20px;">
                            No promo coupons found. Click "+ Create New Coupon" to add one.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>

</div>

@endsection