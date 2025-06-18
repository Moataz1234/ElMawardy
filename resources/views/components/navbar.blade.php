@if (auth()->user()->usertype === 'super')
    <!-- Super User Tabbed Navbar -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    
    <style>
        .super-navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .navbar-nav .nav-link {
            color: white !important;
            font-weight: 500;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin: 0 0.25rem;
            transition: all 0.3s ease;
        }
        .navbar-nav .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
            transform: translateY(-1px);
        }
        .navbar-nav .nav-link.active {
            background-color: rgba(255,255,255,0.2);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .dropdown-item {
            padding: 0.5rem 1rem;
            transition: all 0.2s ease;
        }
        .dropdown-item:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }
    </style>

    <nav class="navbar navbar-expand-lg super-navbar">
        <div class="container-fluid">
            <!-- Brand -->
            {{-- <a class="navbar-brand text-white fw-bold" href="{{ route('super.dashboard') }}">
                <i class="bx bx-shield me-2"></i>Super Admin Panel
            </a> --}}

            <!-- Toggler for mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#superNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="superNavbar">
                <!-- Main Navigation -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('super.dashboard') ? 'active' : '' }}" 
                           href="{{ route('super.dashboard') }}">
                            <i class="bx bx-home me-1"></i>Dashboard
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('super.requests*') ? 'active' : '' }}" 
                           href="{{ route('super.requests') }}">
                            <i class="bx bx-task me-1"></i>Requests
                            @php
                                $pendingRequests = \App\Models\AddRequest::where('status', 'pending')->count() + 
                                                 \App\Models\TransferRequest::where('status', 'pending')->count() + 
                                                 \App\Models\SaleRequest::where('status', 'pending')->count() + 
                                                 \App\Models\PoundRequest::where('status', 'pending')->count();
                            @endphp
                            @if($pendingRequests > 0)
                                <span class="badge bg-danger ms-1">{{ $pendingRequests }}</span>
                            @endif
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('super.orders*') ? 'active' : '' }}" 
                           href="{{ route('super.orders') }}">
                            <i class="bx bx-package me-1"></i>Orders
                            @php
                                $pendingOrders = \App\Models\Order::where('status', 'pending')->count();
                            @endphp
                            @if($pendingOrders > 0)
                                <span class="badge bg-warning text-dark ms-1">{{ $pendingOrders }}</span>
                            @endif
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('super.customers*') ? 'active' : '' }}" 
                           href="{{ route('super.customers') }}">
                            <i class="bx bx-user-plus me-1"></i>Customers
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('super.models*') ? 'active' : '' }}" 
                           href="{{ route('super.models.index') }}">
                            <i class="bx bx-grid me-1"></i>Models
                        </a>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('super.gold-items*') || request()->routeIs('super.sold-items*') || request()->routeIs('super.kasr-sales*') ? 'active' : '' }}" 
                           href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bx bx-package me-1"></i>Items
                        </a>
                        <div class="dropdown-menu">
                            <a href="{{ route('super.gold-items') }}" class="dropdown-item">
                                <i class="bx bx-diamond me-2"></i>Gold Items
                            </a>
                            <a href="{{ route('super.sold-items') }}" class="dropdown-item">
                                <i class="bx bx-check-circle me-2"></i>Sold Items
                            </a>
                            <a href="{{ route('super.kasr-sales') }}" class="dropdown-item">
                                <i class="bx bx-coin-stack me-2"></i>Kasr Sales
                            </a>
                        </div>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('super.analytics*') ? 'active' : '' }}" 
                           href="{{ route('super.analytics') }}">
                            <i class="bx bx-chart me-1"></i>Analytics
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('gold-balance.report') ? 'active' : '' }}" 
                           href="{{ route('gold-balance.report') }}">
                            <i class="bx bx-chart me-1"></i>Gold Balance
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('super.users*') ? 'active' : '' }}" 
                           href="{{ route('super.users') }}">
                            <i class="bx bx-user me-1"></i>Users
                        </a>
                    </li>
                </ul>

                <!-- Right Side Navigation -->
                <ul class="navbar-nav">
                    <!-- Last Price Dropdown -->
                    <li class="nav-item dropdown me-3">
                        <button class="nav-link dropdown-toggle" type="button" data-bs-toggle="dropdown" id="priceDropdown">
                            <i class="bx bx-dollar-circle me-1"></i>Last Prices
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" id="priceDropdownMenu">
                            <div id="priceList" class="px-3 py-2">
                                <div class="text-center">Loading...</div>
                            </div>
                            <div id="priceDate" class="px-3 py-1 border-top text-muted small"></div>
                        </div>
                    </li>

                    <!-- User Profile & Logout -->
                    <li class="nav-item dropdown">
                        <button class="nav-link dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bx bx-user-circle me-1"></i>{{ Auth::user()->name }}
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <div class="dropdown-header">
                                <strong>{{ Auth::user()->name }}</strong><br>
                                <small class="text-muted">{{ Auth::user()->email }}</small>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a href="{{ route('super.settings') }}" class="dropdown-item">
                                <i class="bx bx-cog me-2"></i>Settings
                            </a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger" onclick="event.preventDefault(); this.closest('form').submit();">
                                    <i class="bx bx-log-out me-2"></i>Logout
                                </button>
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const priceDropdown = document.getElementById('priceDropdown');
            const priceList = document.getElementById('priceList');
            const priceDate = document.getElementById('priceDate');
            
            priceDropdown.addEventListener('click', function() {
                if (priceList.innerHTML.includes('Loading...')) {
                    fetch('{{ route('gold.prices') }}')
                        .then(response => response.json())
                        .then(response => {
                            priceList.innerHTML = '';
                            priceDate.textContent = '';

                            const latestPrice = response.data;
                            let priceHtml = '';

                            for (const [key, value] of Object.entries(latestPrice)) {
                                if (key !== 'created_at') {
                                    priceHtml += `<div class="d-flex justify-content-between mb-1">
                                        <span class="text-capitalize">${key.replace('_', ' ')}:</span>
                                        <strong class="text-primary">${value}</strong>
                                    </div>`;
                                }
                            }
                            
                            priceList.innerHTML = priceHtml;

                            if (latestPrice.created_at) {
                                priceDate.textContent = `Updated: ${latestPrice.created_at}`;
                            }
                        })
                        .catch(error => {
                            priceList.innerHTML = '<div class="text-danger">Error loading prices</div>';
                            console.error('Error fetching prices:', error);
                        });
                }
            });
        });
    </script>
