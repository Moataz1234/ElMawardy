<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Requests</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
                .btn-custom {
    padding: 10px 20px;
    border-radius: 5px;
    border: none;
    color: white;
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
                <th>Order Number</th>
                <th>Order Kind</th>
                <th>Order Details</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
                <tr>
                    <td><img src="{{ asset('storage/' . $order->image_link) }}" alt="Order Image" style="max-width: 100%; height: auto;"></td>
                    <td>{{ $order->order_number }}</td>
                    <td>{{ $order->order_kind }}</td>
                    <td>{{ $order->order_details }}</td>
                    <td>
                        <a href="{{ route('orders.show', $order->id) }}" class="btn btn-primary">View</a>
                        <form action="{{ route('orders.accept', $order->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-success">Accept</button>
                        </form>
                        <form action="{{ route('orders.reject', $order->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-danger">Reject</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
</body>
</html>
