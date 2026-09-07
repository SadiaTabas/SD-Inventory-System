@extends('layouts.app')

@section('title', 'Create Product')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">

<style>
    

    .product-sheet-card {
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

    .product-sheet-card * {
        box-sizing: border-box;
    }

    /* Header */

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

    /* Two column layout */

    .sheet-grid-two-col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin-bottom: 20px;
    }

    /* Section heading */

    .sheet-section-heading {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 8px;
    }

    /* Field */

    .sheet-field {
        margin-bottom: 18px;
    }

    .sheet-label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #475569;
        margin-bottom: 5px;
    }

    /* Inputs */

    .sheet-input,
    .sheet-textarea,
    .sheet-select {
        width: 100%;
        padding: 9px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 13.5px;
        color: #1e293b;
        background: #ffffff;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .sheet-input:focus,
    .sheet-textarea:focus,
    .sheet-select:focus {
        border-color: #0284c7;
        box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.10);
    }

    .sheet-textarea {
        resize: vertical;
        min-height: 120px;
    }

    /* Meta box */

    .sheet-meta-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 15px;
        margin-bottom: 20px;
    }

    .sheet-meta-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        font-size: 13.5px;
        gap: 15px;
    }

    .sheet-meta-row:last-child {
        margin-bottom: 0;
    }

    /* Image upload */

    .image-upload-box {
        border: 1px dashed #94a3b8;
        border-radius: 6px;
        padding: 18px;
        background: #f8fafc;
        text-align: center;
        transition: border-color 0.2s, background 0.2s;
    }

    .image-upload-box:hover {
        border-color: #0284c7;
        background: #f0f9ff;
    }

    .image-upload-box input[type="file"] {
        width: 100%;
        font-size: 13px;
    }

    .image-preview-wrapper {
        display: none;
        margin-top: 15px;
        text-align: center;
    }

    .image-preview {
        max-width: 180px;
        max-height: 150px;
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        object-fit: cover;
    }

    /* Product information box */

    .product-info-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 15px;
        margin-top: 5px;
    }

    .product-info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 7px 0;
        font-size: 13px;
        border-bottom: 1px solid #e2e8f0;
    }

    .product-info-row:last-child {
        border-bottom: none;
    }

    .product-info-label {
        color: #64748b;
    }

    .product-info-value {
        font-weight: 600;
        color: #334155;
    }

    /* Price */

    .price-wrapper {
        position: relative;
    }

    .price-symbol {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #475569;
        font-weight: 600;
        font-size: 14px;
        pointer-events: none;
    }

    .price-input {
        padding-left: 32px !important;
    }

    /* Bottom buttons */

    .product-submit-area {
        text-align: right;
        margin-top: 25px;
        border-top: 1px solid #e2e8f0;
        padding-top: 18px;
    }

    .btn-submit-product {
        background: #0284c7;
        color: #ffffff;
        border: none;
        padding: 10px 24px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: bold;
        cursor: pointer;
        transition: background 0.2s;
    }

    .btn-submit-product:hover {
        background: #0369a1;
    }

    .btn-cancel-product {
        margin-right: 15px;
        color: #64748b;
        text-decoration: none;
        font-size: 13.5px;
    }

    .btn-cancel-product:hover {
        color: #334155;
    }

    /* Tom Select */

    .ts-control {
        border-radius: 6px !important;
        padding: 8px 10px !important;
        border: 1px solid #cbd5e1 !important;
        font-size: 13px !important;
        min-height: 38px !important;
        box-shadow: none !important;
    }

    .ts-control.focus {
        border-color: #0284c7 !important;
        box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.10) !important;
    }

    .ts-dropdown {
        font-size: 13px;
    }

    /* Error box */

    .error-box {
        background: #fee2e2;
        border: 1px solid #fca5a5;
        color: #b91c1c;
        padding: 12px 16px;
        border-radius: 6px;
        margin-bottom: 20px;
    }

    .error-box ul {
        margin: 0;
        padding-left: 20px;
    }

    /* Responsive */

    @media (max-width: 768px) {
        .product-sheet-card {
            margin: 15px;
            width: calc(100% - 30px);
            padding: 25px 20px;
        }

        .sheet-grid-two-col {
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .sheet-title {
            font-size: 24px;
        }

        .sheet-logo-badge {
            width: 55px;
            height: 55px;
            font-size: 17px;
        }

        .product-submit-area {
            text-align: center;
        }
    }
</style>


<div class="product-sheet-card">

   
    <div class="sheet-top-header">

        <h1 class="sheet-title">
            Product Form
        </h1>

        <div class="sheet-logo-badge">
            SD
        </div>

    </div>

    <hr class="sheet-divider">


   
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
        method="POST"
        action="{{ route('product.store') }}"
        enctype="multipart/form-data"
        id="productForm"
    >

        @csrf


        
        <div class="sheet-grid-two-col">

            {{-- LEFT: BASIC INFORMATION --}}
            <div>

                <div class="sheet-section-heading">
                    Product Information:
                </div>


                {{-- Product Name --}}
                <div class="sheet-field">

                    <label
                        for="name"
                        class="sheet-label"
                    >
                        Product Name:
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="sheet-input"
                        value="{{ old('name') }}"
                        placeholder="Enter product name"
                        required
                    >

                </div>


                {{-- Category --}}
                <div class="sheet-field">

                    <label
                        for="category_id"
                        class="sheet-label"
                    >
                        Category:
                    </label>

                    <select
                        id="category_id"
                        name="category_id"
                        required
                    >

                        <option value="">
                            Search or select category...
                        </option>

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


                {{-- Quantity --}}
                <div class="sheet-field">

                    <label
                        for="qty"
                        class="sheet-label"
                    >
                        Quantity:
                    </label>

                    <input
                        type="number"
                        id="qty"
                        name="qty"
                        class="sheet-input"
                        value="{{ old('qty', 1) }}"
                        min="0"
                        placeholder="Enter quantity"
                        required
                    >

                </div>


                {{-- Price --}}
                <div class="sheet-field">

                    <label
                        for="price"
                        class="sheet-label"
                    >
                        Price:
                    </label>

                 <div class="price-wrapper" style="position: relative; display: flex; align-items: center;">
    <span class="price-symbol" style="position: absolute; left: 12px; font-weight: 600; color: #64748b; pointer-events: none;">
        BDT
    </span>
    <input 
        type="number" 
        step="0.01" 
        min="0" 
        name="price" 
        id="price" 
        value="{{ old('price') }}" 
        placeholder="0.00" 
        required 
        style="padding-left: 50px; width: 100%; box-sizing: border-box;"
    >
</div>

                </div>

            </div>


            {{-- RIGHT: IMAGE + SUMMARY --}}
            <div>

                <div class="sheet-section-heading">
                    Product Details:
                </div>


                {{-- Product Image --}}
                <div class="sheet-field">

                    <label
                        for="image"
                        class="sheet-label"
                    >
                        Product Image:
                    </label>

                    <div class="image-upload-box">

                        <input
                            type="file"
                            id="image"
                            name="image"
                            accept="image/*"
                        >

                        <div
                            class="image-preview-wrapper"
                            id="imagePreviewWrapper"
                        >

                            <img
                                id="imagePreview"
                                class="image-preview"
                                src=""
                                alt="Product Preview"
                            >

                        </div>

                    </div>

                </div>


                {{-- Product Quick Information --}}
                <div class="product-info-box">

                    <div class="product-info-row">

                        <span class="product-info-label">
                            Product Name
                        </span>

                        <span
                            class="product-info-value"
                            id="previewProductName"
                        >
                            Not entered
                        </span>

                    </div>


                    <div class="product-info-row">

                        <span class="product-info-label">
                            Category
                        </span>

                        <span
                            class="product-info-value"
                            id="previewCategory"
                        >
                            Not selected
                        </span>

                    </div>


                    <div class="product-info-row">

                        <span class="product-info-label">
                            Quantity
                        </span>

                        <span
                            class="product-info-value"
                            id="previewQuantity"
                        >
                            1
                        </span>

                    </div>


                    <div class="product-info-row">

                        <span class="product-info-label">
                            Unit Price
                        </span>

                        <span
                            class="product-info-value"
                            id="previewPrice"
                        >
                            ৳ 0.00
                        </span>

                    </div>

                </div>

            </div>

        </div>


         
        <div>

            <div class="sheet-section-heading">
                Product Description:
            </div>

            <div class="sheet-field">

                <label
                    for="description"
                    class="sheet-label"
                >
                    Description:
                </label>

                <textarea
                    id="description"
                    name="description"
                    class="sheet-textarea"
                    placeholder="Enter product description..."
                >{{ old('description') }}</textarea>

            </div>

        </div>


       
        <div class="product-submit-area">

            <a
                href="{{ route('product.index') }}"
                class="btn-cancel-product"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="btn-submit-product"
            >
                Create Product
            </button>

        </div>

    </form>

</div>

 
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>


<script>
document.addEventListener('DOMContentLoaded', function () {

     

    new TomSelect('#category_id', {
        create: false,
        sortField: {
            field: 'text',
            direction: 'asc'
        },
        placeholder: 'Search or select category...',

        onChange: function (value) {

            const selectedOption =
                document.querySelector(
                    '#category_id option[value="' + value + '"]'
                );

            const categoryPreview =
                document.getElementById('previewCategory');

            if (selectedOption && value) {
                categoryPreview.innerText =
                    selectedOption.textContent.trim();
            } else {
                categoryPreview.innerText =
                    'Not selected';
            }
        }
    });


  

    const nameInput =
        document.getElementById('name');

    const namePreview =
        document.getElementById('previewProductName');

    nameInput.addEventListener('input', function () {

        if (this.value.trim() !== '') {
            namePreview.innerText =
                this.value;
        } else {
            namePreview.innerText =
                'Not entered';
        }

    });


  

    const qtyInput =
        document.getElementById('qty');

    const qtyPreview =
        document.getElementById('previewQuantity');

    qtyInput.addEventListener('input', function () {

        qtyPreview.innerText =
            this.value || '0';

    });


    

    const priceInput =
        document.getElementById('price');

    const pricePreview =
        document.getElementById('previewPrice');

    priceInput.addEventListener('input', function () {

        const price =
            parseFloat(this.value) || 0;

        pricePreview.innerText =
            'BDT ' + price.toFixed(2);

    });


    

    const imageInput =
        document.getElementById('image');

    const imagePreview =
        document.getElementById('imagePreview');

    const imagePreviewWrapper =
        document.getElementById('imagePreviewWrapper');


    imageInput.addEventListener('change', function () {

        const file = this.files[0];

        if (!file) {

            imagePreviewWrapper.style.display =
                'none';

            imagePreview.src =
                '';

            return;
        }


        if (!file.type.startsWith('image/')) {

            alert('Please select a valid image file.');

            this.value = '';

            imagePreviewWrapper.style.display =
                'none';

            return;
        }


        const reader =
            new FileReader();


        reader.onload = function (event) {

            imagePreview.src =
                event.target.result;

            imagePreviewWrapper.style.display =
                'block';

        };


        reader.readAsDataURL(file);

    });


   

    const initialCategory =
        document.getElementById('category_id').value;

    if (initialCategory) {

        const selectedOption =
            document.querySelector(
                '#category_id option[value="' +
                initialCategory +
                '"]'
            );

        if (selectedOption) {

            document.getElementById(
                'previewCategory'
            ).innerText =
                selectedOption.textContent.trim();

        }

    }


    const initialName =
        nameInput.value.trim();

    if (initialName) {

        namePreview.innerText =
            initialName;

    }


    const initialQty =
        qtyInput.value || 0;

    qtyPreview.innerText =
        initialQty;


    const initialPrice =
        parseFloat(priceInput.value) || 0;

    pricePreview.innerText =
        'BDT ' + initialPrice.toFixed(2);

});
</script>

@endsection