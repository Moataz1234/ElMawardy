<div class="sidebar">
    <!-- Sorting options -->
    <form method="GET" action="{{ route('orders.rabea.index') }}">
        <label><input type="radio" name="sort" value="branch_number" {{ request('sort') == 'branch_number' ? 'checked' : '' }}> رقم الفرع</label><br/>
        <label><input type="radio" name="sort" value="order_number" {{ request('sort') == 'order_number' ? 'checked' : '' }}> رقم الاوردر</label><br/>
        <label><input type="radio" name="sort" value="customer_name" {{ request('sort') == 'customer_name' ? 'checked' : '' }}> اسم العميل</label><br/>
        <!-- Add more sorting options as needed -->
        <button type="submit">Set</button>
    </form>
</div>