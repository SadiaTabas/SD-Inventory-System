@extends('layouts.customer')

@section('title', 'Secure Checkout - SD Store')

@section('content')
<h1 style="font-size: 28px; font-weight: 800; margin-bottom: 24px;">Checkout</h1>

<form action="{{ route('checkout.placeOrder') }}" method="POST" id="checkout-form">
    @csrf

    <div style="display: grid; grid-template-columns: 1fr 400px; gap: 32px; align-items: start;">
        <!-- Left Column: Delivery & Payment Details -->
        <div style="display: flex; flex-direction: column; gap: 24px;">

            <!-- 1. Customer Information Snapshot -->
            <div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 24px;">
                <h3 style="font-size: 18px; font-weight: 800; margin-bottom: 14px; display: flex; align-items: center; gap: 10px;">
                    <span style="width: 28px; height: 28px; border-radius: 50%; background: var(--primary); color: white; display: inline-flex; align-items: center; justify-content: center; font-size: 14px;">1</span>
                    Customer Information
                </h3>
                <div style="font-size: 14px; line-height: 1.6; color: var(--gray-700);">
                    <p><strong>Name:</strong> {{ $user->name }}</p>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Account Phone:</strong> {{ $user->phone ?? 'Not set' }}</p>
                </div>
            </div>

            <!-- 2. Delivery Address -->
            <div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 24px;">
                <h3 style="font-size: 18px; font-weight: 800; margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
                    <span style="width: 28px; height: 28px; border-radius: 50%; background: var(--primary); color: white; display: inline-flex; align-items: center; justify-content: center; font-size: 14px;">2</span>
                    Delivery Address
                </h3>

                @if($addresses->isNotEmpty())
                    <div style="margin-bottom: 20px;">
                        <label style="font-size: 14px; font-weight: 700; display: block; margin-bottom: 10px;">Choose Saved Address:</label>
                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            @foreach($addresses as $addr)
                                <label style="display: flex; align-items: flex-start; gap: 12px; padding: 14px; border: 1.5px solid {{ $defaultAddress && $defaultAddress->id == $addr->id ? 'var(--primary)' : 'var(--gray-200)' }}; border-radius: var(--radius); cursor: pointer; background: {{ $defaultAddress && $defaultAddress->id == $addr->id ? 'var(--primary-light)' : 'white' }};" onclick="toggleNewAddress(false)">
                                    <input type="radio" name="address_id" value="{{ $addr->id }}" {{ $defaultAddress && $defaultAddress->id == $addr->id ? 'checked' : '' }} style="margin-top: 4px;">
                                    <div style="font-size: 14px; line-height: 1.5;">
                                        <strong>{{ $addr->full_name }}</strong> ({{ $addr->phone }})
                                        <span style="background: #e2e8f0; font-size: 11px; padding: 2px 6px; border-radius: 4px; margin-left: 6px; text-transform: capitalize;">{{ $addr->type }}</span>
                                        <div style="color: var(--gray-700); margin-top: 4px;">{{ $addr->formatted_address }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Option to enter new address -->
                <div style="margin-top: 14px;">
                    <label style="font-size: 14px; font-weight: 700; display: flex; align-items: center; gap: 8px; cursor: pointer; color: var(--primary);">
                        <input type="radio" name="address_id" value="" id="new_address_radio" {{ $addresses->isEmpty() ? 'checked' : '' }} onclick="toggleNewAddress(true)">
                        <span>+ Enter a new delivery address</span>
                    </label>

                    <div id="new-address-fields" style="display: {{ $addresses->isEmpty() ? 'block' : 'none' }}; margin-top: 16px; padding: 18px; background: #f8fafc; border: 1px solid var(--gray-200); border-radius: var(--radius);">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 12px;">
                            <div>
                                <label style="font-size: 13px; font-weight: 700; display: block; margin-bottom: 4px;">Recipient Name *</label>
                                <input type="text" name="new_full_name" value="{{ old('new_full_name', $user->name) }}" style="width: 100%; padding: 10px; border: 1px solid var(--gray-300); border-radius: var(--radius-sm); font-size: 14px;">
                            </div>
                            <div>
                                <label style="font-size: 13px; font-weight: 700; display: block; margin-bottom: 4px;">Phone Number *</label>
                                <input type="text" name="new_phone" value="{{ old('new_phone', $user->phone) }}" placeholder="01712345678" style="width: 100%; padding: 10px; border: 1px solid var(--gray-300); border-radius: var(--radius-sm); font-size: 14px;">
                            </div>
                        </div>

                        <div style="margin-bottom: 12px;">
                            <label style="font-size: 13px; font-weight: 700; display: block; margin-bottom: 4px;">Street Address / House / Road *</label>
                            <textarea name="new_address_line" rows="2" placeholder="e.g. House #12, Road #4, Block B" style="width: 100%; padding: 10px; border: 1px solid var(--gray-300); border-radius: var(--radius-sm); font-size: 14px; font-family: inherit;">{{ old('new_address_line') }}</textarea>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px;">
                            <div>
                                <label style="font-size: 13px; font-weight: 700; display: block; margin-bottom: 4px;">Area / Locality</label>
                                <input type="text" name="new_area" value="{{ old('new_area') }}" placeholder="e.g. Dhanmondi" style="width: 100%; padding: 10px; border: 1px solid var(--gray-300); border-radius: var(--radius-sm); font-size: 14px;">
                            </div>
                            <div>
                                <label style="font-size: 13px; font-weight: 700; display: block; margin-bottom: 4px;">City / District *</label>
                                <input type="text" name="new_city" value="{{ old('new_city', 'Dhaka') }}" style="width: 100%; padding: 10px; border: 1px solid var(--gray-300); border-radius: var(--radius-sm); font-size: 14px;">
                            </div>
                            <div>
                                <label style="font-size: 13px; font-weight: 700; display: block; margin-bottom: 4px;">Postal Code</label>
                                <input type="text" name="new_postal_code" value="{{ old('new_postal_code') }}" placeholder="1209" style="width: 100%; padding: 10px; border: 1px solid var(--gray-300); border-radius: var(--radius-sm); font-size: 14px;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delivery Instructions & Slot -->
                <div style="margin-top: 18px; display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div>
                        <label style="font-size: 13px; font-weight: 700; display: block; margin-bottom: 4px;">Preferred Delivery Slot</label>
                        <select name="delivery_slot" style="width: 100%; padding: 10px; border: 1px solid var(--gray-300); border-radius: var(--radius-sm); font-size: 14px; background: white;">
                            <option value="Regular (Anytime 9am - 8pm)">Regular (Anytime 9am - 8pm)</option>
                            <option value="Morning (9am - 1pm)">Morning (9am - 1pm)</option>
                            <option value="Evening (4pm - 8pm)">Evening (4pm - 8pm)</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 13px; font-weight: 700; display: block; margin-bottom: 4px;">Delivery Notes (Optional)</label>
                        <input type="text" name="delivery_notes" placeholder="e.g. Call before arriving" style="width: 100%; padding: 10px; border: 1px solid var(--gray-300); border-radius: var(--radius-sm); font-size: 14px;">
                    </div>
                </div>
            </div>

            <!-- 3. Payment Method -->
            <div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 24px;">
                <h3 style="font-size: 18px; font-weight: 800; margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
                    <span style="width: 28px; height: 28px; border-radius: 50%; background: var(--primary); color: white; display: inline-flex; align-items: center; justify-content: center; font-size: 14px;">3</span>
                    Select Payment Method
                </h3>

                <div style="display: flex; flex-direction: column; gap: 14px;">
                    @if($enableCod)
                        <label style="display: flex; align-items: center; gap: 12px; padding: 16px; border: 1.5px solid var(--gray-200); border-radius: var(--radius); cursor: pointer;" onclick="togglePaymentFields('cod')">
                            <input type="radio" name="payment_method" value="cod" checked>
                            <div>
                                <strong style="font-size: 15px;">Cash on Delivery (COD)</strong>
                                <div style="font-size: 13px; color: var(--gray-500);">Pay cash upon physical delivery at your doorstep.</div>
                            </div>
                        </label>
                    @endif

                    @if($enableBkash)
                        <label style="display: flex; align-items: center; gap: 12px; padding: 16px; border: 1.5px solid var(--gray-200); border-radius: var(--radius); cursor: pointer;" onclick="togglePaymentFields('bkash')">
                            <input type="radio" name="payment_method" value="bkash">
                            <div>
                                <strong style="font-size: 15px; color: #e11d48;">bKash Manual Payment</strong>
                                <div style="font-size: 13px; color: var(--gray-500);">Send money via bKash to our official number and submit TrxID.</div>
                            </div>
                        </label>
                    @endif

                    @if($enableNagad)
                        <label style="display: flex; align-items: center; gap: 12px; padding: 16px; border: 1.5px solid var(--gray-200); border-radius: var(--radius); cursor: pointer;" onclick="togglePaymentFields('nagad')">
                            <input type="radio" name="payment_method" value="nagad">
                            <div>
                                <strong style="font-size: 15px; color: #ea580c;">Nagad Manual Payment</strong>
                                <div style="font-size: 13px; color: var(--gray-500);">Send money via Nagad to our official number and submit TrxID.</div>
                            </div>
                        </label>
                    @endif
                </div>

                <!-- Manual Payment Instructions Box -->
                <div id="manual-payment-box" style="display: none; margin-top: 18px; padding: 18px; background: #fffbeb; border: 1.5px solid #fde68a; border-radius: var(--radius);">
                    <h4 id="payment-instruction-title" style="font-size: 15px; font-weight: 700; color: #92400e; margin-bottom: 8px;">Mobile Payment Instructions</h4>
                    <p id="payment-instruction-text" style="font-size: 13px; line-height: 1.6; color: #78350f; margin-bottom: 12px;">
                        Please send the exact total amount (<strong>৳{{ number_format($total, 2) }}</strong>) to our official account.
                    </p>

                    <div style="margin-bottom: 14px;">
                        <span style="font-size: 13px; font-weight: 700; color: #78350f;">Official Account Number:</span>
                        <span id="payment-number" style="font-size: 16px; font-weight: 800; color: #1e3a8a; margin-left: 8px;">{{ $bkashNumber }}</span>
                    </div>

                    <div>
                        <label for="transaction_id" style="font-size: 13px; font-weight: 700; display: block; margin-bottom: 4px; color: #78350f;">
                            Transaction ID / Reference Number *
                        </label>
                        <input type="text" name="transaction_id" id="transaction_id" placeholder="e.g. 9J4K2L1M" style="width: 100%; max-width: 320px; padding: 10px; border: 1.5px solid #d97706; border-radius: var(--radius-sm); font-size: 14px; font-weight: 700; text-transform: uppercase;">
                        <small style="display: block; margin-top: 4px; color: #92400e;">Payment will be verified by admin before shipping.</small>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column: Order Summary -->
        <div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 24px; position: sticky; top: 90px;">
            <h3 style="font-size: 18px; font-weight: 800; margin-bottom: 16px; border-bottom: 1px solid var(--gray-100); padding-bottom: 10px;">
                Order Summary ({{ $items->count() }} items)
            </h3>

            <!-- Item List Preview -->
            <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 20px; max-height: 280px; overflow-y: auto; padding-right: 4px;">
                @foreach($items as $item)
                    @php
                        $product = $item->product;
                        $unitPrice = $product ? $product->price : $item->price;
                    @endphp
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 13px;">
                        <div style="display: flex; align-items: center; gap: 10px; max-width: 260px;">
                            <img src="{{ $product ? $product->image_url : '/uploads/products/default.png' }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 6px; border: 1px solid var(--gray-200);">
                            <div>
                                <strong style="display: block; line-height: 1.3; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $product ? $product->name : 'Product' }}</strong>
                                <span style="color: var(--gray-500);">Qty: {{ $item->quantity }} &times; ৳{{ number_format($unitPrice, 2) }}</span>
                            </div>
                        </div>
                        <span style="font-weight: 700;">৳{{ number_format($unitPrice * $item->quantity, 2) }}</span>
                    </div>
                @endforeach
            </div>

            <!-- Totals Breakdown -->
            <div style="border-top: 1px solid var(--gray-100); padding-top: 14px; margin-bottom: 16px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px; color: var(--gray-700);">
                    <span>Subtotal</span>
                    <span style="font-weight: 700;">৳{{ number_format($subtotal, 2) }}</span>
                </div>

                @if($discount > 0)
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px; color: var(--success);">
                        <span>Coupon Discount ({{ $coupon_code }})</span>
                        <span style="font-weight: 700;">-৳{{ number_format($discount, 2) }}</span>
                    </div>
                @endif

                <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px; color: var(--gray-700);">
                    <span>Delivery Charge</span>
                    <span style="font-weight: 700;">
                        @if($delivery_charge > 0)
                            ৳{{ number_format($delivery_charge, 2) }}
                        @else
                            <strong style="color: var(--success);">FREE</strong>
                        @endif
                    </span>
                </div>

                <div style="border-top: 2px solid var(--gray-100); padding-top: 14px; margin-top: 10px; display: flex; justify-content: space-between; align-items: baseline;">
                    <span style="font-size: 16px; font-weight: 800;">Payable Amount</span>
                    <span style="font-size: 26px; font-weight: 800; color: var(--primary);">৳{{ number_format($total, 2) }}</span>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                Place Order Now &rarr;
            </button>

            <p style="font-size: 12px; color: var(--gray-500); text-align: center; margin-top: 12px;">
                🔒 By placing your order, you agree to our Terms of Service and inventory policies.
            </p>
        </div>
    </div>
</form>

<script>
    function toggleNewAddress(show) {
        document.getElementById('new-address-fields').style.display = show ? 'block' : 'none';
        if (show) {
            document.getElementById('new_address_radio').checked = true;
        }
    }

    function togglePaymentFields(method) {
        const box = document.getElementById('manual-payment-box');
        const numElem = document.getElementById('payment-number');
        const titleElem = document.getElementById('payment-instruction-title');

        if (method === 'bkash') {
            box.style.display = 'block';
            numElem.textContent = '{{ $bkashNumber }} (Personal / Merchant)';
            titleElem.textContent = 'bKash Payment Instructions';
        } else if (method === 'nagad') {
            box.style.display = 'block';
            numElem.textContent = '{{ $nagadNumber }} (Personal / Merchant)';
            titleElem.textContent = 'Nagad Payment Instructions';
        } else {
            box.style.display = 'none';
        }
    }
</script>
@endsection