@else
    <!-- Original Navbar for Other User Types -->
    <link href="{{ asset('css/navbar.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <nav class="navbar" style="padding: 0">
        <ul class="navbar-list">
            @if (auth()->user()->usertype === 'admin' )
                <li class="navbar-item"><a href="{{ route('admin.dashboard') }}" class="navbar-link">Dashboard</a></li>
                <li class="navbar-item dropdown">
                    <a href="#" class="navbar-link dropdown-toggle">Models</a>
                    <div class="dropdown-menu">
                        <a href="{{ route('models.index') }}" class="dropdown-item">View</a>
                        <a href="{{ route('admin.gold_items_avg.index') }}" class="dropdown-item">avg_of_stones</a>
                    </div>
                </li>
                <li class="navbar-item dropdown">
                    <a href="#" class="navbar-link dropdown-toggle">Add Items</a>
                    <div class="dropdown-menu">
                        {{-- <a href="{{ route('gold-pounds.create') }}" class="dropdown-item">New Pound</a> --}}
                        <a href="{{ route('gold-items.create') }}" class="dropdown-item">NewItem</a>
                        <a href="{{ route('barcode.view') }}" class="dropdown-item">Barcode</a>
                    </div>
                    </li>
                <li class="navbar-item dropdown">
                    <a href="#" class="navbar-link dropdown-toggle">Gold Inventory</a>
                    <div class="dropdown-menu">
                        {{-- <a href="{{ route('gold-items.create') }}" class="dropdown-item">NewItem</a>
                        <a href="{{ route('gold-pounds.create') }}" class="dropdown-item">New Pound</a>
                        <a href="{{ route('barcode.view') }}" class="dropdown-item">Barcode</a> --}}
                        <a href="{{ route('gold-pounds.admin.index') }}" class="dropdown-item">Gold Pounds</a>
                        <a href="{{ route('admin.inventory') }}" class="dropdown-item">Gold Items</a>
                        <a href="{{ route('rabea.items.list') }}" class="dropdown-item">Items List</a>
                        <a href="{{ route('admin.sold-items') }}" class="dropdown-item">Sold Items</a>
                        <a href="{{ route('workshop.items.index') }}" class="dropdown-item">Did</a>
                    </div>
                </li>
                <li class="navbar-item dropdown">
                    <a href="#" class="navbar-link dropdown-toggle">Requests</a>
                    <div class="dropdown-menu">
                        <a href="{{ route('admin.add.requests') }}" class="dropdown-item">Add Requests</a>
                        <a class="dropdown-item" href="{{ route('transfer.requests.admin') }}" class="navbar-link">Transfer Requests</a>
                        <a href="{{ route('sell-requests.index') }}" class="dropdown-item">Sold Requests</a>
                        <a href="{{ route('sale-requests.all') }}" class="dropdown-item">All Sold Requests</a>
                    </div>
                </li>
                <li class="navbar-item dropdown">
                    <a href="#" class="navbar-link dropdown-toggle">Reports</a>
                    <div class="dropdown-menu">
                        <a href="{{ route('reports.view') }}" class="dropdown-item">View Reports</a>
                        <a href="{{ route('gold-analysis.index') }}" class="dropdown-item">الجرد</a>
                        <a href="{{ route('tracking.index') }}" class="dropdown-item">Serial Number Tracking</a>
                        <a href="{{ route('gold-item-weight-history.index') }}" class="dropdown-item">Weight Change History</a>
                        {{-- <a href="{{ route('production.import') }}" class="dropdown-item">Production Import</a> --}}
                        <a href="{{ route('production.index') }}" class="dropdown-item">Production</a>
                    </div>
                </li>

                <li class="navbar-item dropdown">
                    <a href="#" class="navbar-link dropdown-toggle">Shopify</a>
                    <div class="dropdown-menu">
                        <a href="{{ route('shopify.products') }}" class="dropdown-item">Products</a>
                        {{-- <a href="{{ route('shopify.orders') }}" class="dropdown-item">Orders</a> --}}
                        <a href="{{ route('shopify.orders.api-view') }}" class="dropdown-item">Orders </a>
                    </div>
                </li>
            @endif

            @if (auth()->user()->usertype === 'Acc' || auth()->user()->usertype === 'super')
                <li class="navbar-item"><a href="{{ route('dashboard') }}" class="navbar-link"> طلبات البيع</a></li>
                {{-- <li class="navbar-item"><a href="{{ route('sell-requests.acc') }}" class="navbar-link"> ط</a></li> --}}
                <li class="navbar-item"><a href="{{ route('all-sold-items') }}" class="navbar-link">sales analysis</a></li>
                <li class="navbar-item"><a href="{{ route('kasr-sales.admin.index') }}" class="navbar-link">الكسر</a></li>
                <li class="navbar-item"><a href="{{ route('gold-balance.report') }}" class="navbar-link">Gold Balance Report</a></li>
                {{-- <li class="navbar-item"><a href="{{ route('tracking.index') }}" class="navbar-link">Serial Tracking</a></li> --}}
                
                {{-- <li class="navbar-item dropdown">
                    <a href="#" class="navbar-link dropdown-toggle">المبيعات</a>
                    <div class="dropdown-menu">
                        <a href="{{ route('sell-requests.acc') }}" class="dropdown-item">طلبات البيع</a>
                    </div>
                </li> --}}
                {{-- <li class="navbar-item dropdown">
                    <a href="#" class="navbar-link dropdown-toggle">التقارير</a>
                    <div class="dropdown-menu">
                        <a href="{{ route('reports.view') }}" class="dropdown-item">عرض التقارير</a>
                        <a href="{{ route('gold-analysis.index') }}" class="dropdown-item">الجرد</a>
                    </div>
                </li> --}}
            @endif

            @if (auth()->user()->usertype === 'rabea')
                <li class="navbar-item"><a href="{{ route('orders.rabea.index') }}" class="navbar-link">الاوردرات</a></li>
                <li class="navbar-item"><a href="{{ route('orders.rabea.to_print') }}" class="navbar-link">الورشة</a></li>
                <li class="navbar-item"><a href="{{ route('orders.completed') }}" class="navbar-link">الاوردرات التي تم
                        تسليمها</a></li>
                <li class="navbar-item"><a href="{{ route('laboratory.operations.index') }}" class="navbar-link">المعمل</a></li>
                <li class="navbar-item"><a href="{{ route('rabea.did.requests') }}" class="navbar-link">طلبات الورشة</a></li>
                <li class="navbar-item dropdown">
                    <a href="#" class="navbar-link dropdown-toggle">Models</a>
                    <div class="dropdown-menu">
                        <a href="{{ route('models.index') }}" class="dropdown-item">View</a>
                        <a href="{{ route('admin.gold_items_avg.index') }}" class="dropdown-item">avg_of_stones</a>
                    </div>
                </li>
                <li class="navbar-item dropdown">
                <a href="#" class="navbar-link dropdown-toggle">Add Items</a>
                <div class="dropdown-menu">
                    <a href="{{ route('gold-pounds.create') }}" class="dropdown-item">New Pound</a>
                    <a href="{{ route('gold-items.create') }}" class="dropdown-item">NewItem</a>
                    <a href="{{ route('barcode.view') }}" class="dropdown-item">Barcode</a>
                </div>
                </li>
                <li class="navbar-item dropdown">

                   
                    <a href="#" class="navbar-link dropdown-toggle">Gold Inventory</a>
                    <div class="dropdown-menu">
                        {{-- <a href="{{ route('gold-pounds.create') }}" class="dropdown-item">New Pound</a> --}}
                        {{-- <a href="{{ route('gold-items.create') }}" class="dropdown-item">NewItem</a>
                        <a href="{{ route('barcode.view') }}" class="dropdown-item">Barcode</a> --}}
                        <a href="{{ route('gold-pounds.admin.index') }}" class="dropdown-item">Gold Pounds</a>
                        <a href="{{ route('admin.inventory') }}" class="dropdown-item">Gold Items</a>
                        <a href="{{ route('rabea.items.list') }}" class="dropdown-item">Items List</a>
                        <a href="{{ route('admin.sold-items') }}" class="dropdown-item">Sold Items</a>
                        {{-- <a href="{{ route('workshop.items.index') }}" class="dropdown-item">Did</a> --}}
                    </div>
                </li>
                <li class="navbar-item dropdown">
                    <a href="#" class="navbar-link dropdown-toggle">Requests</a>
                    <div class="dropdown-menu">
                        <a href="{{ route('admin.add.requests') }}" class="dropdown-item">Add Requests</a>
                        <a class="dropdown-item" href="{{ route('transfer.requests') }}" class="navbar-link">Transfer Requests</a>
                        <a href="{{ route('sell-requests.index') }}" class="dropdown-item">Sold Requests</a>
                        <a href="{{ route('sale-requests.all') }}" class="dropdown-item">All Sold Requests</a>
                        {{-- <a href="{{ route('tracking.index') }}" class="dropdown-item">Serial Number Tracking</a> --}}
                    </div>
                </li>
                {{-- <li class="navbar-item dropdown">
                    <a href="#" class="navbar-link dropdown-toggle">Reports</a>
                    <div class="dropdown-menu">
                        <a href="{{ route('reports.view') }}" class="dropdown-item">View Reports</a>
                    </div>
                </li> --}}
                <li class="navbar-item"><a href="{{ route('kasr-sales.admin.index') }}" class="navbar-link">الكسر</a></li>

            @endif

            @if (auth()->user()->usertype === 'user' )
                <li class="navbar-item dropdown">
                    <a href="#" class="navbar-link dropdown-toggle">المخزون</a>
                    <div class="dropdown-menu">
                        <a href="{{ route('dashboard') }}" class="dropdown-item">القطع</a>
                        <a href="{{ route('gold-pounds.index') }}" class="dropdown-item">الجنيهات و التول</a>
                        {{-- <a href="{{ route('gold-items.create') }}" class="dropdown-item">Diamond Inventory</a> --}}
                        <a href="{{ route('gold-items.index') }}" class="dropdown-item">كل القطع</a>
                        {{-- <a href="{{ route('items.statistics') }}" class="dropdown-item">الجرد</a> --}}
                    </div>
                </li>
                <li class="navbar-item"><a href="{{ route('gold-items.sold') }}" class="navbar-link">المبيعات</a></li>
                <li class="navbar-item dropdown">
                    <a href="#" class="navbar-link dropdown-toggle">الكسر</a>
                    <div class="dropdown-menu">
                        <a href="{{ route('kasr-sales.create') }}" class="dropdown-item">شراء الكسر</a>
                        <a href="{{ route('kasr-sales.index') }}" class="dropdown-item">الكسر</a>

                    </div>
                </li>
                <li class="navbar-item dropdown">
                    <a href="#" class="navbar-link dropdown-toggle">الطلبات</a>
                    <div class="dropdown-menu">
                        <a href="{{ route('orders.create') }}" class="dropdown-item">عمل طلب </a>
                        <a href="{{ route('orders.index') }}" class="dropdown-item"> الطلبات</a>
                        {{-- <a href="{{ route('orders.history') }}" class="dropdown-item">الطلبات السابقة</a> --}}
                    </div>
                </li>
                <li class="navbar-item dropdown">
                    <a href="#" class="navbar-link dropdown-toggle">الكتالوجات</a>
                    <div class="dropdown-menu">
                        <a href="{{ route('gold-catalog') }}" class="navbar-link dropdown-item">كتالوج الذهب</a>
                        <a href="http://172.29.206.251:8000/diamond/ThreeView" class="navbar-link dropdown-item">كتالوج الالماظ</a>
                    </div>
                </li>
                <li class="navbar-item"><a href="{{ route('transfer.requests') }}" class="navbar-link">التحويلات</a></li>
                <li class="navbar-item"><a href="{{ route('add-requests.index') }}" class="navbar-link">الاضافات</a></li>
                <li class="navbar-item"><a href="{{ route('shop.workshop.requests') }}" class="navbar-link">طلبات الكسر</a></li>
                {{-- <li class="navbar-item"><a href="{{ route('tracking.index') }}" class="navbar-link">Serial Tracking</a></li> --}}

                {{-- <li class="navbar-item"><a href="{{ route('shop.requests.index') }}" class="navbar-link">التحويلات الى الورشة</a></li> --}}
                
                <li class="navbar-item dropdown">
                   
                    {{-- <a href="#" class="navbar-link dropdown-toggle">التحويلات و الاضافات</a> --}}
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ route('transfer.requests') }}" class="navbar-link"> التحويلات</a>
                        {{-- <a class="dropdown-item" href="{{ route('workshop.requests') }}" class="navbar-link">Workshop Requests</a> --}}
                        <a class="dropdown-item" href="{{ route('add-requests.index') }}" class="navbar-link">الاضافات</a>
                        {{-- <a class="dropdown-item" href="{{ route('pound-requests.index') }}" class="navbar-link">Pound Requests</a> --}}
                        {{-- <a class="dropdown-item" href="{{ route('shop.requests.index') }}" class="navbar-link"> Item Requests
                            @if (Auth::user()->unreadNotifications->count() > 0)
                                <span class="badge badge-danger">
                                    {{ Auth::user()->unreadNotifications->count() }}
                                </span>
                            @endif
                        </a> --}}
            @endif
            </div>
            </li>
            <li class="navbar-item dropdown">
                <button id="priceDropdown" class="navbar-link dropdown-toggle">اخر الاسعار</button>
                <div id="priceDropdownMenu" class="dropdown-menu-horizontal" style="display: none;">
                    <ul id="priceList"></ul>
                    <div id="priceDate"></div>
                </div>
            </li>


            <div class="dropdown">
                <button class="navbar-link dropdown-toggle">الحساب</button>
                <div class="dropdown-menu">
                    <div class="profile-info cursor-pointer font-medium text-black">
                        <h4 class="name">{{ Auth::user()->name }}</h4>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();"
                            class=" logout block px-4 py-2 hover:bg-primary text-yellow">
                            {{ __('تسجيل الخروج') }}
                        </x-dropdown-link>
                    </form>
                </div>
            </div>
        </ul>
    </nav>
    <script>
        document.getElementById('priceDropdown').addEventListener('click', function() {
            const dropdownMenu = document.getElementById('priceDropdownMenu');
            dropdownMenu.style.display = dropdownMenu.style.display === 'none' ? 'block' : 'none';

            if (dropdownMenu.childElementCount === 2) {
                fetch('{{ route('gold.prices') }}')
                    .then(response => response.json())
                    .then(response => {
                        const priceList = document.getElementById('priceList');
                        const priceDate = document.getElementById('priceDate');
                        priceList.innerHTML = '';
                        priceDate.textContent = '';

                        const latestPrice = response.data;

                        // Create list items without column names
                        for (const [key, value] of Object.entries(latestPrice)) {
                            if (key !== 'created_at') {
                                const listItem = document.createElement('li');
                                listItem.textContent = `${value}`;
                                priceList.appendChild(listItem);
                            }
                        }

                        // Add created_at date with larger styling
                        if (latestPrice.created_at) {
                            priceDate.textContent = `Last Updated: ${latestPrice.created_at}`;
                        }
                    })
                    .catch(error => console.error('Error fetching prices:', error));
            }
        });
    </script>
@endif
