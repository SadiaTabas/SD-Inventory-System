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
    /*
      Display customers.
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

    /*
     Show create form.
     */
    public function create()
    {
        return view('customers.create');
    }

    /*
     Store customer (Supports both batch dynamic rows and single submission fallback).
     */
    public function store(Request $request)
    {
        $creatorName = Auth::user()->name ?? 'Admin';

        // 1. Dynamic Array Submission
        if ($request->has('customers') && is_array($request->customers)) {
            $request->validate([
                'customers'               => 'required|array|min:1',
                'customers.*.name'        => 'required|string|max:255',
                'customers.*.email'       => 'required|email|distinct|unique:users,email',
                'customers.*.phone'       => 'nullable|string|digits:11',
                'customers.*.address'     => 'nullable|string',
                'customers.*.city'        => 'nullable|string|max:100',
                'customers.*.status'      => 'required|in:0,1',
            ], [
                'customers.*.email.distinct' => 'Each customer in the list must have a distinct email address.',
                'customers.*.email.unique'   => 'One or more of the emails have already been taken.',
                'customers.*.phone.digits'   => 'Phone number should be 11 digits.',
            ]);

            DB::transaction(function () use ($request, $creatorName) {
                foreach ($request->customers as $item) {
                    $customer = User::create([
                        'name'       => $item['name'],
                        'email'      => $item['email'],
                        'phone'      => $item['phone'] ?? null,
                        'password'   => Hash::make('password123'),
                        'role'       => 'customer',
                        'status'     => (bool) $item['status'],
                        'created_by' => $creatorName,
                    ]);

                    if (!empty($item['address'])) {
                        Address::create([
                            'user_id'             => $customer->id,
                            'title'               => 'Primary',
                            'contact_person_name' => $item['name'],
                            'contact_phone'       => $item['phone'] ?? null,
                            'address'             => $item['address'],
                            'city'                => !empty($item['city']) ? $item['city'] : 'Dhaka',
                            'is_default'          => 1,
                        ]);
                    }
                }
            });

            return redirect()
                ->route('customer.index')
                ->with('success', 'Customers created successfully.');
        }

        // 2. Single Customer Fallback
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'nullable|string|digits:11',
            'password' => 'nullable|string|min:6',
            'address'  => 'nullable|string',
            'city'     => 'nullable|string|max:100',
            'status'   => 'required|boolean',
        ], [
            'email.unique' => 'This email has already been taken.',
            'phone.digits' => 'Phone number should be 11 digits.',
        ]);

        DB::transaction(function () use ($request, $creatorName) {
            $customer = User::create([
                'name'       => $request->name,
                'email'      => $request->email,
                'phone'      => $request->phone,
                'password'   => Hash::make($request->password ?? 'password123'),
                'role'       => 'customer',
                'status'     => $request->status,
                'created_by' => $creatorName,
            ]);

            if ($request->filled('address')) {
                Address::create([
                    'user_id'             => $customer->id,
                    'title'               => 'Primary',
                    'contact_person_name' => $request->name,
                    'contact_phone'       => $request->phone,
                    'address'             => $request->address,
                    'city'                => $request->city ?? 'Dhaka',
                    'is_default'          => 1,
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
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:users,email,' . $customer->id,
            'phone'   => 'nullable|string|digits:11',
            'status'  => 'required|boolean',
            'address' => 'nullable|string',
            'city'    => 'nullable|string|max:100',
        ], [
            'email.unique' => 'This email has already been taken.',
            'phone.digits' => 'Phone number should be 11 digits.',
        ]);

        DB::transaction(function () use ($request, $customer) {
            $customer->update([
                'name'       => $request->name,
                'email'      => $request->email,
                'phone'      => $request->phone,
                'status'     => $request->status,
                'updated_by' => Auth::user()->name ?? 'Admin',
            ]);

            if ($request->filled('address')) {
                Address::updateOrCreate(
                    ['user_id' => $customer->id, 'is_default' => 1],
                    [
                        'title'               => 'Primary',
                        'contact_person_name' => $request->name,
                        'contact_phone'       => $request->phone,
                        'address'             => $request->address,
                        'city'                => $request->city ?? 'Dhaka',
                    ]
                );
            }
        });

        return redirect()
            ->route('customer.index')
            ->with('success', 'Customer updated successfully.');
    }

    /*
     Delete customer.
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