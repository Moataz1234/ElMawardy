<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.rabea.dashboard')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Order</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body>
<div class="container">
    <h2>Edit Order</h2>

    <!-- Display success or error messages -->
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Form to edit the order -->
    <form class="custom-form" action="{{ route('orders.rabea.update', $order->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Order Kind -->
        <div class="form-group">
            <label for="order_kind">Order Kind:</label>
            <input type="text" id="order_kind" name="order_kind" class="form-control" value="{{ $order->order_kind }}" required readonly>
        </div>

        <!-- Order Fix Type -->
        <div class="form-group">
            <label for="order_fix_type">Order Fix Type:</label>
            <input type="text" id="order_fix_type" name="order_fix_type" class="form-control" value="{{ $order->order_fix_type }}" required readonly>
        </div>
        
        <!-- Order Details -->
        <div class="form-group">
            <label for="order_details">Order Details:</label>
            <textarea id="order_details" name="order_details" class="form-control">{{ $order->order_details }}</textarea>
        </div>
        <!-- Ring Size -->
        <div class="form-group">
            <label for="ring_size">Ring Size:</label>
            <input type="number" id="ring_size" name="ring_size" class="form-control" value="{{ $order->ring_size }}">
        </div>

        <!-- Weight -->
        <div class="form-group">
            <label for="weight">Weight:</label>
            <input type="number" id="weight" step="0.1" name="weight" class="form-control" value="{{ $order->weight }}">
        </div>

        <!-- Gold Color -->
        <div class="form-group">
            <label for="gold_color">Gold Color:</label>
            <input type="text" id="gold_color" name="gold_color" class="form-control" value="{{ $order->gold_color }}" readonly>
        </div>

        <!-- Customer Name -->
        <div class="form-group">
            <label for="customer_name">Customer Name:</label>
            <input type="text" id="customer_name" name="customer_name" class="form-control" value="{{ $order->customer_name }}" readonly>
        </div>

        <!-- Customer Phone -->
        <div class="form-group">
            <label for="customer_phone">Customer Phone:</label>
            <input type="text" id="customer_phone" name="customer_phone" class="form-control" value="{{ $order->customer_phone }}" readonly>
        </div>

        <!-- Seller Name -->
        <div class="form-group">
            <label for="seller_name">Seller Name:</label>
            <input type="text" id="seller_name" name="seller_name" class="form-control" value="{{ $order->seller_name }}" readonly>
        </div>

        <!-- Deposit -->
        <div class="form-group">
            <label for="deposit">Deposit:</label>
            <input type="text" id="deposit" name="deposit" class="form-control" value="{{ $order->deposit }}" readonly>
        </div>

        <!-- Rest of Cost -->
        <div class="form-group">
            <label for="rest_of_cost">Rest of Cost:</label>
            <input type="text" id="rest_of_cost" name="rest_of_cost" class="form-control" value="{{ $order->rest_of_cost }}">
        </div>

        <!-- Order Date -->
        <div class="form-group">
            <label for="order_date">Order Date:</label>
            <input type="date" id="order_date" name="order_date" class="form-control" value="{{ $order->order_date }}" readonly>
        </div>

        <!-- Deliver Date -->
        <div class="form-group">
            <label for="deliver_date">Deliver Date:</label>
            <input type="date" id="deliver_date" name="deliver_date" class="form-control" value="{{ $order->deliver_date }}">
        </div>

        <!-- Status -->
        <div class="form-group">
            <label for="status">Status:</label>
            <select id="status" name="status" class="form-control">
                <option value="في المحل" {{ $order->status == 'في المحل' ? 'selected' : '' }}>في المحل</option>
                <option value="في المصنع" {{ $order->status == 'في المصنع' ? 'selected' : '' }}>في المصنع </option>
                <option value="في الورشة" {{ $order->status == 'في الورشة' ? 'selected' : '' }}>في الورشة</option>
                <option value="في الدمغة" {{ $order->status == 'في الدمغة' ? 'selected' : '' }}>في الدمغة</option>
                <option value="خلص" {{ $order->status == 'خلص' ? 'selected' : '' }}>خلص</option>


            </select>
        </div>

        <!-- Image -->
        <div class="form-group">
            <label for="image_link">Order Image:</label>
            @if($order->image_link)
                <img src="{{ asset('storage/' . $order->image_link) }}" alt="Order Image" style="max-width: 200px; display:block; margin-bottom: 10px;">
            @endif
            {{-- <input type="file" id="image_link" name="image_link" class="form-control"> --}}
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-success">Save Changes</button>
    </form>

    <div class="card-footer">
        <a href="{{ route('orders.rabea.index') }}" class="btn btn-primary">Back to Orders</a>
    </div>
</div>
</body>
</html>
