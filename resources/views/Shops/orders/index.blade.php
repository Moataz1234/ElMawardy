<!DOCTYPE html>
<html lang="en">
<head>
    @include('rabea.dashboard')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Orders</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/pagination.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/order-details.css') }}" rel="stylesheet">
    <style>
        table{
            direction: rtl;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Customer Orders</h2>

    <!-- Orders Table -->
    <table class="table">
        <thead>
            <tr>
                @php
                // Array of columns with their display names
                $columns = [
                    'shop_id ' =>'رقم الفرع',
                    'order_number' => 'رقم الاوردر',
                    'customer_name' => 'اسم العميل',
                    'customer_phone' => 'رقم العميل',
                    'seller_name' => 'اسم البائع',
                    'order_details' => 'موضوع الطلب',
                    'order_date' => 'تاريخ الاستلام',
                    'status' => ' حالة الطلب',

                ];
            @endphp
                @foreach ($columns as $field => $label)
                <th>
                    <div class="sort-container">
                        {{ $label }}
                    </div>
                </th>
                @endforeach
                {{-- <th>Delivery Date</th> --}}
                {{-- <th>Actions</th> --}}
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
                <tr>
                    <td>{{ $order->shop_id }}</td>
                    <td>{{ $order->order_number }}</td>
                    <td>{{ $order->customer_name }}</td>
                    <td>{{ $order->customer_phone }}</td>
                    <td>{{ $order->seller_name }}</td>
                    <td>{{ $order->order_details }}</td>

                    {{-- <td>{{ $order->deposit }}</td>
                    <td>{{ $order->rest_of_cost }}</td> --}}
                    <td>{{ $order->order_date }}</td>
                    <td>{{ $order->status }}</td>

                    {{-- <td>{{ $order->deliver_date }}</td> --}}
                    <td>
                    {{-- <div class="action-buttons">
                        @if($order->status === 'pending')
                            <a href="{{ route('orders.rabea.edit', $order->id) }}" class="action_button">Edit</a>
                        @endif
                    </div> --}}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- <button onclick="window.print()">Print this page</button> --}}

    {{ $orders->appends(request()->query())->links() }} <!-- Pagination with search and sort parameters -->
</div>
</body>
</html>
