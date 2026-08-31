<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Display customers.
     */
    public function index()
    {
        $customers = User::where('role', 'customer')
            ->with('addresses')
            ->latest()
            ->get();

        return view('customers.index', [
            'customers' => $customers
        ]);
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return view('customers.create');
    }

    /*
     Store customer.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:6',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'status' => 'required|boolean',
        ]);

        DB::transaction(function () use ($request) {
            $customer = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password ?? 'password123'),
                'role' => 'customer',
                'status' => $request->status,
                'created_by' => Auth::user()->name ?? 'Admin',
            ]);

            if ($request->filled('address')) {
                Address::create([
                    'user_id' => $customer->id,
                    'title' => 'Primary',
                    'contact_person_name' => $request->name,
                    'contact_phone' => $request->phone,
                    'address' => $request->address,
                    'city' => $request->city ?? 'Dhaka',
                    'is_default' => 1,
                ]);
            }
        });

        return redirect()
            ->route('customer.index')
            ->with('success', 'Customer created successfully.');
    }

    /*
     Show customer details.
     */
    public function show(User $customer)
    {
        if ($customer->role !== 'customer') {
            abort(404);
        }

        $customer->load(['addresses', 'orders.items.product']);

        return view('customers.show', [
            'customer' => $customer
        ]);
    }

    /*
     Show edit form.
     */
    public function edit(User $customer)
    {
        if ($customer->role !== 'customer') {
            abort(404);
        }

        $customer->load('addresses');

        return view('customers.edit', [
            'customer' => $customer
        ]);
    }

    /*
     Update customer.
     */
    public function update(Request $request, User $customer)
    {
        if ($customer->role !== 'customer') {
            abort(404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $customer->id,
            'phone' => 'nullable|string|max:50',
            'status' => 'required|boolean',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
        ]);

        DB::transaction(function () use ($request, $customer) {
            $customer->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'status' => $request->status,
                'updated_by' => Auth::user()->name ?? 'Admin',
            ]);

            if ($request->filled('address')) {
                Address::updateOrCreate(
                    ['user_id' => $customer->id, 'is_default' => 1],
                    [
                        'title' => 'Primary',
                        'contact_person_name' => $request->name,
                        'contact_phone' => $request->phone,
                        'address' => $request->address,
                        'city' => $request->city ?? 'Dhaka',
                    ]
                );
            }
        });

        return redirect()
            ->route('customer.index')
            ->with('success', 'Customer updated successfully.');
    }

    /*
     * Delete customer.
     */
    public function destroy(User $customer)
    {
        if ($customer->role !== 'customer') {
            abort(404);
        }

        $customer->delete();

        return redirect()
            ->route('customer.index')
            ->with('success', 'Customer deleted successfully.');
    }
}