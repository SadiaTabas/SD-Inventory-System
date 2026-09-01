@extends('layouts.app')

@section('title', 'Create Product')

@section('content')

 
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<style>
    .ts-control {
        border-radius: 6px !important;
        padding: 10px 12px !important;
        border: 1px solid #cbd5e1 !important;
        font-size: 14px !important;
    }
    .ts-dropdown {
        border-radius: 6px !important;
        border: 1px solid #cbd5e1 !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
    }
    .ts-dropdown .active {
        background-color: #3b82f6 !important;
        color: white !important;
    }
</style>

<div class="container">

    <h1>Create New Product</h1>

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
        action="{{ route('product.store') }}"
        enctype="multipart/form-data"
    >
        @csrf

        
        <div class="form-group">
            <label for="name">Product Name</label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name') }}"
                placeholder="Enter product name"
                required
            >
        </div>

        
        <div class="form-group">
            <label for="category_id">Category</label>
            <select
                id="category_id"
                name="category_id"
                placeholder="Search or select category..."
                required
            >
                <option value="">Select Category</option>
                @foreach ($categories as $category)
                    <option
                        value="{{ $category->id }}"
                        {{ old('category_id') == $category->id ? 'selected' : '' }}
                    >
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        
        <div class="form-group">
            <label for="image">Product Image</label>
            <input
                type="file"
                id="image"
                name="image"
                accept="image/*"
            >
        </div>

        
        <div class="form-group">
            <label for="qty">Quantity</label>
            <input
                type="number"
                id="qty"
                name="qty"
                value="{{ old('qty', 1) }}"
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
                value="{{ old('price') }}"
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
            >{{ old('description') }}</textarea>
        </div>

        
        <button
            type="submit"
            class="primary-button"
        >
            Save Product
        </button>

    </form>

    <a
        class="back-link"
        href="{{ route('product.index') }}"
    >
        ← Back to Products
    </a>

</div>
 
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    new TomSelect('#category_id', {
        create: false,
        sortField: {
            field: "text",
            direction: "asc"
        }
    });
});
</script>

@endsection