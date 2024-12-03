<head>
    <style>


        .nav-link {
            text-decoration: none;
            padding: 10px 15px;
            /* border: 1px solid #007bff; */
            border-radius: 4px;
            /* background-color: #f8f9fa; */
            color: #2d7cf3;
            transition: background-color 0.3s;
        }

        .nav-link:hover {
            background-color: #e2e6ea;
        }

        .badge {
            background-color: red;
            color: white;
            border-radius: 10px;
            padding: 2px 8px;
        }
</style>
</head>
@php
    $pendingOrdersCount = \App\Models\Order::where('status', 'pending')->count();
@endphp
<nav class="second-section">
    {{-- <h1>Customer Orders</h1> --}}

    <div class="search-container">
        <select class="model-dropdown" id="search-type">
        <option value="order_number">رقم الأوردر</option>
        <option value="customer_name">اسم العميل</option>
        <option value="seller_name">اسم البائع</option>
        <option value="customer_phone">رقم العميل</option>

    </select>
    <input type="text" class="search-input" id="search-input" placeholder="Model Name">
    </div>
    <div class="notification-icon">
        <a href="{{ route('orders.requests') }}" class="nav-link {{ $pendingOrdersCount > 0 ? 'light-up' : '' }}">
            <i class="fas fa-bell" id="notification"></i>
            @if ($pendingOrdersCount > 0)
                <span class="badge">{{ $pendingOrdersCount }}</span>
            @endif
        </a>
    </div>
</nav>
<body>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchType = document.getElementById('search-type');
            const searchInput = document.getElementById('search-input');
        
            function updatePlaceholder() {
                const selected = searchType.options[searchType.selectedIndex];
                searchInput.placeholder = 'البحث بـ ' + selected.text;
            }
        
            function performSearch() {
                const type = searchType.value;
                const value = searchInput.value;
                
                if (!type || !value) return;
        
                window.location.href = `${window.location.pathname}?search_type=${type}&search_value=${value}`;
            }
        
            searchType.addEventListener('change', updatePlaceholder);
            searchInput.addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    performSearch();
                }
            });
        
            // Initial placeholder update
            updatePlaceholder();
        });
        </script>
</body>
