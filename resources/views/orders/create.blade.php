@extends('layouts.app')

@section('title', 'Create Order')

@section('content')

<div class="container">

    <h1>Create New Order</h1>

    @if ($errors->any())

        <div class="error-box">

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
        action="{{ route('order.store') }}"
    >

        @csrf


        {{-- Customer --}}

        <div class="form-group">

            <label for="customer_id">
                Customer
            </label>

            <select
                id="customer_id"
                name="customer_id"
                required
            >

                <option value="">
                    Select Customer
                </option>

                @foreach ($customers as $customer)

                    <option
                        value="{{ $customer->id }}"
                        {{ old('customer_id') == $customer->id ? 'selected' : '' }}
                    >
                        {{ $customer->name }}
                        - {{ $customer->email }}
                    </option>

                @endforeach

            </select>

        </div>


        {{-- Product --}}

        <div class="form-group">

            <label for="product_id">
                Product
            </label>

            <select
                id="product_id"
                name="product_id"
                required
            >

                <option value="">
                    Select Product
                </option>

                @foreach ($products as $product)

                    <option
                        value="{{ $product->id }}"
                        {{ old('product_id') == $product->id ? 'selected' : '' }}
                    >
                        {{ $product->name }}
                        - ৳{{ number_format($product->price, 2) }}
                        - Stock: {{ $product->qty }}
                    </option>

                @endforeach

            </select>

        </div>


        {{-- Quantity --}}

        <div class="form-group">

            <label for="quantity">
                Quantity
            </label>

            <input
                type="number"
                id="quantity"
                name="quantity"
                value="{{ old('quantity', 1) }}"
                min="1"
                required
            >

        </div>


        {{-- Shipping Address --}}

        <div class="form-group">

            <label for="shipping_address">
                Shipping Address
            </label>

            <textarea
                id="shipping_address"
                name="shipping_address"
                placeholder="Enter delivery address"
                required
            >{{ old('shipping_address') }}</textarea>

        </div>


        <button
            type="submit"
            class="primary-button"
        >
            Create Order
        </button>

    </form>


    <a
        class="back-link"
        href="{{ route('order.index') }}"
    >
        ← Back to Orders
    </a>

</div>

@endsection