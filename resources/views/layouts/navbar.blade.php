<nav class="navbar">

    <div class="nav-container">

        <a href="{{ route('admin.dashboard') }}" class="logo">
            SD Inventory System
        </a>

        <div class="nav-links">
            
            <a href="{{ route('admin.dashboard') }}">
                Dashboard
            </a>

            <a href="{{ route('order.index') }}">
                Orders
            </a>

            <a href="{{ route('customer.index') }}">
                Customers
            </a>

            <a href="{{ route('product.index') }}">
                Products
            </a>

            <a href="{{ route('category.index') }}">
                Categories
            </a>
            <a href="{{ route('coupons.index') }}" > Manage Coupons </a>

            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                @csrf
                <button type="submit" class="logout-btn">
                    Logout
                </button>
            </form>

        </div>

    </div>

</nav>