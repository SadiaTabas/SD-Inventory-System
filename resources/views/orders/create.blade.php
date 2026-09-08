@extends('layouts.app')

@section('title', 'Create Order')

@section('content')

 
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">

<style>
    .order-sheet-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        max-width: 920px;
        width: 100%;
        margin: 25px auto;
        padding: 35px 40px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        color: #1e293b;
        box-sizing: border-box;
    }

    .sheet-top-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .sheet-title {
        font-size: 30px;
        font-weight: 800;
        color: #0284c7;
        margin: 0;
    }

    /* Blue SD Logo */
    .sheet-logo-badge {
        width: 65px;
        height: 65px;
        background-color: #0284c7;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 20px;
        font-weight: 800;
        letter-spacing: 1px;
    }

    .sheet-divider {
        height: 3px;
        background-color: #0284c7;
        border: none;
        margin-bottom: 25px;
    }

    .sheet-grid-two-col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin-bottom: 20px;
    }

    .sheet-section-heading {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 8px;
    }

    .sheet-meta-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 14px;
    }

    .sheet-meta-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
        font-size: 13.5px;
    }

    .sheet-meta-row:last-child {
        margin-bottom: 0;
    }

    .sheet-field {
        margin-bottom: 15px;
    }

    .sheet-input, .sheet-textarea, .sheet-select {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 13.5px;
        box-sizing: border-box;
    }

    .sheet-textarea {
        resize: vertical;
        min-height: 65px;
    }

    .sheet-options-stack {
        display: flex;
        flex-direction: column;
        gap: 6px;
        font-size: 13.5px;
    }

    .sheet-options-stack label {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        margin: 0;
    }

   .table-responsive-wrapper {
        width: 100%;
        overflow: visible; /* Prevents clipping and internal scrollbars */
        margin-top: 12px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        box-sizing: border-box;
        background: #ffffff;
    }

    .sheet-items-table {
        width: 100%;
        border-collapse: collapse;
    }

    .sheet-items-table th {
        background-color: #1e293b;
        color: #ffffff !important;
        font-weight: 700;
        font-size: 13.5px;
        padding: 12px 14px;
        text-align: left;
    }

    .sheet-items-table td {
        padding: 14px 12px; /* Gives each row more breathing room */
        border-bottom: 1px solid #e2e8f0;
        vertical-align: top;
        font-size: 13.5px;
    }

    /* Keep TomSelect dropdown above all table elements */
    .sheet-items-table .ts-wrapper {
        position: relative;
        z-index: 20;
    }

    .sheet-items-table .ts-dropdown {
        z-index: 9999 !important;
        max-height: 240px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15);
        border: 1px solid #cbd5e1;
        border-radius: 6px;
    }

    .text-right {
        text-align: right;
    }

    .btn-add-item {
        background: #3b82f6;
        color: #ffffff;
        border: none;
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: bold;
        cursor: pointer;
    }

    .action-btn-delete {
        background: #ef4444;
        color: #ffffff;
        border: none;
        border-radius: 4px;
        padding: 6px 10px;
        cursor: pointer;
        font-weight: bold;
    }

    /* Contained Bottom Summary Section */
    .summary-section-grid {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-top: 25px;
        width: 100%;
        box-sizing: border-box;
        gap: 20px;
    }

    .summary-coupon-box {
        flex: 1;
        max-width: 300px;
    }

    .summary-table-box {
        width: auto;
        min-width: 280px;
        max-width: 320px;
    }

    .sheet-summary-table {
        width: 100%;
        border-collapse: collapse;
        box-sizing: border-box;
    }

    .sheet-summary-table td {
        padding: 6px 4px;
        font-size: 13.5px;
        white-space: nowrap;
    }

    .sheet-summary-table td:first-child {
        width: 120px;
        text-align: left;
    }

    .sheet-summary-table td:last-child {
        text-align: left;
        padding-left: 10px;
    }

    .sheet-summary-table .grand-row {
        font-weight: bold;
        font-size: 15px;
        border-top: 1px solid #cbd5e1;
        border-bottom: 1px solid #cbd5e1;
    }

    .sheet-summary-table .due-row {
        font-weight: bold;
        font-size: 15px;
        color: #e11d48;
    }

    .btn-submit-order {
        background: #0284c7;
        color: #ffffff;
        border: none;
        padding: 10px 24px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: bold;
        cursor: pointer;
    }

    .ts-control {
        border-radius: 6px !important;
        padding: 7px 10px !important;
        border: 1px solid #cbd5e1 !important;
        font-size: 13px !important;
    }
