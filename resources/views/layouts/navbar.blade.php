<nav class="navbar" style="background-color: #1e293b; padding: 12px 24px; box-shadow: 0 2px 10px rgba(0,0,0,0.15);">
    <div style="max-width: 1440px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
        <!-- Logo & Quick Link -->
        <div style="display: flex; align-items: center; gap: 16px;">
            <a href="{{ route('admin.dashboard') }}" style="color: white; font-size: 19px; font-weight: 800; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                <span style="background: #3b82f6; padding: 3px 8px; border-radius: 6px; font-size: 13px;">SD</span>
                <span>SD Inventory &amp; Admin</span>
            </a>
            <a href="{{ route('home') }}" target="_blank" style="background: rgba(255,255,255,0.1); color: #93c5fd; font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 6px; text-decoration: none;">
                Live Store ↗
            </a>
        </div>

        <!-- Admin Navigation Links -->
        <div style="display: flex; align-items: center; gap: 14px; flex-wrap: wrap; font-size: 13px; font-weight: 600;">
            <a href="{{ route('admin.dashboard') }}" style="color: {{ request()->routeIs('admin.dashboard') ? '#60a5fa' : '#cbd5e1' }}; text-decoration: none;">Dashboard</a>
            <a href="{{ route('order.index') }}" style="color: {{ request()->routeIs('order.*') ? '#60a5fa' : '#cbd5e1' }}; text-decoration: none;">Orders</a>
            <a href="{{ route('product.index') }}" style="color: {{ request()->routeIs('product.*') ? '#60a5fa' : '#cbd5e1' }}; text-decoration: none;">Products</a>
            <a href="{{ route('inventory.index') }}" style="color: {{ request()->routeIs('inventory.*') ? '#60a5fa' : '#cbd5e1' }}; text-decoration: none;">Inventory</a>
            <a href="{{ route('category.index') }}" style="color: {{ request()->routeIs('category.*') ? '#60a5fa' : '#cbd5e1' }}; text-decoration: none;">Categories</a>
            <a href="{{ route('customer.index') }}" style="color: {{ request()->routeIs('customer.*') ? '#60a5fa' : '#cbd5e1' }}; text-decoration: none;">Customers</a>
            <a href="{{ route('suppliers.index') }}" style="color: {{ request()->routeIs('suppliers.*') ? '#60a5fa' : '#cbd5e1' }}; text-decoration: none;">Suppliers</a>
            <a href="{{ route('purchases.index') }}" style="color: {{ request()->routeIs('purchases.*') ? '#60a5fa' : '#cbd5e1' }}; text-decoration: none;">Purchases</a>
            <a href="{{ route('coupons.index') }}" style="color: {{ request()->routeIs('coupons.*') ? '#60a5fa' : '#cbd5e1' }}; text-decoration: none;">Coupons</a>
            <a href="{{ route('payments.index') }}" style="color: {{ request()->routeIs('payments.*') ? '#60a5fa' : '#cbd5e1' }}; text-decoration: none;">Payments</a>
            <a href="{{ route('deliveries.index') }}" style="color: {{ request()->routeIs('deliveries.*') ? '#60a5fa' : '#cbd5e1' }}; text-decoration: none;">Deliveries</a>
            <a href="{{ route('reports.index') }}" style="color: {{ request()->routeIs('reports.*') ? '#60a5fa' : '#cbd5e1' }}; text-decoration: none;">Reports</a>
            <a href="{{ route('reviews.index') }}" style="color: {{ request()->routeIs('reviews.*') ? '#60a5fa' : '#cbd5e1' }}; text-decoration: none;">Reviews</a>
            <a href="{{ route('activity_logs.index') }}" style="color: {{ request()->routeIs('activity_logs.*') ? '#60a5fa' : '#cbd5e1' }}; text-decoration: none;">Logs</a>
            <a href="{{ route('admin.index') }}" style="color: {{ request()->routeIs('admin.*') && !request()->routeIs('admin.dashboard') ? '#60a5fa' : '#cbd5e1' }}; text-decoration: none;">Staff</a>
            <a href="{{ route('settings.index') }}" style="color: {{ request()->routeIs('settings.*') ? '#60a5fa' : '#cbd5e1' }}; text-decoration: none;">Settings</a>

            <!-- User badge & logout -->
            <div style="display: flex; align-items: center; gap: 10px; margin-left: 10px; padding-left: 12px; border-left: 1px solid rgba(255,255,255,0.15);">
                <span style="color: #94a3b8; font-size: 12px;">{{ Auth::user()->name ?? 'Admin' }} ({{ Auth::user()->role ?? 'admin' }})</span>
                <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                    @csrf
                    <button type="submit" style="padding: 5px 10px; border: none; border-radius: 4px; background-color: #ef4444; color: white; font-weight: 700; font-size: 12px; cursor: pointer;">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
