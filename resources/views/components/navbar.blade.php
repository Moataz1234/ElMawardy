<nav>
    <ul>
        {{-- Admin-specific navigation items --}}
        @if(auth()->user()->usertype === 'admin')
            <li><a href="{{ route('admin-dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('gold-items.create') }}">Add New Item</a></li>
            <li class="dropdown">
                <a href="#" class="dropbtn">Inventory</a>
                <div class="dropdown-content">
                    {{-- <a href="{{ route('gold-items.index') }}">Gold Inventory</a>
                    <a href="{{ route('gold-items.create') }}">Diamond Inventory</a> --}}
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
        @endif

        {{-- Rabea-specific navigation items --}}
        @if(auth()->user()->usertype === 'rabea')
        <div class="w3-bar">
    
        <li><a href="{{ route('orders.rabea.index') }}" class="w3-bar-item w3-button">الاوردرات</a></li>
            <li><a href="{{ route('orders.rabea.to_print') }}" class="w3-bar-item w3-button">الورشة</a></li>   
            <li><a href="{{ route('orders.completed') }}" class="w3-bar-item w3-button">الاوردرات التي تم تسليمها</a></li>
        </div>
            @endif

        {{-- Third user-specific navigation items --}}
        @if(auth()->user()->usertype === 'user')
            <div class="w3-bar">
                <div class="dropdown">
                    <button class="w3-bar-item w3-button">Inventory</button>
                    <div class="dropdown-content">
                        <a href="{{ route('gold-items.index') }}">Gold Inventory</a>
                        <a href="{{ route('gold-items.create') }}">Diamond Inventory</a>
                        {{-- <a href="{{ route('gold-pounds.index') }}">Coins</a>
                        <a href="{{ route('gold-items.create') }}">Bars</a>
                        <a href="{{ route('gold-items.create') }}">Chains</a> --}}
                        <a href="{{ route('gold-items.index') }}">All Items</a>
                    </div>
                </div>
                <a href="{{ route('gold-items.sold') }}" class="w3-bar-item w3-button">Sold Items</a>
                <a href="{{ route('orders.create') }}" class="w3-bar-item w3-button">Custom Order</a>

                <div class="dropdown">
                    <button class="w3-bar-item w3-button">Orders</button>
                    <div class="dropdown-content">
                        <a href="{{ route('orders.index') }}">Orders List</a>
                        <a href="{{ route('orders.history') }}">Orders History</a>
                    </div>
                </div>

                <a href="{{ route('gold-catalog') }}" class="w3-bar-item w3-button">Catalog</a>
            </div>    
        @endif
    </ul>
</nav>