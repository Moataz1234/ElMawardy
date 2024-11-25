<!DOCTYPE html>
<html lang="en">
<head>
    @include('rabea.dashboard')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Requests</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/pagination.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

    <style>
    .btn-custom {
    padding: 10px 20px;
    border-radius: 5px;
    border: none;
    color: rgb(0, 0, 0);
    background-color: #49dc35;
}

.btn-primary-custom {
    background-color: #007bff; /* Primary color */
}

.btn-success-custom {
    background-color: #28a745; /* Success color */
}

.btn-danger-custom {
    background-color: #dc3545; /* Danger color */
}
        </style>
</head>
<body>
    <form action="{{ route('orders.accept') }}" method="POST">
        @csrf
<div class="container">
    <h2>Order Requests</h2>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>check</th>
                @php
                // Array of columns with their display names
                $columns = [
                    'shop_id    ' =>'shop_id',
                    'order_number' => 'Order Number',
                    'order_details' => 'Order Details',
                    'customer_name' => 'Customer Name',
                    'customer_phone' => 'Customer Phone',
                    'seller_name' => 'Seller Name',
                    'order_date' => 'Order Date',
                    'status' => 'Status',
                ];
            @endphp

            @foreach ($columns as $field => $label)
                <th>
                    <div class="sort-container">
                        {{ $label }}
                        <form method="GET" action="{{ route('orders.rabea.index') }}" style="display:inline;">
                            <input type="hidden" name="sort" value="{{ $field }}">
                            <input type="hidden" name="direction" value="{{ request('direction') === 'asc' ? 'desc' : 'asc' }}">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <button type="submit">&#8597;</button>
                        </form>
                    </div>
                </th>
                @endforeach
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
                <tr>
                   
                    <td>
                        <input type="checkbox" name="order_ids[]" value="{{ $order->id }}" class="order-checkbox">
                    </td>                    
                    <td>{{ $order->shop_id }}</td>
                    <td>{{ $order->shop_id }}-{{ $order->order_number }}</td>
                    <td>{{ $order->order_details }}</td>
                    <td>{{ $order->customer_name }}</td>
                    <td>{{ $order->customer_phone }}</td>
                    <td>{{ $order->seller_name }}</td>
                    <td>{{ $order->order_date }}</td>
                    <td>{{ $order->status }}</td>
                    {{-- <td>
                        <form action="{{ route('orders.accept', $order->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn-custom">Review</button>
                        </form>
                    </td> --}}
                </tr>
            @endforeach

        </tbody>
    </table>
    <button type="submit" class="btn-custom">Accept Selected Orders</button>

    </div>
</form>
<script src="{{ asset('js/order_details.js') }}"></script>

</body>
</html>