</style>

<div class="order-sheet-card">

    {{-- Top Header with Blue SD Badge --}}
    <div class="sheet-top-header">
        <h1 class="sheet-title">Order Form</h1>
        <div class="sheet-logo-badge">SD</div>
    </div>

    <hr class="sheet-divider">

    @if ($errors->any())
        <div style="background: #fee2e2; border: 1px solid #fca5a5; color: #b91c1c; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('order.store') }}">
        @csrf

       
        <div class="sheet-grid-two-col">
            <div>
                <div class="sheet-section-heading">Customer Details:</div>
                <div class="sheet-field">
                    <select id="user_id" name="user_id" placeholder="Select customer..." required>
                        <option value="">Search customer by name or email...</option>
                        @foreach ($customers as $customer)
                            <option
                                value="{{ $customer->id }}"
                                data-addresses='@json($customer->addresses)'
                                {{ old('user_id') == $customer->id ? 'selected' : '' }}
                            >
                                {{ $customer->name }} ({{ $customer->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div id="customer_preview_box" style="font-size: 12.5px; color: #64748b;">
                    <em>Select a customer above to view their recorded address.</em>
                </div>
            </div>

            <div>
                <div class="sheet-meta-box">
                    <div class="sheet-meta-row">
                        <span style="color: #64748b;">Date:</span>
                        <strong>{{ date('d/m/Y') }}</strong>
                    </div>
                    <div class="sheet-meta-row">
                        <span style="color: #64748b;">Order Ref:</span>
                        <span>Auto-generated</span>
                    </div>
                    <div class="sheet-meta-row">
                        <span style="color: #64748b;">Delivery Slot:</span>
                        <div style="width: 60%;">
                            <select id="delivery_slot" name="delivery_slot">
                                <option value="Morning (9:00 AM - 12:00 PM)">Morning (9:00 AM - 12:00 PM)</option>
                                <option value="Afternoon (12:00 PM - 3:00 PM)">Afternoon (12:00 PM - 3:00 PM)</option>
                                <option value="Evening (3:00 PM - 6:00 PM)">Evening (3:00 PM - 6:00 PM)</option>
                                <option value="Express (Within 2 Hours)">Express (Within 2 Hours)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 2: Delivery Address & Notes vs Delivery Method & Payment --}}
        <div class="sheet-grid-two-col">
            <div>
                <div class="sheet-section-heading">Delivery Information:</div>
                <div class="sheet-field">
                    <label style="font-size: 12px; color: #64748b; display: block; margin-bottom: 4px;">Ship to Delivery Address:</label>
                    <textarea
                        class="sheet-textarea"
                        id="shipping_address"
                        name="shipping_address"
                        placeholder="Complete street address..."
                        required
                    >{{ old('shipping_address') }}</textarea>
                </div>

                <div class="sheet-section-heading">Order Notes:</div>
                <div class="sheet-field">
                    <textarea
                        class="sheet-textarea"
                        id="delivery_notes"
                        name="delivery_notes"
                        placeholder="Add delivery instructions or requests here..."
                    >{{ old('delivery_notes') }}</textarea>
                </div>
            </div>

            <div>
               <div class="sheet-section-heading" style="display: flex; justify-content: space-between; align-items: center;">
                    <span>Delivery Method:</span>
                     
                </div>
                <div class="sheet-options-stack sheet-field">
                    <label style="cursor: default;">
                        <input type="radio" name="delivery_charge" value="60" id="charge_dhaka" checked onclick="return false;" style="cursor: default;">
                        Inside Dhaka (BDT 60.00)
                    </label>
                    <label style="cursor: default;">
                        <input type="radio" name="delivery_charge" value="120" id="charge_outside" onclick="return false;" style="cursor: default;">
                        Outside Dhaka (BDT 120.00)
                    </label>
                </div>

                <div class="sheet-section-heading" style="margin-top: 15px;">Payment Details:</div>
                <div class="sheet-options-stack sheet-field">
                    <label><input type="radio" name="payment_method" value="cod" checked> Cash on Delivery (COD)</label>
                    
                    <label><input type="radio" name="payment_method" value="card"> Card / Online</label>
                </div>

               <div style="margin-top: 15px;">
                    <label style="font-size: 12px; font-weight: 600; color: #475569; display: block; margin-bottom: 4px;">Delivery Rider</label>
                    <select id="delivery_agent_id" name="delivery_agent_id">
                        <option value="">-- Optional --</option>
                        @foreach ($deliveryAgents as $agent)
                            <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Row 3: Items Table with Complete Action Word and Fixed Columns --}}
        <div style="margin-top: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <span class="sheet-section-heading" style="margin: 0;">Ordered Items:</span>
                <button type="button" class="btn-add-item" onclick="addProductRow()">+ Add Items</button>
            </div>

            <div class="table-responsive-wrapper">
                <table class="sheet-items-table">
                    <thead>
                        <tr>
                            <th style="width: 6%;">#</th>
                            <th style="width: 44%;">Description / Product</th>
                            <th style="width: 12%;">Qty</th>
                            <th class="text-right" style="width: 14%;">Unit Price</th>
                            <th class="text-right" style="width: 14%;">Total</th>
                            <th style="width: 10%; text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="items-table-body">
                        {{-- Injected dynamically --}}
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Row 4: Contained Coupon & Summary Totals --}}
        <div class="summary-section-grid">
            <div class="summary-coupon-box">
                <label style="font-size: 12px; font-weight: 600; color: #475569; display: block; margin-bottom: 5px;">Promo Code / Coupon:</label>
                <select id="coupon_id" name="coupon_id" placeholder="Select coupon...">
                    <option value="" data-type="none" data-value="0" data-min="0">-- No Coupon --</option>
                    @if(isset($coupons))
                        @foreach ($coupons as $coupon)
                            <option
                                value="{{ $coupon->id }}"
                                data-type="{{ $coupon->type }}"
                                data-value="{{ $coupon->value }}"
                                data-min="{{ $coupon->min_order_amount }}"
                            >
                                {{ $coupon->code }} ({{ $coupon->type === 'percent' ? $coupon->value . '%' : 'BDT ' . $coupon->value }} OFF)
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="summary-table-box">
                <table class="sheet-summary-table">
                    <tr>
                        <td style="color: #64748b;">Subtotal:</td>
                        <td><strong id="display_subtotal">BDT 0.00</strong></td>
                    </tr>
                    <tr>
                        <td style="color: #e74c3c;">Discount:</td>
                        <td style="color: #e74c3c;"><strong id="display_discount">- BDT 0.00</strong></td>
                    </tr>
                    <tr>
                        <td style="color: #0284c7;">Shipping Fee:</td>
                        <td style="color: #0284c7;"><strong id="display_delivery">+ BDT 60.00</strong></td>
                    </tr>
                    <tr class="grand-row">
                        <td>Total:</td>
                        <td><strong id="display_total">BDT 0.00</strong></td>
                    </tr>
                    <tr>
                        <td style="color: #10b981;">Paid:</td>
                        <td style="color: #10b981;"><strong id="display_advance">BDT 0.00</strong></td>
                    </tr>
                    <tr class="due-row">
                        <td>Balance Due:</td>
                        <td><strong id="display_due">BDT 0.00</strong></td>
                    </tr>
                </table>
            </div>
        </div>

        <div style="text-align: right; margin-top: 25px; border-top: 1px solid #e2e8f0; padding-top: 18px;">
            <a href="{{ route('order.index') }}" style="margin-right: 15px; color: #64748b; text-decoration: none; font-size: 13.5px;">Cancel</a>
            <button type="submit" class="btn-submit-order">Process & Place Order</button>
        </div>

    </form>
