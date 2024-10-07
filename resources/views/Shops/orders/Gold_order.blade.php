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
     function toggleLabelVisibility() {
        var checkbox = document.getElementById("toggleLabel");
        var weight_field = document.getElementById("weight_field");

        
        if (checkbox.checked) {
            weight_field.style.display = "inline"; 
        } else {
            weight_field.style.display = "none"; 
        }
    }
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
    function toggleCustomerDetails() {
        var textArea = document.getElementById("order_detail");
        var byCustomer = document.getElementById("by_customer").checked;
        var byShop = document.getElementById("by_shop").checked;

        // Show textarea if either radio button is selected
        if (byCustomer || byShop) {
            textArea.style.display = "block";
        }
    }
</script>
<style>
    .radio-group {
        display: flex;
        align-items: center;
    }
    .radio-group input[type="radio"] {
        margin-right: 5px;
    }
    .radio-group label {
        margin-right: 15px;
    }
</style>

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
    <label for="order_kind">النوع</label>
    <select name="order_kind" id="order_kind" required>
            @foreach ($kinds as $kind)
                <option value="{{ $kind }}">{{ $kind }}</option>
            @endforeach
    </select>
    <!-- Order Fix Type -->
    <label for="order_fix_type">المشكلة</label>
    <select id="order_fix_type" name="order_fix_type" class="form-control">
        <option value="اوردر جديد" > اوردر جديد</option>
        <option value=" تصليح"  >تصليح</option>
        <option value="عمل مقاس"  >عمل مقاس</option>
        <option value=" تلميع"  >تلميع</option>

    </select>
    <!-- Ring Size -->
    <div id="ring_size_field" style="display:none;">
        <label for="ring_size">مقاس الخاتم</label>
        <input type="number" name="ring_size" id="ring_size">
    </div>

    <!-- Order Details -->
    <label for="order_details"> موضوع الطلب :</label>
    <textarea style="height: 200px" name="order_details" id="order_details"></textarea>

    <div class="mb-3">
        <input type="checkbox" id="toggleLabel" onclick="toggleLabelVisibility()"> عينة  </div>
    <div class="mb-3" id="weight_field" style="display: none">
        <label for="weight" class="form-label" >الوزن </label>
        <input type="number" class="form-control" name="weight" step="0.1">
    </div>

    <label for="gold_color">اللون </label>
    <select name="gold_color" id="gold_color" required>
            @foreach ($gold_colors as $gold_color)
                <option value="{{ $gold_color }}">{{ $gold_color }}</option>
            @endforeach
    </select>
    <div class="radio-group">
        <input type="radio" id="by_customer" name="details_type" value="by_customer" onclick="toggleCustomerDetails()"> 
        <label for="by_customer">خاصة بالعميل</label>

        <input type="radio" id="by_shop" name="details_type" value="by_shop" onclick="toggleCustomerDetails()">
        <label for="by_shop">خاصة بالمحل</label>
    </div>
    <textarea style="height: 100px;width:500px; display:none" name="order_details" id="order_detail"></textarea>

<div class="mb-3">
        <label for="customer_name" class="form-label"> اسم العميل</label>
        <input type="text" class="form-control" name="customer_name" id="customer_name" required>
    </div>
    
    <div class="mb-3">
        <label for="customer_phone" class="form-label">تليفون العميل</label>
        <input type="number" class="form-control" name="customer_phone" id="customer_phone" maxlength="11" required>
    </div>

    <div class="mb-3">
        <label for="seller_name" class="form-label">البائع </label>
        <input type="text" class="form-control" name="seller_name" id="seller_name" required>
    </div>

    <div class="mb-3">
        <label for="deposit" class="form-label">المدفوع</label>
        <input type="number" class="form-control" name="deposit" id="deposit" step="0.01" >
    </div>

    <div class="mb-3">
        <label for="rest_of_cost" class="form-label">الباقي</label>
        <input type="number" class="form-control" name="rest_of_cost" id="rest_of_cost" step="0.01" >
    </div>
    <div class="mb-3">
        <label for="payment_method" class="form-label">طريقة الدفع</label>
        <select type="number" class="form-control" name="payment_method" id="payment_method" step="0.01" >
        <option value="visa">Visa</option>
        <option value="value">Value</option>
        <option value="cash">Cash</option>
        <option value="instapay">Instapay</option>

        </select>
        </div>
    <div class="mb-3">
        <label for="order_date" class="form-label">تاريخ الاستلام6</label>
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
