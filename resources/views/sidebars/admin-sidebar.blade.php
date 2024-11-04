<nav>
    <!-- Admin-specific navigation items -->
    <ul>
        {{-- <li><a href="{{ route('admin-dashboard') }}">Dashboard</a></li> --}}
        <li><a href="{{ route('gold-items.create') }}">Add New Item</a></li>
        <li class="dropdown">
            <a href="#" class="dropbtn">Invetory</a>
            <div class="dropdown-content">
                <a href="{{ route('gold-items.index') }}">Gold Inventory</a>
                <a href="{{ route('gold-items.create') }}">Diamond Inventory</a>
                <a href="{{ route('gold-pounds.index') }}">Coins</a>
                <a href="{{ route('gold-items.create') }}">Bars</a>
                <a href="{{ route('gold-items.create') }}">Chains</a>
                <a href="{{ route('gold-items.index') }}">All Items</a>
            </div>
        </li>
        <li><a href="{{ route('gold-items.sold') }}">Sold Items</a></li>
        <li class="dropdown">
            <a href="#" class="dropbtn">Shopify</a>
            <div class="dropdown-content">
                <a href="{{ route('shopify.products') }}">Products</a>
                <a href="{{ route('orders_shopify') }}">Orders</a>
            </div>
        </li>
        {{-- <li><a href="{{ route('orders.create') }}">Custom Order</a></li> --}}
       </ul>
</nav>