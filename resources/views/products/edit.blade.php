@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')

<div class="container">

    <h1>Edit Product: {{ $product->name }}</h1>

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
        enctype="multipart/form-data"
    >
        @csrf
        @method('PUT')

         
        <div class="form-group">
            <label for="name">Product Name</label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $product->name) }}"
                placeholder="Enter product name"
                required
            >
        </div>

        
        <div class="form-group">
            <label for="category_id">Category</label>
            <select
                id="category_id"
                name="category_id"
                required
            >
                <option value="">Select Category</option>
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

        
        <div class="form-group">
            <label for="image">Product Image</label>
            @if ($product->image && file_exists(public_path($product->image)))
                <div style="margin-bottom: 10px;">
                    <img
                        src="{{ asset($product->image) }}"
                        alt="{{ $product->name }}"
                        style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 1px solid #d1d5db;"
                    >
                </div>
            @endif
            <input
                type="file"
                id="image"
                name="image"
                accept="image/*"
            >
            <small style="color: #6b7280;">Leave blank if you don't want to change the image.</small>
        </div>

         
        <div class="form-group">
            <label for="qty">Quantity</label>
            <input
                type="number"
                id="qty"
                name="qty"
                value="{{ old('qty', $product->qty) }}"
                placeholder="Enter stock quantity"
                min="0"
                required
            >
        </div>

       
        <div class="form-group">
            <label for="price">Price (৳)</label>
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

    <a
        class="back-link"
        href="{{ route('product.index') }}"
    >
        ← Back to Products
    </a>

</div>

@endsection