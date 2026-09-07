@extends('layouts.app')

@section('title', 'Products')

@section('content')

<style>
    .product-page-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        max-width: 1100px;
        width: 96%;
        margin: 25px auto;
        padding: 30px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        box-sizing: border-box;
    }

    .top-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .table-scroll-wrapper {
        width: 100%;
        overflow-x: auto;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        box-sizing: border-box;
    }

    .inventory-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .inventory-table th {
        background-color: #2c3e50;
        color: #ffffff;
        font-size: 13px;
        font-weight: 700;
        padding: 12px 14px;
        text-align: left;
    }

    .inventory-table td {
        padding: 12px 14px;
        border-bottom: 1px solid #e2e8f0;
        vertical-align: middle;
        font-size: 13px;
        word-wrap: break-word;
    }

    .btn-action-group {
        display: flex;
        gap: 6px;
        align-items: center;
    }

    .btn-edit {
        background: #f59e0b;
        color: #ffffff;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: bold;
        text-decoration: none;
    }

    .btn-delete {
        background: #ef4444;
        color: #ffffff;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: bold;
        border: none;
        cursor: pointer;
    }
</style>

<div class="product-page-card">

    <h1 style="margin-top: 0; color: #1e293b; font-size: 26px;">Product Inventory</h1>

    @if (session('success'))
        <div style="background: #dcfce7; border: 1px solid #86efac; color: #15803d; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; font-size: 13.5px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="top-toolbar">
        <a
            href="{{ route('product.create') }}"
            style="background: #0284c7; color: white; padding: 9px 18px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 13.5px;"
        >
            + Create New Product
        </a>

        <a
            href="{{ route('product.import.form') }}"
            style="background-color: #10b981; color: white; padding: 9px 18px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 13.5px;"
        >
            ↑ Import Excel
        </a>
    </div>

    <div class="table-scroll-wrapper">
        <table class="inventory-table">
            <thead>
                <tr>
                    <th style="width: 8%;">Image</th>
                    <th style="width: 25%;">Name</th>
                    <th style="width: 15%;">Category</th>
                    <th style="width: 8%;">Stock</th>
                    <th style="width: 12%;">Price</th>
                    <th style="width: 18%;">Description</th>
                    <th style="width: 14%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td>
                            @php
                                $imgSrc = null;
                                if (!empty($product->image)) {
                                    if (Str::startsWith($product->image, ['http://', 'https://'])) {
                                        $imgSrc = $product->image;
                                    } elseif (file_exists(public_path($product->image))) {
                                        $imgSrc = asset($product->image);
                                    } elseif (file_exists(public_path('storage/' . $product->image))) {
                                        $imgSrc = asset('storage/' . $product->image);
                                    }
                                }
                            @endphp

                            @if ($imgSrc)
                                <img
                                    src="{{ $imgSrc }}"
                                    alt="{{ $product->name }}"
                                    style="width: 48px; height: 48px; object-fit: cover; border-radius: 6px; border: 1px solid #e2e8f0; display: block;"
                                    onerror="this.onerror=null; this.src='https://placehold.co/48x48?text=No+Img';"
                                >
                            @else
                                <div style="width: 48px; height: 48px; background: #e0f2fe; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                                    📦
                                </div>
                            @endif
                        </td>

                        <td>
                            <strong>{{ $product->name }}</strong>
                            <br>
                            <small style="color: #64748b;">Added by: {{ $product->created_by ?? 'Admin' }}</small>
                        </td>

                        <td>
                            <span style="background: #ede9fe; color: #6d28d9; padding: 4px 10px; border-radius: 12px; font-weight: bold; font-size: 12px; display: inline-block;">
                                {{ $product->category->name ?? 'Uncategorized' }}
                            </span>
                        </td>

                        <td>
                            @if ($product->qty <= 5)
                                <span style="color: #ef4444; font-weight: bold;">Low: {{ $product->qty }}</span>
                            @else
                                <span style="font-weight: bold; color: #10b981;">{{ $product->qty }}</span>
                            @endif
                        </td>

                        <td>
                            <strong>৳{{ number_format($product->price, 2) }}</strong>
                        </td>

                        <td>
                            <span style="color: #475569; font-size: 12.5px;">
                                {{ Str::limit($product->description ?: 'No description', 45) }}
                            </span>
                        </td>

                        <td>
                            <div class="btn-action-group">
                                <a class="btn-edit" href="{{ route('product.edit', $product) }}">Edit</a>
                                <form
                                    method="POST"
                                    action="{{ route('product.destroy', $product) }}"
                                    onsubmit="return confirm('Delete this product?');"
                                    style="margin: 0;"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-delete" type="submit">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: #64748b; padding: 30px;">
                            No products found in inventory.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

@endsection