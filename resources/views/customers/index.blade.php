@extends('layouts.app')

@section('title', 'Customers')

@section('content')

<div class="container">

 
<h1>Customer Management</h1>

{{-- Success Message --}}
@if (session('success'))
    <div class="success-message">
        {{ session('success') }}
    </div>
@endif

{{-- Create Customer --}}
<a
    class="create-button"
    href="{{ route('customer.create') }}"
>
    + Create New Customer
</a>

<div class="table-container">

    <table>

        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>City</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>

            @forelse ($customers as $customer)

                <tr>

                    <td class="id">
                        {{ $customer->id }}
                    </td>

                    <td>
                        <strong>{{ $customer->name }}</strong>
                    </td>

                    <td>
                        {{ $customer->email }}
                    </td>

                    <td>
                        {{ $customer->phone }}
                    </td>

                    <td>
                        {{ $customer->city ?: 'N/A' }}
                    </td>

                    <td>

                        @if ($customer->status)

                            <span class="status active">
                                Active
                            </span>

                        @else

                            <span class="status inactive">
                                Inactive
                            </span>

                        @endif

                    </td>

                    <td>

                        <div class="action-buttons">

                            {{-- View --}}
                            <a
                                class="create-button"
                                href="{{ route('customer.show', $customer) }}"
                                style="margin: 0; padding: 8px 14px; background-color: #3498db;"
                            >
                                View
                            </a>

                            {{-- Edit --}}
                            <a
                                class="edit-button"
                                href="{{ route('customer.edit', $customer) }}"
                            >
                                Edit
                            </a>

                            {{-- Delete --}}
                            <form
                                class="delete-form"
                                method="POST"
                                action="{{ route('customer.destroy', $customer) }}"
                                onsubmit="return confirm('Are you sure you want to delete this customer?');"
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
                        No customers found.
                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>

</div>
 

</div>

@endsection
