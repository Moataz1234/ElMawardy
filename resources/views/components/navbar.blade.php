<nav class="navbar">
    <ul class="navbar-list">
        @if(auth()->user()->usertype === 'admin')
        <li class="navbar-item"><a href="{{ route('admin.dashboard') }}" class="navbar-link">Dashboard</a></li>
        <li class="navbar-item"><a href="{{ route('gold-items.create') }}" class="navbar-link">Add New Item</a></li>
        <li  class="navbar-item"><a href="{{ route('admin.inventory') }}" class="navbar-link">Gold Inventory</a></li>
        {{-- <li class="navbar-item dropdown">
            <a href="#" class="navbar-link dropdown-toggle">Inventory</a>
            <ul class="dropdown-menu">
                <li><a href="{{ route('admin.inventory') }}" class="dropdown-item">All Items</a></li>
                <li><a href="{{ route('gold-pounds.index') }}" class="dropdown-item">Coins</a></li>
                <li><a href="{{ route('gold-items.create') }}" class="dropdown-item">Bars</a></li>
                <li><a href="{{ route('gold-items.create') }}" class="dropdown-item">Chains</a></li>
            </ul>
        </li> --}}
        <li class="navbar-item"><a href="{{ route('admin.sold-items') }}" class="navbar-link">Sold Items</a></li>
        <li class="navbar-item dropdown">
            <a href="#" class="navbar-link dropdown-toggle">Shopify</a>
            <ul class="dropdown-menu">
                <li><a href="{{ route('shopify.products') }}" class="dropdown-item">Products</a></li>
                <li><a href="{{ route('orders_shopify') }}" class="dropdown-item">Orders</a></li>
            </ul>
        </li>
        <li class="navbar-item"><a href="{{ route('admin.dashboard') }}" class="navbar-link">Reports</a></li>
        @endif

        {{-- Rabea-specific navigation items --}}
        @if(auth()->user()->usertype === 'rabea')
        <li class="navbar-item"><a href="{{ route('orders.rabea.index') }}" class="navbar-link">الاوردرات</a></li>
        <li class="navbar-item"><a href="{{ route('orders.rabea.to_print') }}" class="navbar-link">الورشة</a></li>
        <li class="navbar-item"><a href="{{ route('orders.completed') }}" class="navbar-link">الاوردرات التي تم تسليمها</a></li>
            @endif

        {{-- Third user-specific navigation items --}}
        @if(auth()->user()->usertype === 'user')
        <li class="navbar-item dropdown">
            <a href="#" class="navbar-link dropdown-toggle">Inventory</a>
            <div class="dropdown-menu">
                <a href="{{ route('dashboard') }}" class="dropdown-item">Gold Inventory</a>
                <a href="{{ route('gold-items.create') }}" class="dropdown-item">Diamond Inventory</a>
                <a href="{{ route('gold-items.index') }}" class="dropdown-item">All Items</a>
            </div>
        </li>
            <li class="navbar-item"><a href="{{ route('gold-items.sold') }}" class="navbar-link">Sold Items</a></li>
            <li class="navbar-item"><a href="{{ route('orders.create') }}" class="navbar-link">Custom Order</a></li>
            <li class="navbar-item dropdown">
                <a href="#" class="navbar-link dropdown-toggle">Orders</a>
                <ul class="dropdown-menu">
                    <li><a href="{{ route('orders.index') }}" class="dropdown-item">Orders List</a></li>
                    <li><a href="{{ route('orders.history') }}" class="dropdown-item">Orders History</a></li>
                </ul>
            </li>
            <li class="navbar-item"><a href="{{ route('gold-catalog') }}" class="navbar-link">Catalog</a></li>
            <li class="navbar-item"><a href="{{ route('transfer.requests') }}" class="navbar-link">Transfer Requests</a></li>
        @endif
        <div class="dropdown">
            <button class="navbar-link dropdown-toggle">Profile</button>
            <div class="dropdown-menu">
                <div class="profile-info cursor-pointer font-medium text-gray-700 hover:text-gray-900">
                    <h4 class="name">{{ Auth::user()->name }}</h4>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-dropdown-link :href="route('logout')"
                                 onclick="event.preventDefault(); this.closest('form').submit();"
                                 class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                        {{ __('Log Out') }}
                    </x-dropdown-link>
                </form>
            </div>
        </div>
        
        
    </ul>
</nav>
