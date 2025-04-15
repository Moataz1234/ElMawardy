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

        @if (auth()->user()->usertype === 'Acc')
            <li class="navbar-item"><a href="{{ route('dashboard') }}" class="navbar-link"> طلبات البيع</a></li>
            {{-- <li class="navbar-item"><a href="{{ route('sell-requests.acc') }}" class="navbar-link"> ط</a></li> --}}
            <li class="navbar-item"><a href="{{ route('all-sold-items') }}" class="navbar-link">sales analysis</a></li>
            <li class="navbar-item"><a href="{{ route('kasr-sales.admin.index') }}" class="navbar-link">الكسر</a></li>
            <li class="navbar-item"><a href="{{ route('gold-balance.report') }}" class="navbar-link">Gold Balance Report</a></li>
            
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

        @if (auth()->user()->usertype === 'user')
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
            <li class="navbar-item"><a href="{{ route('shop.workshop.requests') }}" class="navbar-link">التحويلات الى الورشة</a></li>
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
{{-- 
<script>
    document.getElementById('priceDropdown').addEventListener('click', function() {
        const dropdownMenu = document.getElementById('priceDropdownMenu');
        dropdownMenu.style.display = dropdownMenu.style.display === 'none' ? 'block' : 'none';

        if (dropdownMenu.childElementCount === 1) {
            fetch('{{ route('gold.prices') }}')
                .then(response => response.json())
                .then(response => {
                    const priceList = document.getElementById('priceList');
                    priceList.innerHTML = '';

                    const latestPrice = response.data;

                    // Create list items for each column in the latest gold price entry
                    for (const [key, value] of Object.entries(latestPrice)) {
                        const listItem = document.createElement('li');
                        listItem.textContent = `${key}: ${value}`;
                        priceList.appendChild(listItem);
                    }
                })
                .catch(error => console.error('Error fetching prices:', error));
        }
    });
</script> --}}
