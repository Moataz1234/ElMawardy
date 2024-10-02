<!DOCTYPE html>
<html lang="en">
<head>
    @include('dashboard')
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Your Shop's Items</title>
   <link href="{{ asset('css/app.css') }}" rel="stylesheet">
   <link href="{{ asset('css/style.css') }}" rel="stylesheet">
   <link href="{{ asset('css/pagination.css') }}" rel="stylesheet">
   <link href="{{ asset('css/navbar.css') }}" rel="stylesheet">
   <script>
    // JavaScript to toggle the ring size field
    function toggleRingSizeField() {
        const orderKind = document.getElementById('order_kind').value;
        const ringSizeField = document.getElementById('ring_size_field');

        // Show the ring size field only if the selected order kind is "ring"
        if (orderKind === 'Ring') {
            ringSizeField.style.display = 'block'; // Show the ring size field
        } else {
            ringSizeField.style.display = 'none'; // Hide the ring size field
        }
    }

    // Run this function after the page loads to ensure the ring size field is hidden initially
    document.addEventListener('DOMContentLoaded', function() {
        toggleRingSizeField(); // Hide initially if not "ring"
        document.getElementById('order_kind').addEventListener('change', toggleRingSizeField);
    });
</script>
</head>
<body>
<form class="custom-form" action="{{ route('orders.store') }}" method="POST" enctype="multipart/form-data" accept-charset="UTF-8">
    @csrf
{{-- 
<div class="mb-3">
    <label for="shop_id" class="form-label">Shop Name</label>
    <select name="shop_id" id="shop_id" class="form-select" required>
        @foreach($shops as $shop)
            <option value="{{ $shop->id }}">{{ $shop->name }}</option>
        @endforeach
    </select>
</div> 
--}}
        
    <!-- Order Kind -->
    <label for="order_kind">Order Kind:</label>
    <select name="order_kind" id="order_kind" required>
        <option value="">Select Order Kind</option>
            @foreach ($kinds as $kind)
                <option value="{{ $kind }}">{{ $kind }}</option>
            @endforeach
    </select>
    <!-- Order Fix Type -->
    <label for="order_fix_type">Order Fix Type:</label>
    <input type="text" name="order_fix_type" id="order_fix_type" required>

    <!-- Ring Size -->
    <div id="ring_size_field" style="display:none;">
        <label for="ring_size">Ring Size:</label>
        <input type="number" name="ring_size" id="ring_size">
    </div>

    <!-- Order Details -->
    <label for="order_details">Order Details:</label>
    <textarea name="order_details" id="order_details"></textarea>

   
    <div class="mb-3">
        <label for="weight" class="form-label">Weight </label>
        <input type="number" class="form-control" name="weight" id="weight" step="0.1">
    </div>

    <label for="gold_color">Gold Color</label>
    <select name="gold_color" id="gold_color" required>
        <option >Select Gold Color</option>
            @foreach ($gold_colors as $gold_color)
                <option value="{{ $gold_color }}">{{ $gold_color }}</option>
            @endforeach
    </select>

    <div class="mb-3">
        <label for="customer_name" class="form-label">Customer Name</label>
        <input type="text" class="form-control" name="customer_name" id="customer_name" required>
    </div>
    
    <div class="mb-3">
        <label for="customer_phone" class="form-label">Customer Phone</label>
        <input type="number" class="form-control" name="customer_phone" id="customer_phone" maxlength="11" required>
    </div>

    <div class="mb-3">
        <label for="seller_name" class="form-label">Seller Name</label>
        <input type="text" class="form-control" name="seller_name" id="seller_name" required>
    </div>

    <div class="mb-3">
        <label for="deposit" class="form-label">Deposit</label>
        <input type="number" class="form-control" name="deposit" id="deposit" step="0.01" required>
    </div>

    <div class="mb-3">
        <label for="rest_of_cost" class="form-label">Rest of Cost</label>
        <input type="number" class="form-control" name="rest_of_cost" id="rest_of_cost" step="0.01" required>
    </div>

    <div class="mb-3">
        <label for="order_date" class="form-label">Order Date</label>
        <input type="date" class="form-control" name="order_date" id="order_date" required>
    </div>

    {{--    
    <div class="mb-3">
        <label for="deliver_date" class="form-label">Deliver Date</label>
        <input type="date" class="form-control" name="deliver_date" id="deliver_date" required>
    </div>

     <div class="mb-3">
        <label for="status" class="form-label">Order Status</label>
        <select name="status" id="status" class="form-select" required>
            <option value="في المحل">في المحل</option>
            <option value="في الورشة">في الورشة</option>
            <option value="خلص">خلص</option>
            <option value="تم استلامه">تم استلامه</option>
            <option value="في الدمغة">في الدمغة</option>
        </select>
    </div> --}}
        <!-- Upload Image -->
    <label for="image_link">Upload Image:</label>
    <input type="file" name="image_link" id="image_link">

    <button type="submit">Submit Order</button>
</form>

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

</body>

</html>
