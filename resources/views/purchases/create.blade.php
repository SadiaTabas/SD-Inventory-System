@extends('layouts.app')

@section('title', 'Create Purchase Order')

@section('content')
<div class="container" style="max-width: 900px;">
    <h1>Create Purchase Order</h1>

    @if ($errors->any())
        <div class="error-box">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-container" style="max-width: 900px;">
        <form action="{{ route('purchases.store') }}" method="POST">
            @csrf

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                <div class="form-group">
                    <label>Supplier *</label>
                    <select name="supplier_id" required>
                        <option value="">-- Choose Supplier --</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->company_name ?? 'Vendor' }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Purchase Date *</label>
                    <input type="date" name="purchase_date" value="{{ now()->toDateString() }}" required>
                </div>
            </div>

            <!-- Items Dynamic Table -->
            <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 12px;">Purchase Items</h3>
            <div id="items-container" style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 20px;">
                <div class="item-row" style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 12px; background: #f8fafc; padding: 12px; border-radius: 8px;">
                    <div>
                        <label style="font-size: 12px; font-weight: 700;">Product *</label>
                        <select name="items[0][product_id]" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 6px;">
                            <option value="">-- Select Product --</option>
                            @foreach($products as $prod)
                                <option value="{{ $prod->id }}">{{ $prod->name }} (Current: {{ $prod->qty }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 12px; font-weight: 700;">Quantity *</label>
                        <input type="number" name="items[0][quantity]" min="1" value="10" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 6px;">
                    </div>
                    <div>
                        <label style="font-size: 12px; font-weight: 700;">Unit Cost (৳) *</label>
                        <input type="number" step="0.01" name="items[0][unit_cost]" min="0" value="0.00" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 6px;">
                    </div>
                </div>
            </div>

            <button type="button" onclick="addItemRow()" style="padding: 6px 14px; background: #f3f4f6; border: 1px solid #ccc; border-radius: 6px; font-weight: 600; cursor: pointer; margin-bottom: 20px;">+ Add Another Item</button>

            <div class="form-group">
                <label>Notes (Optional)</label>
                <textarea name="notes" placeholder="Invoice reference, transport details..."></textarea>
            </div>

            <button type="submit" class="primary-button">Create Purchase Order</button>
        </form>
    </div>

    <a href="{{ route('purchases.index') }}" class="back-link">← Back to Purchases</a>
</div>

<script>
    let rowIndex = 1;
    const productsHtml = `@foreach($products as $prod)<option value="{{ $prod->id }}">{{ $prod->name }} (Current: {{ $prod->qty }})</option>@endforeach`;

    function addItemRow() {
        const div = document.createElement('div');
        div.className = 'item-row';
        div.style = 'display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 12px; background: #f8fafc; padding: 12px; border-radius: 8px;';
        div.innerHTML = `
            <div>
                <label style="font-size: 12px; font-weight: 700;">Product *</label>
                <select name="items[${rowIndex}][product_id]" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 6px;">
                    <option value="">-- Select Product --</option>
                    ${productsHtml}
                </select>
            </div>
            <div>
                <label style="font-size: 12px; font-weight: 700;">Quantity *</label>
                <input type="number" name="items[${rowIndex}][quantity]" min="1" value="10" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 6px;">
            </div>
            <div>
                <label style="font-size: 12px; font-weight: 700;">Unit Cost (৳) *</label>
                <input type="number" step="0.01" name="items[${rowIndex}][unit_cost]" min="0" value="0.00" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 6px;">
            </div>
        `;
        document.getElementById('items-container').appendChild(div);
        rowIndex++;
    }
</script>
@endsection
