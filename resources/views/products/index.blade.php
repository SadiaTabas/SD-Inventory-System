@extends('layouts.app')

@section('title', 'Products')

@section('content')

<div class="container">

 
<h1>Product List</h1>

{{-- Success Message --}}
@if (session('success'))
    <div class="success-message">
        {{ session('success') }}
    </div>
@endif

{{-- Create Product --}}
<a
    class="create-button"
    href="{{ route('product.create') }}"
>
    + Create New Product
</a>

<div class="table-container">

    <table>

        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Category</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>

            @forelse ($products as $product)

                <tr>

                    <td class="id">
                        {{ $product->id }}
                    </td>

                    <td>
                        <strong>{{ $product->name }}</strong>
                    </td>

                    <td>
                        {{ $product->category->name ?? 'No Category' }}
                    </td>

                    <td>
                        {{ $product->qty }}
                    </td>

                    <td>
                        {{ $product->price }}
                    </td>

                    <td>
                        {{ $product->description ?: 'No description' }}
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
                        No products found.
                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>

</div>

{{-- Back to Categories --}}
<a
    class="back-link"
    href="{{ route('category.index') }}"
>
    ← Back to Categories
</a>
 

</div>

@endsection
