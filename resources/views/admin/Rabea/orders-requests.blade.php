<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.rabea.dashboard')
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
                <th>Image</th>
                @php
                // Array of columns with their display names
                $columns = [
                    'order_number' => 'Order Number',
                    'order_kind' => 'Order Kind',
                    'order_details' => 'Order Details',
                    'ring_size' => 'Ring Size',
                    'weight' => 'Weight',
                    'gold_color' => 'Gold Color',
                    'order_fix_type' => 'Order Fix Type',
                    'customer_name' => 'Customer Name',
                    'customer_phone' => 'Customer Phone',
                    'seller_name' => 'Seller Name',
                    'deposit' => 'Deposit',
                    'rest_of_cost' => 'Rest of Cost',
                    'order_date' => 'Order Date',
                    'deliver_date' => 'Deliver Date',
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
                   
                    <td><img src="{{ asset('storage/' . $order->image_link) }}" alt="Order Image" style="max-width: 100%; height: auto;">
                    </td>
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
                        <a href="{{ route('orders.show', $order->id) }}" class="btn btn-primary">View</a>
                        <form action="{{ route('orders.accept', $order->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn-custom">Accept</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<script>
    // JavaScript for light-up effect and badge updating
    document.addEventListener('DOMContentLoaded', function () {
        const badge = document.querySelector('.badge');
        const link = document.querySelector('.nav-link');
        // Logic to handle updates (simulated for demonstration)
    });
</script>
</body>
</html>
