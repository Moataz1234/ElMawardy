
<div class="spreadsheet">
    <h2>Completed Orders</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table >
            <thead>
                <tr>
                    <th>رقم الفرع</th>
                    <th>رقم الأوردر</th>
                    <th>اسم العميل</th>
                    <th>رقم العميل</th>
                    <th>موضوع الطلب</th>
                    <th>Completion Date</th>
                </tr>
            </thead>
            <tbody id="table-body">
                @foreach ($orders as $order)
                <tr data-order-id="{{ $order->id }}">
                        <td>{{ $order->shop_id }}</td>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->customer_name }}</td>
                        <td>{{ $order->customer_phone }}</td>
                        <td>{{ $order->order_details }}</td>
                        <td>{{ $order->updated_at->format('d/m/Y') }}</td>

                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>