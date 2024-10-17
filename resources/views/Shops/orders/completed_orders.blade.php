<!-- resources/views/orders/completed_orders.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Completed Orders</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2>Completed Orders</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table">
            <thead>
                <tr>
                    <th>Shop Number</th>
                    <th>Order Number</th>
                    <th>Customer Name</th>
                    <th>Order Details</th>
                    <th>Completion Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($completedOrders as $order)
                    <tr>
                        <td>{{ $order->order_id }}</td>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->customer_name }}</td>
                        <td>{{ $order->order_details }}</td>
                        <td>{{ $order->updated_at->format('d/m/Y') }}</td>

                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