</div>

<div id="products-catalog-data" style="display: none;">@json($products)</div>
 
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
const productsCatalog = JSON.parse(document.getElementById('products-catalog-data').textContent);
let rowIndex = 0;
const rowSelectMap = {};

function calculateOrderSummary() {
    let subtotal = 0;

    Object.keys(rowSelectMap).forEach(key => {
        const select = rowSelectMap[key];
        if (select) {
            const prodId = select.getValue();
            const qtyInput = document.getElementById('item_qty_' + key);
            const qty = parseInt(qtyInput ? qtyInput.value : 0) || 0;

            if (prodId) {
                const prod = productsCatalog.find(p => p.id == prodId);
                if (prod) {
                    const price = parseFloat(prod.price);
                    const lineTotal = price * qty;
                    subtotal += lineTotal;

                    const unitEl = document.getElementById('unit_price_' + key);
                    const lineEl = document.getElementById('line_total_' + key);
                    if (unitEl) unitEl.innerText = 'BDT ' + price.toFixed(2);
                    if (lineEl) lineEl.innerText = 'BDT ' + lineTotal.toFixed(2);
                }
            }
        }
    });

    let discount = 0;
    const couponVal = window.couponSelectInstance ? window.couponSelectInstance.getValue() : null;
    if (couponVal) {
        const opt = document.querySelector('#coupon_id option[value="' + couponVal + '"]');
        const type = opt ? opt.getAttribute('data-type') : null;
        const val = parseFloat(opt ? opt.getAttribute('data-value') : 0) || 0;
        const min = parseFloat(opt ? opt.getAttribute('data-min') : 0) || 0;

        if (subtotal >= min) {
            if (type === 'percent') {
                discount = subtotal * (val / 100);
            } else if (type === 'fixed') {
                discount = val;
            }
        }
    }

    discount = Math.min(discount, subtotal);

    let deliveryCharge = 60.00;
    const selectedDelivery = document.querySelector('input[name="delivery_charge"]:checked');
    if (selectedDelivery) {
        deliveryCharge = parseFloat(selectedDelivery.value);
    }

    const grandTotal = Math.max(0, (subtotal - discount) + deliveryCharge);
    const advanceInput = parseFloat(document.getElementById('advance_payment')?.value || 0) || 0;
    const advancePaid = Math.min(advanceInput, grandTotal);
    const dueBalance = Math.max(0, grandTotal - advancePaid);

    document.getElementById('display_subtotal').innerText = 'BDT ' + subtotal.toFixed(2);
    document.getElementById('display_discount').innerText = '- BDT ' + discount.toFixed(2);
    document.getElementById('display_delivery').innerText = '+ BDT ' + deliveryCharge.toFixed(2);
    document.getElementById('display_total').innerText = 'BDT ' + grandTotal.toFixed(2);
    document.getElementById('display_advance').innerText = 'BDT ' + advancePaid.toFixed(2);
    document.getElementById('display_due').innerText = 'BDT ' + dueBalance.toFixed(2);
}

