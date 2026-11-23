@extends('layouts.app')

@section('title', 'Order Details')

@section('content')

<div class="container" style="max-width: 1200px;">

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

    <div class="top-actions" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
        <div>
            <h1 style="margin: 0; text-align: left;">Order: {{ $order->order_number ?? ('#' . $order->id) }}</h1>
            <span style="display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-top: 4px; background: {{ $order->order_source === 'online' ? '#eff6ff; color:#1e40af;' : '#f5f3ff; color:#6d28d9;' }}">
                Source: {{ $order->order_source ?? 'Admin' }}
            </span>
        </div>
        
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            @if (($order->due_amount ?? 0) > 0)
                <button
                    type="button"
                    onclick="openPaymentModal()"
                    style="background-color: #3b82f6; color: #fff; padding: 10px 16px; border-radius: 6px; border: none; font-size: 14px; font-weight: bold; cursor: pointer; height: 38px;"
                >
                    + Record Payment
                </button>
            @endif

            <a
                href="{{ route('order.invoice', $order) }}"
                style="background-color: #27ae60; color: #fff; padding: 10px 16px; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: bold; height: 38px; display: inline-flex; align-items: center;"
            >
                📥 Download Invoice
            </a>

            @php
                $hoursSinceCreation = $order->created_at->diffInHours(now());
                $isEditable = ($hoursSinceCreation <= 48) && !in_array($order->status, ['delivered', 'cancelled']);
            @endphp

            @if ($isEditable)
                <a
                    class="edit-button"
                    href="{{ route('order.edit', $order) }}"
                    style="height: 38px; display: inline-flex; align-items: center; padding: 10px 16px;"
                >
                    Edit Order
                </a>
            @endif
        </div>
    </div>

    <!-- Main Two Column Grid -->
    <div class="dashboard-main-grid" style="margin-bottom: 25px;">
        {{-- Customer & Logistics Details --}}
        <div class="dashboard-box">
            <div class="box-header">
                <h2>Customer &amp; Shipping</h2>
            </div>

            <p><strong>Customer Name:</strong> {{ $order->user->name ?? ($order->customer->name ?? 'Walk-in Customer') }}</p>
            <p><strong>Email:</strong> {{ $order->user->email ?? ($order->customer->email ?? 'N/A') }}</p>
            <p><strong>Phone:</strong> {{ $order->user->phone ?? ($order->customer->phone ?? 'N/A') }}</p>
            <p><strong>Shipping Address:</strong><br><span style="white-space: pre-line; color: #374151;">{{ $order->shipping_address }}</span></p>
            <p><strong>Delivery Slot:</strong> {{ $order->delivery_slot ?? 'Regular' }}</p>
            <p><strong>Delivery Notes:</strong> {{ $order->delivery_notes ?? 'None' }}</p>
            @if($order->admin_notes)
                <p><strong>Admin Internal Notes:</strong> <em style="color: #6b7280;">{{ $order->admin_notes }}</em></p>
            @endif
        </div>

        {{-- Financial & Logistics Status --}}
        <div class="dashboard-box">
            <div class="box-header">
                <h2>Logistics &amp; Payment Status</h2>
            </div>

            <p>
                <strong>Order Status:</strong>
                <span class="status {{ $order->status === 'delivered' ? 'active' : ($order->status === 'cancelled' ? 'inactive' : '') }}" style="background: {{ $order->status === 'cancelled' ? '#fef2f2; color:#991b1b;' : ($order->status === 'delivered' ? '#ecfdf5; color:#065f46;' : '#eff6ff; color:#1e40af;') }}">
                    {{ ucfirst($order->status) }}
                </span>
            </p>

            <p>
                <strong>Payment Status:</strong>
                <span class="status {{ $order->payment_status === 'paid' ? 'active' : 'inactive' }}">
                    {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}
                </span>
                ({{ strtoupper($order->payment_method ?? 'COD') }})
            </p>

            <p>
                <strong>Delivery Status:</strong>
                <span class="status {{ $order->delivery_status === 'delivered' ? 'active' : 'inactive' }}">
                    {{ ucfirst(str_replace('_', ' ', $order->delivery_status)) }}
                </span>
            </p>

            <p>
                <strong>Assigned Rider:</strong>
                @if($order->deliveryAgent)
                    <strong>{{ $order->deliveryAgent->name }}</strong> ({{ $order->deliveryAgent->phone }})
                @elseif($order->delivery_man_name)
                    <strong>{{ $order->delivery_man_name }}</strong> ({{ $order->delivery_man_phone }})
                @else
                    <span style="color: #9ca3af;">Unassigned</span>
                @endif
            </p>

            <!-- Rider Assignment Form -->
            <form action="{{ route('order.assign.rider', $order) }}" method="POST" style="margin-top: 14px; padding-top: 12px; border-top: 1px dashed #cbd5e1; display: flex; gap: 8px; align-items: center;">
                @csrf
                <select name="delivery_agent_id" required style="flex: 1; padding: 7px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px;">
                    <option value="">-- Assign Delivery Rider --</option>
                    @foreach($deliveryAgents as $agent)
                        <option value="{{ $agent->id }}" {{ $order->delivery_agent_id == $agent->id ? 'selected' : '' }}>
                            {{ $agent->name }} ({{ $agent->phone }}) - {{ $agent->status }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" style="padding: 7px 14px; background-color: #2563eb; color: white; border: none; border-radius: 6px; font-weight: 700; font-size: 12px; cursor: pointer;">
                    Assign
                </button>
            </form>

            <div style="border-top: 1px dashed #cbd5e1; margin-top: 14px; padding-top: 10px; font-size: 14px;">
                <p><strong>Subtotal:</strong> ৳{{ number_format($order->subtotal_amount, 2) }}</p>
                @if ($order->discount_amount > 0)
                    <p style="color: #e74c3c;">
                        <strong>Discount Applied ({{ $order->coupon_code }}):</strong> -৳{{ number_format($order->discount_amount, 2) }}
                    </p>
                @endif
                <p style="color: #0284c7;">
                    <strong>Delivery Charge:</strong> +৳{{ number_format($order->delivery_charge, 2) }}
                </p>
                <p style="font-size: 16px;"><strong>Grand Total:</strong> <strong>৳{{ number_format($order->total_amount, 2) }}</strong></p>
                <p style="color: #27ae60;"><strong>Paid Amount:</strong> <strong>৳{{ number_format($order->paid_amount, 2) }}</strong></p>
                <p style="color: #e74c3c; font-size: 16px;"><strong>Due Balance:</strong> <strong>৳{{ number_format($order->due_amount, 2) }}</strong></p>
            </div>
        </div>
    </div>

    {{-- Order Items Table --}}
    <div class="table-container" style="margin-bottom: 25px;">
        <h2>Order Items Snapshot</h2>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Unit Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($order->items as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->product_name ?? $item->product->name ?? 'Product' }}</strong>
                        </td>
                        <td>{{ $item->product_sku ?? ($item->product->sku ?? '—') }}</td>
                        <td>৳{{ number_format($item->price, 2) }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td><strong>৳{{ number_format($item->subtotal, 2) }}</strong></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty-message">No item details found for this order.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Payment Transactions History & Verification --}}
    <div class="table-container" style="margin-bottom: 25px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <h2>Payment Transactions &amp; Verification</h2>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Payment Method</th>
                    <th>Transaction Reference</th>
                    <th>Amount</th>
                    <th>Verification Status</th>
                    <th>Note</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($order->payments as $payment)
                    <tr>
                        <td>{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('d M, Y') : $payment->created_at->format('d M, Y') }}</td>
                        <td><strong>{{ strtoupper($payment->payment_method) }}</strong></td>
                        <td>
                            <code style="background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-weight: 700;">{{ $payment->transaction_id ?? '—' }}</code>
                        </td>
                        <td style="color: #27ae60; font-weight: bold;">৳{{ number_format($payment->amount, 2) }}</td>
                        <td>
                            <span style="font-size: 12px; font-weight: 700; padding: 2px 8px; border-radius: 4px; text-transform: uppercase; background: {{ $payment->verification_status === 'verified' ? '#ecfdf5; color:#065f46;' : ($payment->verification_status === 'rejected' ? '#fef2f2; color:#991b1b;' : '#fffbeb; color:#92400e;') }}">
                                {{ $payment->verification_status }}
                            </span>
                        </td>
                        <td><small>{{ $payment->note ?? '—' }}</small></td>
                        <td>
                            @if($payment->verification_status === 'pending')
                                <div style="display: flex; gap: 6px;">
                                    <form action="{{ route('order.payment.verify', $payment) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="action" value="verify">
                                        <button type="submit" style="background: #10b981; color: white; border: none; border-radius: 4px; padding: 4px 8px; font-size: 12px; font-weight: 700; cursor: pointer;">
                                            ✓ Verify
                                        </button>
                                    </form>
                                    <form action="{{ route('order.payment.verify', $payment) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" style="background: #ef4444; color: white; border: none; border-radius: 4px; padding: 4px 8px; font-size: 12px; font-weight: 700; cursor: pointer;" onclick="return confirm('Reject this transaction?');">
                                            ✕ Reject
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span style="color: #6b7280; font-size: 12px;">Processed</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-message">No payment transactions recorded yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Order Status History Timeline --}}
    @if($order->statusHistories->isNotEmpty())
        <div class="table-container" style="margin-bottom: 25px;">
            <h2>Status &amp; Event History</h2>
            <table>
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>Status</th>
                        <th>Comment / Details</th>
                        <th>User Responsible</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->statusHistories as $history)
                        <tr>
                            <td style="color: #6b7280; font-size: 13px;">{{ $history->created_at->format('d M Y, h:i A') }}</td>
                            <td><strong style="text-transform: capitalize;">{{ $history->status }}</strong></td>
                            <td style="font-size: 13px;">{{ $history->comment }}</td>
                            <td><small>{{ $history->changed_by ?? 'System' }}</small></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <a class="back-link" href="{{ route('order.index') }}">← Back to Orders</a>

