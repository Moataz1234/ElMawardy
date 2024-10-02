<!-- resources/views/images/index.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    @include('dashboard')
    {{-- @include("GoldCatalog.Shared.sideBar") --}}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalog Items</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/pagination.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body>
<div class="container">
    <h2>Customer Orders</h2>

    <!-- Display success or error messages -->
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Orders Table -->
    <table class="table">
        <thead>
            <tr>
                <th>Order Number</th>
                <th>Order Kind</th>
                <th>Order Details</th>
                <th>Ring Size</th>
                <th>Weight</th>
                <th>Gold Color</th>
                <th>Order Fix Type</th>
                <th>Customer Name</th>
                <th>Customer Phone</th>
                <th>Seller Name</th>
                <th>Deposit</th>
                <th>Rest of Cost</th>
                <th>Order Date</th>
                <th>Deliver Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
                <tr>
                    <td>{{ $order->order_number }}</td>
                    <td>{{ $order->order_kind }}</td>
                    <td>{{ $order->order_details }}</td>
                    <td>{{ $order->ring_size }}</td>
                    <td>{{ $order->weight }}</td>
                    <td>{{ $order->gold_color }}</td>
                    <td>{{ $order->order_fix_type }}</td>
                    <td>{{ $order->customer_name }}</td>
                    <td>{{ $order->customer_phone }}</td>
                    <td>{{ $order->seller_name }}</td>
                    <td>{{ $order->deposit }}</td>
                    <td>{{ $order->rest_of_cost }}</td>
                    <td>{{ $order->order_date }}</td>
                    <td>{{ $order->deliver_date }}</td>
                    <td>{{ $order->status }}</td>
                    <td>
                        <!-- You can add actions like view, edit, delete here -->
                        <a href="{{ route('orders.show', $order->id) }}" class="action_button">View</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
</body>
</html>
