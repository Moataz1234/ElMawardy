<link href="{{ asset('css/navbar.css') }}" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<nav class="navbar" style="padding: 0">
    <ul class="navbar-list">
        @if (auth()->user()->usertype === 'admin')
            <li class="navbar-item"><a href="{{ route('admin.dashboard') }}" class="navbar-link">Dashboard</a></li>
            <li class="navbar-item dropdown">
                <a href="#" class="navbar-link dropdown-toggle">Models</a>
                <div class="dropdown-menu">
                    <a href="{{ route('models.index') }}" class="dropdown-item">View</a>
                    <a href="{{ route('admin.gold_items_avg.index') }}" class="dropdown-item">avg_of_stones</a>
                </div>
            </li>
            <li class="navbar-item dropdown">
                <a href="#" class="navbar-link dropdown-toggle">Gold Inventory</a>
                <div class="dropdown-menu">
                    <a href="{{ route('gold-items.create') }}" class="dropdown-item">NewItem</a>
                    <a href="{{ route('barcode.view') }}" class="dropdown-item">Barcode</a>
                    <a href="{{ route('admin.inventory') }}" class="dropdown-item">Items</a>
                    <a href="{{ route('admin.sold-items') }}" class="dropdown-item">Sold Items</a>
                    <a href="{{ route('workshop.items') }}" class="dropdown-item">Did</a>
                </div>
            </li>
            <li class="navbar-item dropdown">
                <a href="#" class="navbar-link dropdown-toggle">Requests</a>
                <div class="dropdown-menu">
                    <a href="{{ route('sell-requests.index') }}" class="dropdown-item">Sold Requests</a>
                    <a href="{{ route('sale-requests.all') }}" class="dropdown-item">All Sold Requests</a>
                </div>
            </li>
            <li class="navbar-item dropdown">
                <a href="#" class="navbar-link dropdown-toggle">Reports</a>
                <div class="dropdown-menu">
                    <a href="{{ route('reports.view') }}" class="dropdown-item">View Reports</a>
                </div>
            </li>

            <li class="navbar-item dropdown">
                <a href="#" class="navbar-link dropdown-toggle">Shopify</a>
                <div class="dropdown-menu">
                    <a href="{{ route('shopify.products') }}" class="dropdown-item">Products</a>
                    <a href="{{ route('orders_shopify') }}" class="dropdown-item">Orders</a>
                </div>
            </li>
        @endif

        @if (auth()->user()->usertype === 'rabea')
            <li class="navbar-item"><a href="{{ route('orders.rabea.index') }}" class="navbar-link">الاوردرات</a></li>
            <li class="navbar-item"><a href="{{ route('orders.rabea.to_print') }}" class="navbar-link">الورشة</a></li>
            <li class="navbar-item"><a href="{{ route('orders.completed') }}" class="navbar-link">الاوردرات التي تم
                    تسليمها</a></li>
            <li class="navbar-item dropdown">
                <a href="#" class="navbar-link dropdown-toggle">Models</a>
                <div class="dropdown-menu">
                    <a href="{{ route('models.index') }}" class="dropdown-item">View</a>
                    <a href="{{ route('admin.gold_items_avg.index') }}" class="dropdown-item">avg_of_stones</a>
                </div>
            </li>
            <li class="navbar-item dropdown">
                <a href="#" class="navbar-link dropdown-toggle">Gold Inventory</a>
                <div class="dropdown-menu">
                    <a href="{{ route('gold-items.create') }}" class="dropdown-item">NewItem</a>
                    <a href="{{ route('barcode.view') }}" class="dropdown-item">Barcode</a>
                    <a href="{{ route('admin.inventory') }}" class="dropdown-item">Items</a>
                    <a href="{{ route('admin.sold-items') }}" class="dropdown-item">Sold Items</a>
                    <a href="{{ route('workshop.items') }}" class="dropdown-item">Did</a>
                </div>
            </li>
            <li class="navbar-item dropdown">
                <a href="#" class="navbar-link dropdown-toggle">Reports</a>
                <div class="dropdown-menu">
                    <a href="{{ route('reports.view') }}" class="dropdown-item">View Reports</a>
                </div>
            </li>
        @endif

        @if (auth()->user()->usertype === 'user')
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
                <div class="dropdown-menu">
                    <a href="{{ route('orders.index') }}" class="dropdown-item">Orders List</a>
                    <a href="{{ route('orders.history') }}" class="dropdown-item">Orders History</a>
                </div>
            </li>
            <li class="navbar-item"><a href="{{ route('gold-catalog') }}" class="navbar-link">Catalog</a></li>
            <li class="navbar-item dropdown">
                <a href="#" class="navbar-link dropdown-toggle">Requests</a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('transfer.requests') }}" class="navbar-link">Transfer
                        Requests</a>
                    <a class="dropdown-item" href="{{ route('workshop.requests') }}" class="navbar-link">Workshop
                        Requests</a>
                    <a class="dropdown-item" href="{{ route('add-requests.index') }}" class="navbar-link">Add
                        Requests</a>
                    <a class="dropdown-item" href="{{ route('shop.requests.index') }}" class="navbar-link">
                        Item Requests
                        @if (Auth::user()->unreadNotifications->count() > 0)
                            <span class="badge badge-danger">
                                {{ Auth::user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </a>
        @endif
        </div>
        </li>

        <div class="dropdown">
            <button class="navbar-link dropdown-toggle">Profile</button>
            <div class="dropdown-menu">
                <div class="profile-info cursor-pointer font-medium text-gray-700 hover:text-gray-900">
                    <h4 class="name">{{ Auth::user()->name }}</h4>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();"
                        class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                        {{ __('Log Out') }}
                    </x-dropdown-link>
                </form>
            </div>
        </div>
    </ul>
</nav>
