@extends('layouts.app')

@section('title', 'Create Customers')

@section('content')

<style>
    .customer-row-block {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 18px;
        margin-bottom: 18px;
    }
    .customer-row-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 14px;
        padding-bottom: 8px;
        border-bottom: 1px dashed #cbd5e1;
    }
    .customer-row-badge {
        font-size: 14px;
        font-weight: 700;
        color: #0284c7;
    }
    .btn-remove-customer {
        background: #ef4444;
        color: #ffffff;
        border: none;
        border-radius: 4px;
        padding: 4px 10px;
        font-size: 12px;
        font-weight: bold;
        cursor: pointer;
    }
    .btn-add-customer {
        background: #0284c7;
        color: #ffffff;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 13.5px;
        font-weight: bold;
        cursor: pointer;
    }
</style>

<div class="container">

    <div style="display: flex; justify-content: space-between; align-items: center; max-width: 600px; margin: 0 auto 15px auto;">
        <h1 style="margin: 0;">Create New Customer</h1>
        <button type="button" class="btn-add-customer" onclick="addCustomerRow()">+ Add Customer</button>
    </div>

    @if ($errors->any())
        <div class="error-box" style="max-width: 600px; margin: 0 auto 15px auto;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        class="form-container"
        method="POST"
        action="{{ route('customer.store') }}"
        style="max-width: 600px; margin: 0 auto;"
    >
        @csrf

        <div id="customers-container">
            {{-- Injected dynamically --}}
        </div>

        <button
            type="submit"
            class="primary-button"
            style="width: 100%; margin-top: 5px;"
        >
            Save Customers
        </button>

    </form>

    <div style="max-width: 600px; margin: 15px auto 0 auto;">
        <a
            class="back-link"
            href="{{ route('customer.index') }}"
        >
            ← Back to Customers
        </a>
    </div>

</div>

<script>
let customerIndex = 0;

function addCustomerRow() {
    const container = document.getElementById('customers-container');
    const index = customerIndex++;

    const row = document.createElement('div');
    row.className = 'customer-row-block';
    row.id = `customer-block-${index}`;

    row.innerHTML = `
        <div class="customer-row-top">
            <span class="customer-row-badge">Customer #${container.children.length + 1}</span>
            <button type="button" class="btn-remove-customer" onclick="removeCustomerRow(${index})">✕ Remove</button>
        </div>

        <div class="form-group" style="margin-bottom: 12px;">
            <label for="name_${index}">Customer Name</label>
            <input
                type="text"
                id="name_${index}"
                name="customers[${index}][name]"
                placeholder="Enter customer name"
                required
            >
        </div>

        <div class="form-group" style="margin-bottom: 12px;">
            <label for="email_${index}">Email</label>
            <input
                type="email"
                id="email_${index}"
                name="customers[${index}][email]"
                placeholder="Enter email address"
                required
            >
        </div>

        <div class="form-group" style="margin-bottom: 12px;">
            <label for="phone_${index}">Phone</label>
            <input
                type="text"
                id="phone_${index}"
                name="customers[${index}][phone]"
                placeholder="Enter phone number"
            >
        </div>

        <div class="form-group" style="margin-bottom: 12px;">
            <label for="address_${index}">Address</label>
            <textarea
                id="address_${index}"
                name="customers[${index}][address]"
                placeholder="Enter customer street address"
                rows="2"
            ></textarea>
        </div>

        <div class="form-group" style="margin-bottom: 12px;">
            <label for="city_${index}">City</label>
            <input
                type="text"
                id="city_${index}"
                name="customers[${index}][city]"
                value="Dhaka"
                placeholder="Enter city"
            >
        </div>

        <div class="form-group" style="margin-bottom: 0;">
            <label for="status_${index}">Status</label>
            <select id="status_${index}" name="customers[${index}][status]">
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
        </div>
    `;

    container.appendChild(row);
    refreshCustomerLabels();
}

function removeCustomerRow(index) {
    const container = document.getElementById('customers-container');
    if (container.children.length <= 1) {
        alert("You must provide at least one customer.");
        return;
    }

    const row = document.getElementById(`customer-block-${index}`);
    if (row) row.remove();
    refreshCustomerLabels();
}

function refreshCustomerLabels() {
    const rows = document.querySelectorAll('.customer-row-block');
    rows.forEach((row, idx) => {
        const badge = row.querySelector('.customer-row-badge');
        if (badge) badge.innerText = `Customer #${idx + 1}`;
    });
}

document.addEventListener('DOMContentLoaded', function () {
    addCustomerRow();
});
</script>

@endsection