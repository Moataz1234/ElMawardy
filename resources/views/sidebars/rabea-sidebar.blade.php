    <div class="sidebar_order">
        <!-- Rabea Navigation Links -->
        <div class="nav-links mt-4">
            <a href="{{ route('orders.rabea.index') }}" class="nav-link {{ request()->routeIs('orders.rabea.index') ? 'active' : '' }}">
                <i class="fas fa-shopping-cart me-2"></i> Orders Dashboard
            </a>
            <a href="{{ route('rabea.items') }}" class="nav-link {{ request()->routeIs('rabea.items') ? 'active' : '' }}">
                <i class="fas fa-gem me-2"></i> Inventory
            </a>
            <a href="{{ route('rabea.did.requests') }}" class="nav-link {{ request()->routeIs('rabea.did.requests') ? 'active' : '' }}">
                <i class="fas fa-tools me-2"></i> Workshop Requests
            </a>
        </div>
        
        <form method="GET" action="{{ url()->current() }}" id="filterForm">    
            <div class="radio-group">
                <h3>Sort By</h3>
                <div class="page-2">
                    <label>
                        <input type="radio" name="sort" value="order_number" {{ request('sort') == 'order_number' ? 'checked' : '' }}>
                        <span class="custom-radio"></span> رقم الأوردر
                    </label>
                    <label>
                        <input type="radio" name="sort" value="customer_name" {{ request('sort') == 'customer_name' ? 'checked' : '' }}>
                        <span class="custom-radio"></span> اسم العميل
                    </label>
                    <label>
                        <input type="radio" name="sort" value="seller_name" {{ request('sort') == 'seller_name' ? 'checked' : '' }}>
                        <span class="custom-radio"></span> اسم البائع
                    </label>
                    {{-- <label>
                        <input type="radio" name="sort" value="status" {{ request('sort') == 'status' ? 'checked' : '' }}>
                        <span class="custom-radio"></span> الحالة
                    </label> --}}
                </div>
                <div class="button-container">
                    <button type="submit" class="reset-button">Set</button>
                    <button type="button" onclick="location.href='{{ url()->current() }}';"  class="reset-button">Reset</a>
                    </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get all filter inputs
            const filterInputs = document.querySelectorAll('#filterForm select, #filterForm input');
            
            // Add change event listener to each input
            filterInputs.forEach(input => {
                input.addEventListener('change', function() {
                    document.getElementById('filterForm').submit();
                });
            });
        });
        </script>