function addProductRow() {
    const tableBody = document.getElementById('items-table-body');
    const currentIndex = rowIndex++;

    let productOptions = '<option value="">Select product...</option>';
    productsCatalog.forEach(p => {
        productOptions += '<option value="' + p.id + '">' + p.name + ' (Stock: ' + p.qty + ')</option>';
    });

    const tr = document.createElement('tr');
    tr.id = 'table-row-' + currentIndex;
    tr.innerHTML = `
        <td style="color: #64748b;">${tableBody.children.length + 1}</td>
        <td>
            <select id="item_select_${currentIndex}" name="items[${currentIndex}][product_id]" required placeholder="Select item...">
                ${productOptions}
            </select>
        </td>
        <td>
            <input
                type="number"
                class="sheet-input"
                id="item_qty_${currentIndex}"
                name="items[${currentIndex}][quantity]"
                value="1"
                min="1"
                required
                oninput="calculateOrderSummary()"
            >
        </td>
        <td class="text-right" id="unit_price_${currentIndex}" style="color: #475569;">
            BDT 0.00
        </td>
        <td class="text-right" id="line_total_${currentIndex}" style="font-weight: 600;">
            BDT 0.00
        </td>
        <td style="text-align: center;">
            <button type="button" class="action-btn-delete" onclick="removeProductRow(${currentIndex})">✕</button>
        </td>
    `;

    tableBody.appendChild(tr);

    rowSelectMap[currentIndex] = new TomSelect('#item_select_' + currentIndex, {
        create: false,
        sortField: { field: "text", direction: "asc" },
        onChange: function() {
            calculateOrderSummary();
        }
    });

    calculateOrderSummary();
}

