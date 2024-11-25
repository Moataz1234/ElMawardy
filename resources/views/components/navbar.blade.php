<head>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOM8kNq7/8z2zVw5U5NAuTp6WVsMSXJ1pO9aX1l" crossorigin="anonymous">
    <link href="{{ asset('css/sidebar.css') }}" rel="stylesheet">
    <link href="{{ asset('css/navbar.css') }}" rel="stylesheet">
    {{-- <link href="{{ asset('css/pagination.css') }}" rel="stylesheet"> --}}

</head>
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
                        <a href="{{ route('dashboard') }}">Gold Inventory</a>
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
                <a href="{{ route('transfer.requests') }}" class="w3-bar-item w3-button">Transfer Requests</a>

            </div>    
        @endif
    </ul>
</nav>
