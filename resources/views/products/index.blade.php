@extends('layouts.app')

@section('title', 'Products')

@section('content')

<div class="container">

    <h1>Product Inventory</h1>

    {{-- Success Message --}}
    @if (session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
    @endif

    {{-- Create Product Button --}}
    <div class="top-actions">
        <a
            class="create-button"
            href="{{ route('product.create') }}"
        >
            + Create New Product
        </a>
    </div>

    <div class="table-container">

        <table>

            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Stock</th>
                    <th>Price</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($products as $product)

                    <tr>

                        <td>
                            @if ($product->image && file_exists(public_path($product->image)))
                                <img
                                    src="{{ asset($product->image) }}"
                                    alt="{{ $product->name }}"
                                    style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; border: 1px solid #e5e7eb;"
                                >
                            @else
                                <div style="width: 50px; height: 50px; background: #e0f2fe; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                                    📦
                                </div>
                            @endif
                        </td>

                        <td>
                            <strong>{{ $product->name }}</strong>
                            <br>
                            <small style="color: #6b7280;">Added by: {{ $product->created_by ?? 'Admin' }}</small>
                        </td>

                        <td>
                            <span style="background: #ede9fe; color: #6d28d9; padding: 4px 10px; border-radius: 12px; font-weight: bold; font-size: 13px;">
                                {{ $product->category->name ?? 'Uncategorized' }}
                            </span>
                        </td>

                        <td>
                            @if ($product->qty <= 5)
                                <span class="stock-danger">Low: {{ $product->qty }}</span>
                            @else
                                <span style="font-weight: bold; color: #27ae60;">{{ $product->qty }}</span>
                            @endif
                        </td>

                        <td>
                            <strong>৳{{ number_format($product->price, 2) }}</strong>
                        </td>

                        <td>
                            <small style="color: #4b5563;">
                                {{ Str::limit($product->description ?: 'No description provided', 50) }}
                            </small>
                        </td>

                        <td>

                            <div class="action-buttons">

                                {{-- Edit --}}
                                <a
                                    class="edit-button"
                                    href="{{ route('product.edit', $product) }}"
                                >
                                    Edit
                                </a>

                                {{-- Delete --}}
                                <form
                                    class="delete-form"
                                    method="POST"
                                    action="{{ route('product.destroy', $product) }}"
                                    onsubmit="return confirm('Are you sure you want to delete this product?');"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        class="delete-button"
                                        type="submit"
                                    >
                                        Delete
                                    </button>
                                </form>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td
                            colspan="7"
                            class="empty-message"
                        >
                            No products found in inventory.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection