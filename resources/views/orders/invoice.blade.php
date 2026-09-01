<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $order->order_number ?? $order->id }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            font-size: 13px;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }
        .header {
            width: 100%;
            border-bottom: 2px solid #27ae60;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .header table {
            width: 100%;
        }
        .logo {
            font-size: 22px;
            font-weight: bold;
            color: #27ae60;
        }
        .invoice-title {
            text-align: right;
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
        }
        .info-grid {
            width: 100%;
            margin-bottom: 25px;
        }
        .info-grid td {
            vertical-align: top;
            width: 50%;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #2c3e50;
            border-bottom: 1px solid #ddd;
            padding-bottom: 4px;
            display: inline-block;
            width: 90%;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 20px;
        }
        .items-table th {
            background-color: #2c3e50;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            padding: 9px;
        }
        .items-table td {
            padding: 9px;
            border-bottom: 1px solid #e2e8f0;
        }
        .text-right {
            text-align: right;
        }
        .total-box {
            width: 48%;
            float: right;
            margin-top: 10px;
            margin-bottom: 20px;
        }
        .total-box table {
            width: 100%;
        }
        .total-box td {
            padding: 5px;
        }
        .grand-total {
            font-size: 15px;
            font-weight: bold;
            color: #2c3e50;
            border-top: 1px solid #cbd5e1;
        }
        .due-total {
            font-size: 16px;
            font-weight: bold;
            color: #e11d48;
            border-top: 2px solid #e11d48;
        }
        .payments-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 25px;
        }
        .payments-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: bold;
            text-align: left;
            padding: 7px;
            font-size: 12px;
            border-bottom: 1px solid #cbd5e1;
        }
        .payments-table td {
            padding: 7px;
            font-size: 12px;
            border-bottom: 1px solid #f1f5f9;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 11px;
            color: #7f8c8d;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td>
                    <div class="logo">SD Inventory System</div>
                    <small>Official Sales Invoice & Payment Receipt</small>
                </td>
                <td class="invoice-title">
                    INVOICE
                    <br>
                    <small style="font-size: 12px; font-weight: normal; color: #555;">
                        #{{ $order->order_number ?? $order->id }}
                    </small>
                </td>
            </tr>
        </table>
    </div>

    <table class="info-grid">
        <tr>
            <td>
                <div class="section-title">Billed To (Customer):</div>
                <p>
                    <strong>Name:</strong> {{ $order->user->name ?? ($order->customer->name ?? 'Customer') }}<br>
                    <strong>Email:</strong> {{ $order->user->email ?? ($order->customer->email ?? 'N/A') }}<br>
                    <strong>Phone:</strong> {{ $order->user->phone ?? ($order->customer->phone ?? 'N/A') }}<br>
                    <strong>Address:</strong> {{ $order->shipping_address }}
                </p>
            </td>
            <td>
                <div class="section-title">Order & Payment Info:</div>
                <p>
                    <strong>Date:</strong> {{ $order->created_at->format('d M, Y h:i A') }}<br>
                    <strong>Payment Method:</strong> {{ strtoupper($order->payment_method ?? 'COD') }}<br>
                    <strong>Payment Status:</strong> {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}<br>
                    <strong>Delivery Status:</strong> {{ ucfirst(str_replace('_', ' ', $order->delivery_status)) }}
                </p>
            </td>
        </tr>
    </table>

    {{-- Order Items Table --}}
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 50%;">Product</th>
                <th class="text-right" style="width: 15%;">Price</th>
                <th class="text-right" style="width: 15%;">Quantity</th>
                <th class="text-right" style="width: 20%;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->product->name ?? 'Product Unavailable' }}</td>
                    <td class="text-right">৳{{ number_format($item->price, 2) }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">৳{{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Calculation & Financial Balance Box --}}
    <div class="total-box">
        <table>
            <tr>
                <td><strong>Subtotal:</strong></td>
                <td class="text-right">৳{{ number_format($order->subtotal_amount > 0 ? $order->subtotal_amount : $order->total_amount, 2) }}</td>
            </tr>

            @if(($order->discount_amount ?? 0) > 0)
                <tr>
                    <td style="color: #e74c3c;">
                        <strong>Discount ({{ $order->coupon_code ?? 'Manual' }}):</strong>
                    </td>
                    <td class="text-right" style="color: #e74c3c;">
                        - ৳{{ number_format($order->discount_amount, 2) }}
                    </td>
                </tr>
            @endif

            <tr class="grand-total">
                <td>Grand Total:</td>
                <td class="text-right">৳{{ number_format($order->total_amount, 2) }}</td>
            </tr>

            <tr>
                <td style="color: #10b981;"><strong>Paid Amount:</strong></td>
                <td class="text-right" style="color: #10b981;"><strong>৳{{ number_format($order->paid_amount ?? 0, 2) }}</strong></td>
            </tr>

            <tr class="due-total">
                <td>Remaining Due:</td>
                <td class="text-right">৳{{ number_format($order->due_amount ?? 0, 2) }}</td>
            </tr>
        </table>
    </div>

    <div style="clear: both;"></div>

    {{-- Payment Transactions History --}}
    @if($order->payments && $order->payments->count() > 0)
        <div style="margin-top: 10px;">
            <div class="section-title">Payment Transaction History:</div>
            <table class="payments-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Method</th>
                        <th>Trx ID / Ref</th>
                        <th>Note</th>
                        <th class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->payments as $payment)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M, Y') }}</td>
                            <td>{{ strtoupper($payment->payment_method) }}</td>
                            <td>{{ $payment->transaction_id ?? '—' }}</td>
                            <td>{{ $payment->note ?? '—' }}</td>
                            <td class="text-right" style="font-weight: bold; color: #10b981;">
                                ৳{{ number_format($payment->amount, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="footer">
        <p>Thank you for your business! This is a system-generated invoice from SD Inventory System.</p>
    </div>

</body>
</html>