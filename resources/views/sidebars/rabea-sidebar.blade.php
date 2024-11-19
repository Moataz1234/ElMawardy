
<div class="page-2">
    <div class="container">
        <div class="filter-search">
            <form method="GET" action="{{ route('orders.rabea.index') }}">
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
                        <label>
                            <input type="radio" name="sort" value="status" {{ request('sort') == 'status' ? 'checked' : '' }}>
                            <span class="custom-radio"></span> الحالة
                        </label>
                    </div>
                </div>
                <div class="horizontal-line"></div>
                <div class="button-container">
                    <button type="submit" class="reset-button">Set</button>
                </div>
            </form>
        </div>
    </div>
</div>