function removeProductRow(index) {
    const rows = document.getElementById('items-table-body').children;
    if (rows.length <= 1) {
        alert("The order form requires at least one product line.");
        return;
    }

    if (rowSelectMap[index]) {
        rowSelectMap[index].destroy();
        delete rowSelectMap[index];
    }

    const tr = document.getElementById('table-row-' + index);
    if (tr) tr.remove();

    calculateOrderSummary();
}

document.addEventListener('DOMContentLoaded', function () {
    addProductRow();

    new TomSelect('#user_id', {
        create: false,
        sortField: { field: "text", direction: "asc" },
        onChange: function(value) {
            if (!value) return;
            const originalOption = document.querySelector('#user_id option[value="' + value + '"]');
            const rawData = originalOption ? originalOption.getAttribute('data-addresses') : null;
            const previewBox = document.getElementById('customer_preview_box');
            const addressField = document.getElementById('shipping_address');

            if (rawData) {
                try {
                    const addresses = JSON.parse(rawData);
                    if (addresses.length > 0) {
                        const defaultAddr = addresses.find(a => a.is_default) || addresses[0];
                        const line = defaultAddr.address || defaultAddr.address_line || '';
                        const city = defaultAddr.city || '';
                        const full = line + (city ? ', ' + city : '');

                        addressField.value = full;
                        previewBox.innerHTML = '<strong>Delivery:</strong> ' + full;

                       const targetCity = (city || full).toLowerCase();
                        if (targetCity.includes('dhaka')) {
                            document.getElementById('charge_dhaka').checked = true;
                        } else {
                            document.getElementById('charge_outside').checked = true;
                        }
                        calculateOrderSummary();
                    }
                } catch (e) {
                    console.error('Failed to parse customer addresses', e);
                }
            }
        }
    });

    window.couponSelectInstance = new TomSelect('#coupon_id', {
        create: false,
        onChange: function() {
            calculateOrderSummary();
        }
    });

    new TomSelect('#delivery_slot', { create: true, persist: false });
    new TomSelect('#delivery_agent_id', { create: false });

    document.querySelectorAll('input[name="delivery_charge"]').forEach(r => {
        r.addEventListener('change', calculateOrderSummary);
    });

    document.getElementById('advance_payment')?.addEventListener('input', calculateOrderSummary);

    calculateOrderSummary();
});
</script>

@endsection