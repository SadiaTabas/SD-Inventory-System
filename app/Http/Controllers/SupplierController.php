<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::withCount('purchases')->latest()->paginate(15);
        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email'          => 'nullable|email|max:255',
            'phone'          => 'nullable|string|max:50',
            'company_name'   => 'nullable|string|max:255',
            'address'        => 'nullable|string|max:500',
            'notes'          => 'nullable|string',
            'status'         => 'required|boolean',
        ]);

        $supplier = Supplier::create([
            'name'           => $request->name,
            'contact_person' => $request->contact_person,
            'email'          => $request->email,
            'phone'          => $request->phone,
            'company_name'   => $request->company_name,
            'address'        => $request->address,
            'notes'          => $request->notes,
            'status'         => $request->status,
            'created_by'     => Auth::user()->name ?? 'System',
        ]);

        ActivityLog::log('Supplier Created', 'Supplier', $supplier->id, "Supplier {$supplier->name} created.");

        return redirect()->route('suppliers.index')->with('success', 'Supplier created successfully.');
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email'          => 'nullable|email|max:255',
            'phone'          => 'nullable|string|max:50',
            'company_name'   => 'nullable|string|max:255',
            'address'        => 'nullable|string|max:500',
            'notes'          => 'nullable|string',
            'status'         => 'required|boolean',
        ]);

        $supplier->update($request->all());

        ActivityLog::log('Supplier Updated', 'Supplier', $supplier->id, "Supplier {$supplier->name} updated.");

        return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->purchases()->count() > 0) {
            return back()->with('error', 'Cannot delete supplier with existing purchase records. You can deactivate it instead.');
        }

        ActivityLog::log('Supplier Deleted', 'Supplier', $supplier->id, "Supplier {$supplier->name} deleted.");

        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully.');
    }
}
