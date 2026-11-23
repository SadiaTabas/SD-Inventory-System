<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\DeliveryAgent;
use App\Models\OrderPayment;
use App\Models\StockMovement;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Notifications\AdminInvitationNotification;

class AdminController extends Controller
{
    // Enhanced Admin Dashboard
    public function dashboard(Request $request)
    {
        $today = now()->toDateString();
        $startOfWeek = now()->startOfWeek();
        $startOfMonth = now()->startOfMonth();

        // 1. Core Counts
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $totalCustomers = User::where('role', 'customer')->count();
        $totalAdmins = User::where('role', '!=', 'customer')->count();
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $processingOrders = Order::where('status', 'processing')->count();
        $shippedOrders = Order::where('status', 'shipped')->count();
        $deliveredOrders = Order::where('status', 'delivered')->count();
        $cancelledOrders = Order::where('status', 'cancelled')->count();
        $totalAgents = DeliveryAgent::count();

        // 2. Revenue & Sales Overview (Excluding cancelled orders)
        $validOrders = Order::where('status', '!=', 'cancelled');

        $totalRevenue = (float) Order::where('status', '!=', 'cancelled')->sum('total_amount');
        $todayRevenue = (float) Order::where('status', '!=', 'cancelled')->whereDate('created_at', $today)->sum('total_amount');
        $weekRevenue = (float) Order::where('status', '!=', 'cancelled')->where('created_at', '>=', $startOfWeek)->sum('total_amount');
        $monthRevenue = (float) Order::where('status', '!=', 'cancelled')->where('created_at', '>=', $startOfMonth)->sum('total_amount');

        $totalPaidAmount = (float) OrderPayment::where('verification_status', 'verified')->sum('amount');
        if ($totalPaidAmount <= 0) {
            $totalPaidAmount = (float) Order::where('status', '!=', 'cancelled')->sum('paid_amount');
        }
        $totalDueAmount = max(0, $totalRevenue - $totalPaidAmount);

        // 3. Inventory Overview
        $totalStockQty = (int) Product::sum('qty');
        $lowStockProducts = Product::where('qty', '<=', 5)
            ->where('qty', '>', 0)
            ->orderBy('qty', 'asc')
            ->get();
        $outOfStockCount = Product::where('qty', '<=', 0)->count();

        // Inventory valuation
        $inventoryValuation = (float) Product::select(DB::raw('SUM(qty * IF(cost_price > 0, cost_price, price)) as total_val'))->value('total_val');

        // Recent stock movements
        $recentStockMovements = StockMovement::with('product')->latest()->take(6)->get();

        // 4. Customer Overview
        $newCustomersCount = User::where('role', 'customer')->where('created_at', '>=', now()->subDays(30))->count();
        $recentCustomers = User::where('role', 'customer')->latest()->take(5)->get();

        // 5. Recent Products & Orders
        $recentProducts = Product::latest()->take(5)->get();
        $recentOrders = Order::with(['user', 'customer'])->latest()->take(8)->get();

        // 6. Chart Data: Monthly Revenue for the current year
        $monthlySales = Order::where('status', '!=', 'cancelled')
            ->whereYear('created_at', now()->year)
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $chartLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $chartData = [];
        for ($m = 1; $m <= 12; $m++) {
            $chartData[] = isset($monthlySales[$m]) ? (float) $monthlySales[$m] : 0.00;
        }

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalCategories',
            'totalCustomers',
            'totalAdmins',
            'totalOrders',
            'pendingOrders',
            'processingOrders',
            'shippedOrders',
            'deliveredOrders',
            'cancelledOrders',
            'totalAgents',
            'totalRevenue',
            'todayRevenue',
            'weekRevenue',
            'monthRevenue',
            'totalPaidAmount',
            'totalDueAmount',
            'totalStockQty',
            'lowStockProducts',
            'outOfStockCount',
            'inventoryValuation',
            'recentStockMovements',
            'newCustomersCount',
            'recentCustomers',
            'recentProducts',
            'recentOrders',
            'chartLabels',
            'chartData'
        ));
    }

    // Show all admins
    public function index()
    {
        $admins = User::where('role', '!=', 'customer')
            ->latest()
            ->get();

        return view('admin.index', compact('admins'));
    }

    // Show create admin form
    public function create()
    {
        return view('admin.create');
    }

    // Store new admin invitation or direct user
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'required|digits:11',
            'role'     => 'required|in:admin,super_admin,inventory_manager,order_manager,customer_support',
            'password' => [
                'required',
                'confirmed',
                'min:8',
            ],
        ], [
            'email.unique' => 'This email address is already in use.',
            'phone.digits' => 'Phone number must be exactly 11 digits.',
        ]);

        $user = User::create([
            'name'       => $request->name,
            'email'      => strtolower(trim($request->email)),
            'phone'      => $request->phone,
            'role'       => $request->role,
            'password'   => Hash::make($request->password),
            'status'     => 1,
            'created_by' => Auth::user()->name ?? 'System',
        ]);

        ActivityLog::log('Admin Created', 'User', $user->id, "New staff user {$user->email} with role {$user->role} created by " . Auth::user()->name);

        return redirect()->route('admin.index')->with('success', 'Staff account created successfully.');
    }

    public function edit(User $admin)
    {
        return view('admin.edit', compact('admin'));
    }

    public function update(Request $request, User $admin)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $admin->id,
            'phone'    => 'required|digits:11',
            'role'     => 'required|in:admin,super_admin,inventory_manager,order_manager,customer_support',
            'status'   => 'required|boolean',
            'password' => 'nullable|confirmed|min:8',
        ]);

        $data = [
            'name'       => $request->name,
            'email'      => strtolower(trim($request->email)),
            'phone'      => $request->phone,
            'role'       => $request->role,
            'status'     => $request->status,
            'updated_by' => Auth::user()->name,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $admin->update($data);

        ActivityLog::log('Admin Updated', 'User', $admin->id, "Staff account {$admin->email} updated.");

        return redirect()->route('admin.index')->with('success', 'Staff account updated successfully.');
    }

    public function destroy(User $admin)
    {
        if ($admin->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        ActivityLog::log('Admin Deleted', 'User', $admin->id, "Staff account {$admin->email} deleted.");

        $admin->delete();

        return redirect()->route('admin.index')->with('success', 'Staff account deleted successfully.');
    }
}
