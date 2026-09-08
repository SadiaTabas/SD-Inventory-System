@extends('layouts.app')

@section('title', 'Order Details')

@section('content')

<div class="container">

    @if (session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="error-box">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="top-actions" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 style="margin: 0;">Order: {{ $order->order_number ?? ('#' . $order->id) }}</h1>
        
        <div style="display: flex; gap: 10px; align-items: center;">

           @if (($order->due_amount ?? 0) > 0)
                <button
                    type="button"
                    onclick="openPaymentModal()"
                    style="background-color: #3b82f6; color: #fff; padding: 10px 16px; border-radius: 6px; border: none; font-size: 14px; font-weight: bold; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; height: 38px; box-sizing: border-box; line-height: 1;"
                >
                    + {{ ($order->paid_amount ?? 0) > 0 ? 'Record Partial Payment' : 'Record Advance Payment' }}
                </button>
            @endif

            <a
                href="{{ route('order.invoice', $order) }}"
                style="background-color: #27ae60; color: #fff; padding: 10px 16px; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: bold; display: inline-flex; align-items: center; justify-content: center; height: 38px; box-sizing: border-box; line-height: 1;"
            >
                📥 Download Invoice
            </a>

            <a
                class="edit-button"
                href="{{ route('order.edit', $order) }}"
                style="display: inline-flex; align-items: center; justify-content: center; height: 38px; box-sizing: border-box; line-height: 1; padding: 10px 16px;"
            >
                Edit Order
            </a>

        </div>
    </div>
    <div class="dashboard-main-grid" style="margin-bottom: 25px;">

        {{-- Customer Details --}}
        <div class="dashboard-box">

            <div class="box-header">
                <h2>Customer Details</h2>
            </div>

            <p><strong>Name:</strong> {{ $order->user->name ?? ($order->customer->name ?? 'User Deleted') }}</p>
            <p><strong>Email:</strong> {{ $order->user->email ?? ($order->customer->email ?? 'N/A') }}</p>
            <p><strong>Phone:</strong> {{ $order->user->phone ?? ($order->customer->phone ?? 'N/A') }}</p>
            <p><strong>Shipping Address:</strong> {{ $order->shipping_address }}</p>
            <p><strong>Delivery Slot:</strong> {{ $order->delivery_slot ?? 'Not specified' }}</p>
            <p><strong>Order Source:</strong> {{ ucfirst($order->order_placed_by ?? 'Admin') }} (Audit: {{ $order->created_by ?? 'System' }})</p>

        </div>

        {{-- Logistics & Financial Breakdown --}}
        <div class="dashboard-box">

            <div class="box-header">
                <h2>Logistics & Payment</h2>
            </div>

            <p><strong>Assigned Rider:</strong> {{ $order->deliveryAgent->name ?? ($order->delivery_man_name ?? 'Unassigned') }}</p>
            <p><strong>Rider Contact:</strong> {{ $order->deliveryAgent->phone ?? ($order->delivery_man_phone ?? 'N/A') }}</p>

            <p>
                <strong>Delivery Status:</strong>
                <span class="status {{ $order->delivery_status === 'delivered' ? 'active' : 'inactive' }}">
                    {{ ucfirst(str_replace('_', ' ', $order->delivery_status)) }}
                </span>
            </p>

            <p>
                <strong>Payment Status:</strong>
                <span class="status {{ $order->payment_status === 'paid' ? 'active' : 'inactive' }}">
                    {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}
                </span>
                ({{ strtoupper($order->payment_method ?? 'COD') }})
            </p>

            <p><strong>Delivery Notes:</strong> {{ $order->delivery_notes ?? 'None' }}</p>
            
            <div style="border-top: 1px dashed #cbd5e1; margin-top: 12px; padding-top: 10px;">

                <p><strong>Subtotal:</strong> BDT{{ number_format($order->subtotal_amount > 0 ? $order->subtotal_amount : $order->total_amount, 2) }}</p>

                @if (($order->discount_amount ?? 0) > 0)
                    <p style="color: #e74c3c;">
                        <strong>Discount Applied:</strong> - BDT{{ number_format($order->discount_amount, 2) }}
                        @if ($order->coupon_code)
                            <small>({{ $order->coupon_code }})</small>
                        @endif
                    </p>
                @endif

                @php
                    $deliveryCharge = (float) (
                        $order->delivery_charge 
                        ?? ($order->total_amount - ($order->subtotal_amount ?? 0) + ($order->discount_amount ?? 0))
                    );
                    if ($deliveryCharge <= 0 && ((float)$order->total_amount > (float)($order->subtotal_amount ?? 0))) {
                        $deliveryCharge = (float)$order->total_amount - (float)($order->subtotal_amount ?? 0) + (float)($order->discount_amount ?? 0);
                    }
                @endphp

                @if ($deliveryCharge > 0)
                    <p style="color: #0284c7;">
                        <strong>Delivery Charge:</strong> + BDT{{ number_format($deliveryCharge, 2) }}
                    </p>
                @endif

                <p><strong>Total Amount:</strong> <strong>BDT{{ number_format($order->total_amount, 2) }}</strong></p>

                <p style="color: #27ae60;">
                    <strong>Paid Amount:</strong> <strong>BDT{{ number_format($order->paid_amount ?? 0, 2) }}</strong>
                </p>

                <p style="color: #e74c3c; font-size: 16px;">
                    <strong>Due Balance:</strong> <strong>BDT{{ number_format($order->due_amount ?? 0, 2) }}</strong>
                </p>

            </div>

        </div>

    </div>

    {{-- Order Items --}}
    <div class="table-container" style="margin-bottom: 25px;">

        <h2>Order Items</h2>

        <table>

            <thead>
                <tr>
                    <th>Product</th>
                    <th>Unit Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($order->items as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->product->name ?? 'Product Removed' }}</strong>
                        </td>
                        <td>BDT{{ number_format($item->price, 2) }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>BDT{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="empty-message">No item details found for this order.</td>
                    </tr>
                @endforelse
            </tbody>

        </table>

    </div>

    {{-- Payment Transactions History --}}
    <div class="table-container" style="margin-bottom: 25px;">

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <h2>Payment Transactions History</h2>
        </div>

        <table>

            <thead>
                <tr>
                    <th>Date</th>
                    <th>Payment Method</th>
                    <th>Transaction ID</th>
                    <th>Amount Paid</th>
                    <th>Note</th>
                    <th>Recorded By</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($order->payments as $payment)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M, Y') }}</td>
                        <td><strong>{{ strtoupper($payment->payment_method) }}</strong></td>
                        <td>{{ $payment->transaction_id ?? '—' }}</td>
                        <td style="color: #27ae60; font-weight: bold;">৳{{ number_format($payment->amount, 2) }}</td>
                        <td>{{ $payment->note ?? '—' }}</td>
                        <td><small>{{ $payment->recorded_by ?? 'System' }}</small></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-message">No payment transactions recorded yet.</td>
                    </tr>
                @endforelse
            </tbody>

        </table>

    </div>

    <a
        class="back-link"
        href="{{ route('order.index') }}"
    >
        ← Back to Orders
    </a>

</div>

{{-- Payment Modal --}}
<div
    id="paymentModal"
    style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center;"
>
    <div style="background: #fff; width: 100%; max-width: 480px; border-radius: 10px; padding: 24px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">

     <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h3 style="margin: 0;">{{ ($order->paid_amount ?? 0) > 0 ? 'Record Partial / Due Payment' : 'Record Advance Payment' }}</h3>
            <button
                type="button"
                onclick="closePaymentModal()"
                style="background: none; border: none; font-size: 22px; cursor: pointer;"
            >
                &times;
            </button>
        </div>

        <div style="background: #f8fafc; padding: 12px; border-radius: 6px; margin-bottom: 15px; border: 1px solid #e2e8f0;">
            <span>{{ ($order->paid_amount ?? 0) > 0 ? 'Current Due:' : 'Remaining Amount:' }}</span>
            <strong style="color: #e74c3c; font-size: 16px;">৳{{ number_format($order->due_amount ?? 0, 2) }}</strong>
        </div>

        <form
            method="POST"
            action="{{ route('order.payments.store', $order) }}"
        >
            @csrf

            <div class="form-group">
                <label for="amount">Payment Amount (৳)</label>
                <input
                    type="number"
                    step="0.01"
                    min="0.01"
                    max="{{ max(0.01, $order->due_amount ?? 0) }}"
                    id="amount"
                    name="amount"
                    value="{{ old('amount', $order->due_amount ?? 0) }}"
                    required
                >
            </div>

            <div class="form-group">
                <label for="modal_payment_method">Payment Method</label>
                <select id="modal_payment_method" name="payment_method" required>
                    <option value="cash">Cash</option>
                    <option value="bkash">bKash</option>
                    <option value="nagad">Nagad</option>
                    <option value="card">Card</option>
                    <option value="bank_transfer">Bank Transfer</option>
                </select>
            </div>

            <div class="form-group">
                <label for="modal_payment_date">Payment Date</label>
                <input
                    type="date"
                    id="modal_payment_date"
                    name="payment_date"
                    value="{{ date('Y-m-d') }}"
                    required
                >
            </div>

            <div class="form-group">
                <label for="modal_transaction_id">Transaction ID  (Optional)</label>
                <input
                    type="text"
                    id="modal_transaction_id"
                    name="transaction_id"
                    placeholder="e.g. Trx ID or Receipt #"
                >
            </div>

          <div class="form-group">
                <label for="modal_note">Payment Note</label>
                <input
                    type="text"
                    id="modal_note"
                    name="note"
                    value="{{ ($order->paid_amount ?? 0) > 0 ? 'Partial Payment' : 'Advance / Initial Payment' }}"
                    placeholder="e.g. Advance / Partial settlement"
                >
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 15px;">
                <button
                    type="button"
                    onclick="closePaymentModal()"
                    class="btn btn-secondary"
                    style="padding: 8px 16px; border-radius: 6px; border: 1px solid #cbd5e1; background: #fff; cursor: pointer;"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="primary-button"
                >
                    Confirm Payment
                </button>
            </div>

        </form>

    </div>
</div>

<script>
function openPaymentModal() {
    document.getElementById('paymentModal').style.display = 'flex';
}

function closePaymentModal() {
    document.getElementById('paymentModal').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('paymentModal');
    if (event.target === modal) {
        closePaymentModal();
    }
}
</script>

@endsection