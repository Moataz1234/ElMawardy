
<body>
    
    <div style="display: block">
        <p ><strong>Date: </strong>{{ \Carbon\Carbon::now()->format('Y-m-d') }}</p>
    </div>
<div class="container">

    <!-- Display success or error messages -->
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif (session('error'))
        <div class="light-up">{{ session('error') }}</div>
    @endif


    <!-- Orders Table -->
    <div class="printable">
    <table style="width: 1200px">
        <thead>
            <tr>
                <th>موضوع الطلب</th>
                <th>رقم الاوردر</th>
                <th>اسم البائع</th>
                <th>رقم المحل</th>

            </tr>
        </thead>
        <tbody id="table-body">
            @foreach ($orders as $order)
                <tr data-order-id="{{ $order->id }}">
                    <td class="order_details">{{ $order->order_details }}</td>                    
                    <td>{{ $order->shop_id }}-{{ $order->order_number }}</td>
                    <td>{{ $order->seller_name }}</td>
                    <td>{{ $order->shop_id }}</td>
                </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

    {{ $orders->appends(request()->query())->links() }} <!-- Pagination with search and sort parameters -->
</div>
<button  onclick="window.print()">Print this page</button>

</body>
</html>
