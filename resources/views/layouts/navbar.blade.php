<nav class="navbar">

 
<div class="nav-container">

    <a href="{{ route('product.index') }}" class="logo">
        SD Inventory System
    </a>

    <div class="nav-links">
        
        <a href="{{ route('customer.index') }}">
    Customers
</a>

        <a href="{{ route('product.index') }}">
            Products
        </a>

        <a href="{{ route('category.index') }}">
            Categories
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="logout-btn">
                Logout
            </button>
        </form>

    </div>

</div>
 

</nav>
