<!DOCTYPE html>
<html lang="en">
<head>
@include('components.navbar')
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Customer Orders</title>
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOM8kNq7/8z2zVw5U5NAuTp6WVsMSXJ1pO9aX1l" crossorigin="anonymous">

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
{{-- <link href="{{ asset('css/pagination.css') }}" rel="stylesheet"> --}}
{{-- <link href="{{ asset('css/first_page.css') }}" rel="stylesheet"> --}}
<link href="{{ asset('css/order-details.css') }}" rel="stylesheet">
<link href="{{ asset('css/navbar.css') }}" rel="stylesheet">
<link href="{{ asset('css/style.css') }}" rel="stylesheet">


<style>
    /* Additional Styling for Layout */
    body {
        font-family: Arial, sans-serif;
    }

    .custom-form {
        /* background-color: #504db4; */
        padding: 20px;
        border-radius: 10px;
        color: #000000;
        max-width: 800px;
        margin: auto;
        direction: rtl;
        
    }

    .form-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
    }

    .column {
        width: 48%;
    }
    .column2{
        width: 20%;
    }
    .form-group label {
        font-weight: bold;
    }

    textarea, input[type="text"], input[type="number"], input[type="date"], select {
        width: 100%;
        padding: 8px;
        margin-top: 5px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .btn-custom, .btn-primary {
        display: block;
        padding: 10px;
        color: #ffffff;
        border: none;
        border-radius: 5px;
        margin-top: 10px;
        margin-left: 330px;
        width: 100%;
    }

</style>
</head>
<body>
<form class="custom-form" action="{{ route('orders.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

   
    <!-- Row 1 -->
    <div class="form-row">
        <div class="form-group column">
            <label for="customer_name">اسم العميل</label>
            <input type="text" class="form-control" name="customer_name" id="customer_name" required>
        </div>
        <div class="form-group column">
            <label for="customer_phone">تليفون العميل</label>
            <input type="number" class="form-control" name="customer_phone" id="customer_phone" maxlength="11" required>
        </div>
    </div>

    <!-- Row 2 -->
    <div class="form-row">
        <div class="form-group column">
            <label for="seller_name">البائع</label>
            <input type="text" class="form-control" name="seller_name" id="seller_name" required>
        </div>
        
        <div class="form-group column2">
            <label for="deposit">المدفوع</label>
            <input type="number" class="form-control" name="deposit" id="deposit" step="0.01">
        </div>
        <div class="form-group column2">
            <label for="rest_of_cost">الباقي</label>
            <input type="number" class="form-control" name="rest_of_cost" id="rest_of_cost" step="0.01">
        </div>
    </div>

    <!-- Row 3 -->
    <div class="form-row">
        
        <div class="form-group column">
            <label for="order_date">تاريخ الاستلام</label>
            <input type="date" class="form-control" name="order_date" id="order_date">
        </div>
        <div class="form-group column">
            <label for="payment_method">طريقة الدفع</label>
            <select class="form-control" name="payment_method" id="payment_method">
                <option value="visa">لا يوجد</option>
                <option value="visa">Visa</option>
                <option value="value">Value</option>
                <option value="cash">Cash</option>
                <option value="instapay">Instapay</option>
            </select>
        </div>
    </div>

    <!-- Row 4 -->
    <div class="form-group">
            <label for="order_details">موضوع الطلب :</label>
            <textarea style="height: 150px; width: 100%;" name="order_details" id="order_details"></textarea>
    </div>

    <!-- Row 5: Text Area for Order Details -->
    

    <!-- Order Items Section -->
    <div class="bg-green-500 text-center text-white font-semibold py-2 rounded-md mb-4">Order Items</div>
    <div id="order-items">
        <div class="order-item" id="order-item-template" style="display: none;">
            <div class="form-row">
                <div class="form-group">
                    <label for="order_kind">النوع</label>
                    <select name="order_kind[]" class="form-control" >
                        @foreach ($kinds as $kind)
                            <option value="{{ $kind }}">{{ $kind }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group column">
                    <label for="order_fix_type">المشكلة</label>
                    <select name="order_fix_type[]" class="form-control">
                        <option value="اوردر جديد">اوردر جديد</option>
                        <option value="تصليح">تصليح</option>
                        <option value="عمل مقاس">عمل مقاس</option>
                        <option value="تلميع">تلميع</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group column">
                    <label for="quantity">الكمية</label>
                    <input type="number" class="form-control" name="quantity[]">
                </div>
                <div class="form-group column ring-size" style="display: none;">
                    <label for="ring_size">مقاس الخاتم</label>
                    <input type="number" class="form-control" name="ring_size[]" disabled>
                </div>
            </div>

            <div class="form-row">
              
                <div class="form-group column mb-3">
                    <input type="checkbox" class="toggleLabel"> عينة
                </div>
            </div>



            <div class="mb-3 weight_field" style="display: none">
                <label for="weight" class="form-label">الوزن</label>
                <input type="number" class="form-control" name="weight[]" step="0.001">
            </div>

            <div class="form-group image_field" style="display: none">
                <label for="image_link">Item Image Link</label>
                <input type="file" class="form-control" name="image_link[]">
            </div>
            <!-- Remove Item Button -->
            <button type="button" class="remove-item">Remove Item</button>
        </div>
    </div>

    <button type="button" id="add-item" class="btn-custom">Add Item</button>

    <div class="form-group">
        <button style="margin: 20px 300px" type="submit" class="btn btn-primary">Submit Order</button>
    </div>
</form>
<script src="{{ asset('js/order_details.js') }}"></script>
</body>
</html>
