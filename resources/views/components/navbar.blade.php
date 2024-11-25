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
        <li class="navbar-item"><a href="{{ route('orders.rabea.index') }}" class="navbar-link">الاوردرات</a></li>
        <li class="navbar-item"><a href="{{ route('orders.rabea.to_print') }}" class="navbar-link">الورشة</a></li>
        <li class="navbar-item"><a href="{{ route('orders.completed') }}" class="navbar-link">الاوردرات التي تم تسليمها</a></li>
            @endif

        {{-- Third user-specific navigation items --}}
        @if(auth()->user()->usertype === 'user')
            <li class="navbar-item dropdown">
                <a href="#" class="navbar-link dropdown-toggle">Inventory</a>
                <ul class="dropdown-menu">
                    <li><a href="{{ route('dashboard') }}" class="dropdown-item">Gold Inventory</a></li>
                    <li><a href="{{ route('gold-items.create') }}" class="dropdown-item">Diamond Inventory</a></li>
                    {{-- <li><a href="{{ route('gold-pounds.index') }}" class="dropdown-item">Coins</a></li>
                    <li><a href="{{ route('gold-items.create') }}" class="dropdown-item">Bars</a></li>
                    <li><a href="{{ route('gold-items.create') }}" class="dropdown-item">Chains</a></li> --}}
                    <li><a href="{{ route('gold-items.index') }}" class="dropdown-item">All Items</a></li>
                </ul>
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
    </ul>
</nav>
