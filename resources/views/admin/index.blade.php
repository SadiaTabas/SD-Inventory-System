@extends('layouts.app')

@section('title', 'Admin Management')

@section('content')

<div class="container">

    <h1>Admin User Management</h1>

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
            href="{{ route('admin.create') }}"
        >
            + Create New Admin
        </a>
    </div>

    <div class="table-container">

        <table>

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($admins as $admin)

                    <tr>

                        <td>{{ $admin->id }}</td>

                        <td>
                            <strong>{{ $admin->name }}</strong>
                            @if(Auth::id() === $admin->id)
                                <small style="color: #2563eb; font-weight: bold;">(You)</small>
                            @endif
                        </td>

                        <td>{{ $admin->email }}</td>

                        <td>{{ $admin->phone ?: 'N/A' }}</td>

                        <td>
                            @if ($admin->status)
                                <span class="status active">Active</span>
                            @else
                                <span class="status inactive">Inactive</span>
                            @endif
                        </td>

                        <td>

                            <div class="action-buttons">

                                <a
                                    class="edit-button"
                                    href="{{ route('admin.edit', $admin) }}"
                                >
                                    Edit
                                </a>

                                @if (Auth::id() !== $admin->id)
                                    <form
                                        class="delete-form"
                                        method="POST"
                                        action="{{ route('admin.destroy', $admin) }}"
                                        onsubmit="return confirm('Are you sure you want to delete this admin?');"
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
                                @endif

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="6" class="empty-message">No administrators found.</td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection