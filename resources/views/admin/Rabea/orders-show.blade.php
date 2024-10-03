<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.rabea.dashboard')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/order-details.css') }}" rel="stylesheet">

</head>
<body>
<div class="container">
    <h2>Order Details</h2>

    <!-- Display success or error messages -->
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-header">
            Order {{$order->shop_id}}#{{ $order->order_number }}
        
        </div>
        <div class="card-body">
            <div class="order-container">
            <div class="image">
            @if($order->image_link)
            <p><strong>Order Image:</strong></p>
            <img src="{{ asset('storage/' . $order->image_link) }}" alt="Order Image" style="max-width: 100%; height: auto;">
            @else
            <p>No image available for this order.</p>
            @endif
            </div>
            <div class="order-details">
            <p> <strong>Shop :</strong> {{ $order->shop->name }}</p>
            <p> <strong>Order Kind :</strong> {{ $order->order_kind }}</p>
            <p><strong>Order Fix Type:</strong> {{ $order->order_fix_type }}</p>
            <p><strong>Ring Size:</strong> {{ $order->ring_size }}</p>
            <p><strong>Weight:</strong> {{ $order->weight }}</p>
            <p><strong>Gold Color:</strong> {{ $order->gold_color }}</p>
            <p><strong>Order Details:</strong> {{ $order->order_details }}</p>
            <p><strong>Customer Name:</strong> {{ $order->customer_name }}</p>
            <p><strong>Customer Phone:</strong> {{ $order->customer_phone }}</p>
            <p><strong>Seller Name:</strong> {{ $order->seller_name }}</p>
            <p><strong>Deposit:</strong> {{ $order->deposit }}</p>
            <p><strong>Rest of Cost:</strong> {{ $order->rest_of_cost }}</p>
            <p><strong>Order Date:</strong> {{ $order->order_date }}</p>
            <p><strong>Deliver Date:</strong> {{ $order->deliver_date }}</p>
            <p><strong>Status:</strong> {{ $order->status }}</p>
        </div>
        </div>
        </div>
        <div class="card-footer">
            <a href="{{ route('orders.rabea.index') }}" class="btn btn-primary">Back to Orders</a>
            <a href="{{ route('orders.rabea.edit',$order->id)}}" class="btn btn-primary">Edit</a>

        </div>
    </div>
</div>
</body>
</html>
