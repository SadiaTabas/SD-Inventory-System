@extends('layouts.app')

@section('title', 'Reports & Business Analytics')

@section('content')
<div class="container" style="max-width: 1400px;">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
        <div>
            <h1 style="text-align: left; margin: 0;">Reports &amp; Analytics</h1>
            <p style="color: #6b7280; font-size: 14px; margin-top: 4px;">Comprehensive financial, inventory, sales, and operational intelligence reports.</p>
        </div>
        <div>
            <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="create-button" style="background: #10b981; margin: 0; display: inline-flex; align-items: center; gap: 6px;">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export CSV Report
            </a>
            <button onclick="window.print()" class="create-button" style="background: #64748b; margin: 0 0 0 8px; display: inline-flex; align-items: center; gap: 6px;">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Print Report
            </button>
        </div>
    </div>

    <!-- Navigation Tabs for All 9 Report Types -->
    <div style="display: flex; gap: 8px; overflow-x: auto; padding-bottom: 12px; margin-bottom: 20px; border-bottom: 2px solid #e2e8f0;">
        @php
            $tabs = [
                'sales'      => 'Sales Report',
                'orders'     => 'Orders Report',
                'products'   => 'Top Products',
                'inventory'  => 'Full Stock',
                'low_stock'  => 'Low Stock Warning',
                'customers'  => 'Customer Insights',
                'payments'   => 'Payments Log',
                'purchases'  => 'Purchases Log',
                'profit'     => 'Gross Profit Margin',
            ];
        @endphp
        @foreach($tabs as $key => $label)
            <a href="{{ route('reports.index', ['type' => $key, 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}"
               style="padding: 10px 18px; border-radius: 8px; font-weight: 700; font-size: 14px; text-decoration: none; white-space: nowrap; transition: all 0.2s; {{ $reportType === $key ? 'background: #2563eb; color: #fff; box-shadow: 0 2px 4px rgba(37,99,235,0.2);' : 'background: #f1f5f9; color: #475569;' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <!-- Filter Form -->
    <div class="table-container" style="padding: 16px 20px; margin-bottom: 24px; background: #f8fafc;">
        <form method="GET" action="{{ route('reports.index') }}" style="display: flex; gap: 16px; align-items: flex-end; flex-wrap: wrap;">
            <input type="hidden" name="type" value="{{ $reportType }}">

            <div>
                <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 4px;">Start Date</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" style="padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;">
            </div>

            <div>
                <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 4px;">End Date</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" style="padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;">
            </div>

            <button type="submit" style="padding: 9px 20px; background: #2563eb; color: #fff; border: none; border-radius: 6px; font-weight: 700; cursor: pointer;">
                Apply Date Filter
            </button>
        </form>
    </div>

    <!-- Report Contents by Type -->
    <div class="table-container">

        @if($reportType === 'sales' || $reportType === 'orders')
            <h2 style="font-size: 18px; margin-bottom: 16px;">{{ ucfirst($reportType) }} Report: {{ $dateFrom }} to {{ $dateTo }}</h2>
            <div style="margin-bottom: 16px; display: flex; gap: 20px;">
                <div style="background: #eff6ff; padding: 12px 18px; border-radius: 8px;">
                    <div style="font-size: 12px; color: #1e40af; font-weight: 600;">Total Orders</div>
                    <div style="font-size: 22px; font-weight: 800; color: #1e3a8a;">{{ count($data) }}</div>
                </div>
                <div style="background: #ecfdf5; padding: 12px 18px; border-radius: 8px;">
                    <div style="font-size: 12px; color: #065f46; font-weight: 600;">Total Net Revenue</div>
                    <div style="font-size: 22px; font-weight: 800; color: #047857;">৳ {{ number_format(collect($data)->sum('total_amount'), 2) }}</div>
                </div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Source</th>
                        <th>Payment Method</th>
                        <th>Payment Status</th>
                        <th>Order Status</th>
                        <th style="text-align: right;">Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $order)
                        <tr>
                            <td><a href="{{ route('order.show', $order) }}" style="font-weight: 700; color: #2563eb;">{{ $order->order_number }}</a></td>
                            <td>{{ $order->created_at->format('M d, Y h:i A') }}</td>
                            <td>{{ $order->user->name ?? ($order->customer->name ?? 'Guest/Unknown') }}</td>
                            <td><span class="status {{ $order->order_source === 'online' ? 'active' : 'inactive' }}">{{ ucfirst($order->order_source) }}</span></td>
                            <td>{{ strtoupper($order->payment_method) }}</td>
                            <td><span class="status {{ $order->payment_status === 'paid' ? 'active' : 'inactive' }}">{{ ucfirst($order->payment_status) }}</span></td>
                            <td><span class="status {{ $order->status === 'delivered' ? 'active' : ($order->status === 'cancelled' ? 'inactive' : '') }}">{{ ucfirst($order->status) }}</span></td>
                            <td style="text-align: right; font-weight: 700;">৳ {{ number_format($order->total_amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="empty-message">No orders recorded in this date range.</td></tr>
                    @endforelse
                </tbody>
            </table>

        @elseif($reportType === 'products')
            <h2 style="font-size: 18px; margin-bottom: 16px;">Top-Selling Products: {{ $dateFrom }} to {{ $dateTo }}</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th style="text-align: center;">Total Units Sold</th>
                        <th style="text-align: right;">Total Revenue Generated</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $row)
                        <tr>
                            <td>#{{ $row->product_id }}</td>
                            <td><strong>{{ $row->product_name }}</strong></td>
                            <td style="text-align: center; font-weight: 700; color: #2563eb;">{{ $row->total_qty }}</td>
                            <td style="text-align: right; font-weight: 700; color: #059669;">৳ {{ number_format($row->total_sales, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="empty-message">No product sales recorded in this date range.</td></tr>
                    @endforelse
                </tbody>
            </table>

        @elseif($reportType === 'inventory' || $reportType === 'low_stock')
            <h2 style="font-size: 18px; margin-bottom: 16px;">{{ $reportType === 'low_stock' ? '⚠️ Low Stock & Depleted Inventory Warning' : 'Full Inventory Valuation Report' }}</h2>
            <table>
                <thead>
                    <tr>
                        <th>SKU / ID</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th style="text-align: right;">Unit Cost</th>
                        <th style="text-align: right;">Selling Price</th>
                        <th style="text-align: center;">Stock Qty</th>
                        <th style="text-align: center;">Min Threshold</th>
                        <th style="text-align: right;">Total Asset Value</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $prod)
                        <tr style="{{ $prod->qty <= $prod->min_stock_level ? 'background: #fff1f2;' : '' }}">
                            <td><code>{{ $prod->sku ?? ('PRD-' . $prod->id) }}</code></td>
                            <td><strong>{{ $prod->name }}</strong></td>
                            <td>{{ $prod->category->name ?? 'Uncategorized' }}</td>
                            <td style="text-align: right;">৳ {{ number_format($prod->cost_price, 2) }}</td>
                            <td style="text-align: right; font-weight: 700;">৳ {{ number_format($prod->selling_price, 2) }}</td>
                            <td style="text-align: center;">
                                <strong style="color: {{ $prod->qty <= $prod->min_stock_level ? '#e11d48' : '#059669' }};">{{ $prod->qty }}</strong>
                            </td>
                            <td style="text-align: center; color: #64748b;">{{ $prod->min_stock_level }}</td>
                            <td style="text-align: right; font-weight: 700;">৳ {{ number_format($prod->qty * ($prod->cost_price > 0 ? $prod->cost_price : $prod->selling_price), 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="empty-message">No inventory records found.</td></tr>
                    @endforelse
                </tbody>
            </table>

        @elseif($reportType === 'customers')
            <h2 style="font-size: 18px; margin-bottom: 16px;">Customer Lifetime Activity &amp; Spending</h2>
            <table>
                <thead>
                    <tr>
                        <th>Customer ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th style="text-align: center;">Orders in Period</th>
                        <th style="text-align: right;">Total Spend in Period</th>
                        <th>Registration Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $cust)
                        <tr>
                            <td>#{{ $cust->id }}</td>
                            <td><strong>{{ $cust->name }}</strong></td>
                            <td>{{ $cust->email }}</td>
                            <td>{{ $cust->phone ?? '—' }}</td>
                            <td style="text-align: center; font-weight: 700;">{{ $cust->orders_count }}</td>
                            <td style="text-align: right; font-weight: 700; color: #059669;">৳ {{ number_format($cust->orders_sum_total_amount ?? 0, 2) }}</td>
                            <td>{{ $cust->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="empty-message">No customer activity recorded in this period.</td></tr>
                    @endforelse
                </tbody>
            </table>

        @elseif($reportType === 'payments')
            <h2 style="font-size: 18px; margin-bottom: 16px;">Payments Audit Log: {{ $dateFrom }} to {{ $dateTo }}</h2>
            <table>
                <thead>
                    <tr>
                        <th>Payment ID</th>
                        <th>Order #</th>
                        <th>Payment Method</th>
                        <th>Transaction ID</th>
                        <th style="text-align: right;">Amount Paid</th>
                        <th>Status</th>
                        <th>Date &amp; Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $pay)
                        <tr>
                            <td>#{{ $pay->id }}</td>
                            <td><a href="{{ route('order.show', $pay->order) }}" style="font-weight: 700; color: #2563eb;">{{ $pay->order->order_number ?? 'Order #' . $pay->order_id }}</a></td>
                            <td><strong>{{ strtoupper($pay->payment_method) }}</strong></td>
                            <td><code>{{ $pay->transaction_id ?? 'N/A' }}</code></td>
                            <td style="text-align: right; font-weight: 700; color: #059669;">৳ {{ number_format($pay->amount, 2) }}</td>
                            <td><span class="status {{ $pay->status === 'verified' ? 'active' : 'inactive' }}">{{ ucfirst($pay->status) }}</span></td>
                            <td>{{ $pay->payment_date ? \Carbon\Carbon::parse($pay->payment_date)->format('M d, Y h:i A') : $pay->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="empty-message">No payments recorded in this period.</td></tr>
                    @endforelse
                </tbody>
            </table>

        @elseif($reportType === 'purchases')
            <h2 style="font-size: 18px; margin-bottom: 16px;">Purchase Orders Log: {{ $dateFrom }} to {{ $dateTo }}</h2>
            <table>
                <thead>
                    <tr>
                        <th>Purchase #</th>
                        <th>Date</th>
                        <th>Supplier</th>
                        <th>Status</th>
                        <th style="text-align: right;">Total Amount</th>
                        <th style="text-align: right;">Paid Amount</th>
                        <th style="text-align: right;">Due Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $pur)
                        <tr>
                            <td><a href="{{ route('purchases.show', $pur) }}" style="font-weight: 700; color: #2563eb;">{{ $pur->purchase_number }}</a></td>
                            <td>{{ \Carbon\Carbon::parse($pur->purchase_date)->format('M d, Y') }}</td>
                            <td>{{ $pur->supplier->name ?? 'Supplier' }}</td>
                            <td><span class="status {{ $pur->status === 'received' ? 'active' : 'inactive' }}">{{ ucfirst($pur->status) }}</span></td>
                            <td style="text-align: right; font-weight: 700;">৳ {{ number_format($pur->total_amount, 2) }}</td>
                            <td style="text-align: right; color: #059669;">৳ {{ number_format($pur->paid_amount, 2) }}</td>
                            <td style="text-align: right; color: #e11d48; font-weight: 700;">৳ {{ number_format($pur->due_amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="empty-message">No purchase orders found for this period.</td></tr>
                    @endforelse
                </tbody>
            </table>

        @elseif($reportType === 'profit')
            <h2 style="font-size: 18px; margin-bottom: 16px;">Gross Profit &amp; Margin Analysis: {{ $dateFrom }} to {{ $dateTo }}</h2>
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;">
                <div style="background: #eff6ff; padding: 16px; border-radius: 8px;">
                    <div style="font-size: 13px; color: #1e40af; font-weight: 600;">Gross Sales Revenue</div>
                    <div style="font-size: 24px; font-weight: 800; color: #1e3a8a;">৳ {{ number_format($data['total_sales'], 2) }}</div>
                </div>
                <div style="background: #fef2f2; padding: 16px; border-radius: 8px;">
                    <div style="font-size: 13px; color: #991b1b; font-weight: 600;">Cost of Goods Sold (COGS)</div>
                    <div style="font-size: 24px; font-weight: 800; color: #b91c1c;">৳ {{ number_format($data['total_cost'], 2) }}</div>
                </div>
                <div style="background: #ecfdf5; padding: 16px; border-radius: 8px;">
                    <div style="font-size: 13px; color: #065f46; font-weight: 600;">Net Gross Profit</div>
                    <div style="font-size: 24px; font-weight: 800; color: #047857;">৳ {{ number_format($data['profit'], 2) }}</div>
                </div>
                <div style="background: #fdf4ff; padding: 16px; border-radius: 8px;">
                    <div style="font-size: 13px; color: #86198f; font-weight: 600;">Gross Profit Margin</div>
                    <div style="font-size: 24px; font-weight: 800; color: #a21caf;">{{ $data['margin'] }}%</div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Item Details</th>
                        <th style="text-align: center;">Quantity Sold</th>
                        <th style="text-align: right;">Unit Cost</th>
                        <th style="text-align: right;">Selling Price</th>
                        <th style="text-align: right;">Subtotal Sales</th>
                        <th style="text-align: right;">Estimated Profit</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data['items'] as $item)
                        @php
                            $cost = $item->product && $item->product->cost_price > 0 ? $item->product->cost_price : ($item->price * 0.7);
                            $profit = $item->subtotal - ($cost * $item->quantity);
                        @endphp
                        <tr>
                            <td><strong>{{ $item->product_name ?? ($item->product->name ?? 'Product') }}</strong></td>
                            <td style="text-align: center; font-weight: 700;">{{ $item->quantity }}</td>
                            <td style="text-align: right;">৳ {{ number_format($cost, 2) }}</td>
                            <td style="text-align: right;">৳ {{ number_format($item->price, 2) }}</td>
                            <td style="text-align: right; font-weight: 700;">৳ {{ number_format($item->subtotal, 2) }}</td>
                            <td style="text-align: right; font-weight: 800; color: {{ $profit >= 0 ? '#059669' : '#e11d48' }};">
                                ৳ {{ number_format($profit, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty-message">No order line items found in this date range.</td></tr>
                    @endforelse
                </tbody>
            </table>
        @endif

    </div>

</div>

<style>
@media print {
    .navbar, header, form, .create-button, a[href*="export=csv"], button {
        display: none !important;
    }
    body {
        background: #fff !important;
        font-size: 12px !important;
    }
    .table-container {
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
    }
}
</style>
@endsection
