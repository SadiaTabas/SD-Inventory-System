@extends('layouts.app')

@section('title', 'Categories')

@section('content')

<div class="container">

    <h1>Category Management</h1>

    @if (session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="error-box">
            {{ session('error') }}
        </div>
    @endif

    <div class="top-actions">
        <a
            class="create-button"
            href="{{ route('category.create') }}"
        >
            + Create New Category
        </a>
    </div>

    <div class="table-container">

        <table>

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Created By</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($categories as $category)

                    <tr>

                        <td>
                            {{ $category->id }}
                        </td>

                        <td>
                            <strong>{{ $category->name }}</strong>
                        </td>

                        <td>
                            {{ $category->description ?: 'No description' }}
                        </td>

                        <td>
                            @if ($category->status)
                                <span class="status active">Active</span>
                            @else
                                <span class="status inactive">Inactive</span>
                            @endif
                        </td>

                        <td>
                            <small style="color: #6b7280;">{{ $category->created_by ?? 'System' }}</small>
                        </td>

                        <td>

                            <div class="action-buttons">

                                <a
                                    class="edit-button"
                                    href="{{ route('category.edit', $category) }}"
                                >
                                    Edit
                                </a>

                                <form
                                    class="delete-form"
                                    method="POST"
                                    action="{{ route('category.destroy', $category) }}"
                                    onsubmit="return confirm('Are you sure you want to delete this category?');"
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
                            colspan="6"
                            class="empty-message"
                        >
                            No categories found.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection