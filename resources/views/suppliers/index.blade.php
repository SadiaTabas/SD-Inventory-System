@extends('layouts.app')

@section('title', 'Suppliers Management')

@section('content')
<div class="container" style="max-width: 1280px;">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h1 style="text-align: left; margin: 0;">Suppliers Directory</h1>
            <p style="color: #6b7280; font-size: 14px; margin-top: 4px;">Manage wholesale vendors, supplier contacts, and linked purchase order history.</p>
        </div>
        <a href="{{ route('suppliers.create') }}" class="create-button" style="margin-bottom: 0;">+ Add Supplier</a>
    </div>

    @if (session('success'))
        <div class="success-message">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="error-box" style="max-width: 100%;">{{ session('error') }}</div>
    @endif

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Supplier / Company</th>
                    <th>Contact Person</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Purchases</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $supplier)
                    <tr>
                        <td>
                            <strong>{{ $supplier->name }}</strong>
                            @if($supplier->company_name)
                                <div style="font-size: 12px; color: #6b7280;">{{ $supplier->company_name }}</div>
                            @endif
                        </td>
                        <td>{{ $supplier->contact_person ?? '—' }}</td>
                        <td>{{ $supplier->phone ?? '—' }}</td>
                        <td>{{ $supplier->email ?? '—' }}</td>
                        <td style="font-size: 13px; max-width: 200px;">{{ $supplier->address ?? '—' }}</td>
                        <td><strong>{{ $supplier->purchases_count }} Orders</strong></td>
                        <td>
                            <span class="status {{ $supplier->status ? 'active' : 'inactive' }}">
                                {{ $supplier->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('suppliers.edit', $supplier) }}" class="edit-button">Edit</a>
                                <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" onsubmit="return confirm('Delete this supplier?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-button">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="empty-message">No suppliers registered yet. Click "+ Add Supplier" to get started.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $suppliers->links() }}
        </div>
    </div>

</div>
@endsection
