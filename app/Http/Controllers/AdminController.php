<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\DeliveryAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AdminInvitationNotification;

class AdminController extends Controller
{
    // Admin Dashboard
    public function dashboard()
    {
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $totalCustomers = User::where('role', 'customer')->count();
        $totalAdmins = User::where('role', 'admin')->count();
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $totalAgents = DeliveryAgent::count();

        $lowStockProducts = Product::where('qty', '<=', 5)
            ->orderBy('qty', 'asc')
            ->get();

        $recentProducts = Product::latest()
            ->take(5)
            ->get();

        $recentOrders = Order::with('customer')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalCategories',
            'totalCustomers',
            'totalAdmins',
            'totalOrders',
            'pendingOrders',
            'totalAgents',
            'lowStockProducts',
            'recentProducts',
            'recentOrders'
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

    // Store new admin invitation
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email:rfc,dns|unique:users,email|unique:admin_invitations,email',
            'phone' => 'required|digits:11',
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*?&#]/',
            ],
        ], [
            'email.email'  => 'Please enter a genuine email address with an active domain.',
            'email.unique' => 'This email address is already in use or has a pending invitation.',
            'phone.digits' => 'Phone number must be exactly 11 digits.',
        ]);

        $token = Str::random(60);

        // Store into pending invitations table without hashing yet to avoid double hashing
        DB::table('admin_invitations')->updateOrInsert(
            ['email' => $request->email],
            [
                'name'       => $request->name,
                'phone'      => $request->phone,
                'password'   => $request->password,
                'token'      => $token,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $invitation = (object) [
            'name'  => $request->name,
            'email' => $request->email,
            'token' => $token,
        ];

        // Send email with verification link
        Notification::route('mail', $request->email)->notify(new AdminInvitationNotification($invitation));

        return redirect()
            ->route('admin.index')
            ->with('success', 'Verification email sent. The admin will only be added to the system after verifying their email.');
    }

    // Verify invitation token and officially create admin in the system
    public function verifyInvitation($token)
    {
        $invitation = DB::table('admin_invitations')->where('token', $token)->first();

        if (!$invitation) {
            return redirect()->route('login')->with('error', 'Invalid or expired invitation link.');
        }

        // Create the user record
        $admin = new User();
        $admin->name              = $invitation->name;
        $admin->email             = $invitation->email;
        $admin->phone             = $invitation->phone;
        $admin->password          = Hash::make($invitation->password);
        $admin->role              = 'admin';
        $admin->status            = 1;
        $admin->email_verified_at = now();
        $admin->created_by        = 'Verified Invitation';
        $admin->save();

        // Remove token from pending invitations
        DB::table('admin_invitations')->where('token', $token)->delete();

        // Log the newly verified admin in immediately
        Auth::login($admin);

        return redirect()->route('admin.dashboard')->with('success', 'Email verified successfully! Welcome to your Admin Dashboard.');
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
            'email' => 'required|email:rfc,dns|unique:users,email,' . $admin->id,
            'phone' => 'required|digits:11',
            'status' => 'required|in:0,1',
            'password' => [
                'nullable',
                'confirmed',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*?&#]/',
            ],
        ]);

        $data = [
            'name'       => $request->name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'status'     => $request->status,
            'updated_by' => Auth::user()->name ?? 'System',
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $admin->update($data);

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

        if ($admin->id === Auth::id()) {
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