</div>

{{-- Payment Modal --}}
<div id="paymentModal" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center;">
    <div style="background: #fff; width: 100%; max-width: 480px; border-radius: 10px; padding: 24px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); margin: 60px auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h3 style="margin: 0;">Record Payment for Order #{{ $order->order_number }}</h3>
            <button type="button" onclick="closePaymentModal()" style="background: none; border: none; font-size: 22px; cursor: pointer;">&times;</button>
        </div>

        <form action="{{ route('order.payments.store', $order) }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Amount to Pay (Due: ৳{{ number_format($order->due_amount, 2) }}) *</label>
                <input type="number" step="0.01" name="amount" value="{{ $order->due_amount }}" max="{{ $order->due_amount }}" required>
            </div>

            <div class="form-group">
                <label>Payment Method *</label>
                <select name="payment_method" required>
                    <option value="cash">Cash Collection</option>
                    <option value="bkash">bKash</option>
                    <option value="nagad">Nagad</option>
                    <option value="bank_transfer">Bank Transfer</option>
                </select>
            </div>

            <div class="form-group">
                <label>Payment Date *</label>
                <input type="date" name="payment_date" value="{{ now()->toDateString() }}" required>
            </div>

            <div class="form-group">
                <label>Transaction ID / Reference (Optional)</label>
                <input type="text" name="transaction_id" placeholder="e.g. TRX12345678">
            </div>

            <div class="form-group">
                <label>Internal Note (Optional)</label>
                <input type="text" name="note" placeholder="e.g. Collected by rider on delivery">
            </div>

            <button type="submit" class="primary-button">Submit Payment</button>
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
</script>
@endsection
