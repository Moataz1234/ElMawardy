<!DOCTYPE html>
<html lang="en">
<head>
    @include('components.navbar')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/order-details.css') }}" rel="stylesheet">
    <link href="{{ asset('css/navbar.css') }}" rel="stylesheet">

    <style>
        /* Container for order details and items, displayed side by side */
        .order-wrapper {
            display: flex;
            gap: 20px;
            justify-content: space-between;
            align-items: flex-start;
        }

        /* Styling for the order details */
        .order-details, .order-items {
            flex: 1;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        /* Order items table styling */
        .order-items-table {
            width: 100%;
            border-collapse: collapse;
        }

        .order-items-table th, .order-items-table td {
            padding: 8px 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .order-items-table th {
            background-color: #f1f1f1;
        }

        .item-image {
            max-width: 80px;
            height: auto;
        }
    </style>
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

    <!-- Order Details Card -->
            <div class="card-header">
            Order #{{$order->shop->name}} - {{$order->order_number}}
        </div>
            <div class="order-wrapper">
                

                <!-- Order Details -->
                <div class="order-details">
                    <p><strong>Shop :</strong> {{ $order->shop->name }}</p>
                    <p><strong>Customer Name:</strong> {{ $order->customer_name }}</p>
                    <p><strong>Customer Phone:</strong> {{ $order->customer_phone }}</p>
                    <p><strong>Seller Name:</strong> {{ $order->seller_name }}</p>
                    <p><strong>Order Date:</strong> {{ $order->order_date }}</p>
                    <p><strong>Deliver Date:</strong> {{ $order->deliver_date }}</p>
                    <p><strong>Payment Method:</strong> {{ $order->payment_method }}</p>
                    <p><strong>Deposit:</strong> {{ $order->deposit }}</p>
                    <p><strong>Rest of Cost:</strong> {{ $order->rest_of_cost }}</p>
                    <p><strong>Order Details:</strong> {{ $order->order_details }}</p>
                    <p><strong>Status:</strong> {{ $order->status }}</p>
                </div>
           

    <!-- Order Items Section -->
        <div class="order-items">
        <h3>Order Items</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Item Kind</th>
                    <th>Quantity</th>
                    <th>Ring Size</th>
                    <th>Weight</th>
                    <th>Gold Color</th>
                    <th>Fix Type</th>
                    <th>Item Image</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr>
                        <td>{{ $item->order_kind }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->ring_size }}</td>
                        <td>{{ $item->weight }}</td>
                        <td>{{ $item->gold_color }}</td>
                        <td>{{ $item->order_fix_type }}</td>
                        <td>
                            @if($item->image_link)
                            <img src="{{ asset('storage/' . $orderItem->image_link) }}" alt="Order Item Image">
                            @else
                                <p>No image available for this item.</p>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>


    <!-- Action Buttons -->
    <div class="card-footer" style="margin-top: 20px;">
        {{-- <a href="{{ route('orders.rabea.index') }}" class="btn btn-primary">Back to Orders</a> --}}
        <a  href="{{ route('orders.rabea.edit', $order->id) }}" >Edit</a>
    </div>
</div>
</body>
</html>
