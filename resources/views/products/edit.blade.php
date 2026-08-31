@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')

<div class="container">

 
<h1>Edit Product</h1>

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
    action="{{ route('product.update', $product) }}"
>

    @csrf
    @method('PUT')

    <!-- Product Name -->
    <div class="form-group">

        <label for="name">Name</label>

        <input
            type="text"
            id="name"
            name="name"
            value="{{ old('name', $product->name) }}"
            placeholder="Enter product name"
            required
        >

    </div>


    <!-- Category -->
    <div class="form-group">

        <label for="category_id">Category</label>

        <select
            id="category_id"
            name="category_id"
            required
        >

            <option value="">
                Select Category
            </option>

            @foreach ($categories as $category)

                <option
                    value="{{ $category->id }}"
                    {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}
                >
                    {{ $category->name }}
                </option>

            @endforeach

        </select>

    </div>


    <!-- Quantity -->
    <div class="form-group">

        <label for="qty">Quantity</label>

        <input
            type="number"
            id="qty"
            name="qty"
            value="{{ old('qty', $product->qty) }}"
            placeholder="Enter quantity"
            min="0"
            required
        >

    </div>


    <!-- Price -->
    <div class="form-group">

        <label for="price">Price</label>

        <input
            type="number"
            id="price"
            name="price"
            value="{{ old('price', $product->price) }}"
            placeholder="Enter price"
            min="0"
            step="0.01"
            required
        >

    </div>


    <!-- Description -->
    <div class="form-group">

        <label for="description">Description</label>

        <textarea
            id="description"
            name="description"
            placeholder="Enter product description"
        >{{ old('description', $product->description) }}</textarea>

    </div>


    <!-- Submit -->
    <button
        type="submit"
        class="primary-button"
    >
        Update Product
    </button>

</form>


<!-- Back -->
<a
    class="back-link"
    href="{{ route('product.index') }}"
>
    ← Back to Products
</a>
 

</div>

@endsection
