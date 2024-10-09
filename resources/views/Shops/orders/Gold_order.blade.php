<!DOCTYPE html>
<html lang="en">
<head>
@include('admin.rabea.dashboard')
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Customer Orders</title>
<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<link href="{{ asset('css/pagination.css') }}" rel="stylesheet">
<link href="{{ asset('css/style.css') }}" rel="stylesheet">
<link href="{{ asset('css/order-details.css') }}" rel="stylesheet">

</head>
<body>
<form class="custom-form" action="{{ route('orders.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <!-- Customer Details -->
    <div class="mb-3">
        <label for="customer_name" class="form-label">اسم العميل</label>
        <input type="text" class="form-control" name="customer_name" id="customer_name" required>
    </div>

    <div class="mb-3">
        <label for="customer_phone" class="form-label">تليفون العميل</label>
        <input type="number" class="form-control" name="customer_phone" id="customer_phone" maxlength="11" required>
    </div>

    <div class="mb-3">
        <label for="seller_name" class="form-label">البائع</label>
        <input type="text" class="form-control" name="seller_name" id="seller_name" required>
    </div>

    <!-- Order Details -->
    <label for="order_details">موضوع الطلب :</label>
    <textarea style="height: 200px" name="order_details" id="order_details"></textarea>

    <div class="mb-3">
        <label for="deposit" class="form-label">المدفوع</label>
        <input type="number" class="form-control" name="deposit" id="deposit" step="0.01">
    </div>

    <div class="mb-3">
        <label for="rest_of_cost" class="form-label">الباقي</label>
        <input type="number" class="form-control" name="rest_of_cost" id="rest_of_cost" step="0.01">
    </div>

    <div class="form-group">
        <label for="payment_method" class="form-label">طريقة الدفع</label>
        <select class="form-control" name="payment_method" id="payment_method">
            <option value="visa">Visa</option>
            <option value="value">Value</option>
            <option value="cash">Cash</option>
            <option value="instapay">Instapay</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="order_date" class="form-label">تاريخ الاستلام</label>
        <input type="date" class="form-control" name="order_date" id="order_date">
    </div>

    <!-- Order Items Section -->
    <h3 style="text-align: center;background-color:rgb(0, 255, 13)">Order Items</h3>
    <div id="order-items">
        <div class="order-item" id="order-item-template" style="display: none;">
            <div class="form-group">
                <label for="order_kind">النوع</label>
                <select name="order_kind[]" class="form-control" >
                    @foreach ($kinds as $kind)
                        <option value="{{ $kind }}">{{ $kind }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="order_fix_type">المشكلة</label>
                <select type="string" name="order_fix_type[]" class="form-control">
                    <option value="اوردر جديد">اوردر جديد</option>
                    <option value="تصليح">تصليح</option>
                    <option value="عمل مقاس">عمل مقاس</option>
                    <option value="تلميع">تلميع</option>
                </select>
            </div>

            <div class="form-group">
                <label for="quantity">الكمية</label>
                <input type="number" class="form-control" name="quantity[]" >
            </div>

            <div class="form-group ring-size" style="display: none">
                <label for="ring_size">مقاس الخاتم</label>
                <input type="number" class="form-control" name="ring_size[]" disabled>
            </div>

            <div class="form-group">
                <label for="gold_color">اللون</label>
                <select name="gold_color[]" class="form-control" >
                    @foreach ($gold_colors as $gold_color)
                        <option value="{{ $gold_color }}">{{ $gold_color }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <input type="checkbox" class="toggleLabel"> عينة
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
        <button style="margin: 20px 200px" type="submit" class="btn btn-primary">Submit Order</button>
    </div>
</form>
<script src="{{ asset('js/order_details.js') }}"></script>
</body>
</html>
