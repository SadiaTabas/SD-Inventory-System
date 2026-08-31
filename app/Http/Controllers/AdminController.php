<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Admin Dashboard
    public function dashboard()
{
    $totalProducts = Product::count();

    $totalCategories = Category::count();

    $totalCustomers = User::where('role', 'customer')->count();

    $totalAdmins = User::where('role', 'admin')->count();

    $lowStockProducts = Product::where('qty', '<=', 5)
        ->orderBy('qty', 'asc')
        ->get();

    $recentProducts = Product::latest()
        ->take(5)
        ->get();

    return view('admin.dashboard', compact(
        'totalProducts',
        'totalCategories',
        'totalCustomers',
        'totalAdmins',
        'lowStockProducts',
        'recentProducts'
    ));
}
    // Show all admins
    public function index()
    {
        $admins = User::where('role', 'admin')
            ->latest()
            ->get();

        return view('admin.index', compact('admins'));
    }

    // Show create admin form
    public function create()
    {
        return view('admin.create');
    }

    // Store new admin
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*?&#]/',
            ],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'admin',
        ]);

        return redirect()
            ->route('admin.index')
            ->with('success', 'New admin created successfully.');
    }

    // Show edit admin form
    public function edit(User $admin)
    {
        if ($admin->role !== 'admin') {
            abort(404);
        }

        return view('admin.edit', compact('admin'));
    }

    // Update admin
    public function update(Request $request, User $admin)
    {
        if ($admin->role !== 'admin') {
            abort(404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $admin->id,
        ]);

        $admin->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return redirect()
            ->route('admin.index')
            ->with('success', 'Admin updated successfully.');
    }

    // Delete admin
    public function destroy(User $admin)
    {
        if ($admin->role !== 'admin') {
            abort(404);
        }

        // Prevent admin from deleting themselves
        if ($admin->id === auth()->id) {
            return redirect()
                ->route('admin.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $admin->delete();

        return redirect()
            ->route('admin.index')
            ->with('success', 'Admin deleted successfully.');
    }
}