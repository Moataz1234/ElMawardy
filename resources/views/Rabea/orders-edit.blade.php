<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.rabea.dashboard')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Customer Order</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/pagination.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/order-details.css') }}" rel="stylesheet">
</head>
<body>
<form class="custom-form" action="{{ route('orders.update', $order->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT') <!-- Use PUT method for updating -->
    
    <!-- Customer Details -->
    <div class="mb-3">
        <label for="customer_name" class="form-label">اسم العميل</label>
        <input type="text" class="form-control" name="customer_name" id="customer_name" value="{{ $order->customer_name }}" required>
    </div>

    <div class="mb-3">
        <label for="customer_phone" class="form-label">تليفون العميل</label>
        <input type="number" class="form-control" name="customer_phone" id="customer_phone" maxlength="11" value="{{ $order->customer_phone }}" required>
    </div>

    <div class="mb-3">
        <label for="seller_name" class="form-label">البائع</label>
        <input type="text" class="form-control" name="seller_name" id="seller_name" value="{{ $order->seller_name }}" required>
    </div>

    <!-- Order Details -->
    <label for="order_details">موضوع الطلب :</label>
    <textarea style="height: 200px" name="order_details" id="order_details">{{ $order->order_details }}</textarea>

    <div class="mb-3">
        <label for="deposit" class="form-label">المدفوع</label>
        <input type="number" class="form-control" name="deposit" id="deposit" value="{{ $order->deposit }}" step="0.01">
    </div>

    <div class="mb-3">
        <label for="rest_of_cost" class="form-label">الباقي</label>
        <input type="number" class="form-control" name="rest_of_cost" id="rest_of_cost" value="{{ $order->rest_of_cost }}" step="0.01">
    </div>

    <div class="form-group">
        <label for="payment_method" class="form-label">طريقة الدفع</label>
        <select class="form-control" name="payment_method" id="payment_method">
            <option value="visa" {{ $order->payment_method == 'visa' ? 'selected' : '' }}>Visa</option>
            <option value="value" {{ $order->payment_method == 'value' ? 'selected' : '' }}>Value</option>
            <option value="cash" {{ $order->payment_method == 'cash' ? 'selected' : '' }}>Cash</option>
            <option value="instapay" {{ $order->payment_method == 'instapay' ? 'selected' : '' }}>Instapay</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="order_date" class="form-label">تاريخ الاستلام</label>
        <input type="date" class="form-control" name="order_date" id="order_date" value="{{ $order->order_date }}">
    </div>

    <!-- Order Items Section -->
    <h3 style="text-align: center;background-color:rgb(0, 255, 13)">Edit Order Items</h3>
    <div id="order-items">
@foreach ($order->items as $index => $item)
    <div class="order-item">
        <input type="hidden" name="order_item_id[]" value="{{ $item->id }}"> <!-- Hidden field for item ID -->
        
        <div class="form-group">
            <label for="order_kind_{{ $index }}">النوع</label>
            <select name="order_kind[]" class="form-control">
                @foreach ($kinds as $kind)
                    <option value="{{ $kind }}" {{ $item->order_kind == $kind ? 'selected' : '' }}>{{ $kind }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="order_fix_type_{{ $index }}">المشكلة</label>
            <select name="order_fix_type[]" class="form-control">
                <option value="اوردر جديد" {{ $item->order_fix_type == 'اوردر جديد' ? 'selected' : '' }}>اوردر جديد</option>
                <option value="تصليح" {{ $item->order_fix_type == 'تصليح' ? 'selected' : '' }}>تصليح</option>
                <option value="عمل مقاس" {{ $item->order_fix_type == 'عمل مقاس' ? 'selected' : '' }}>عمل مقاس</option>
                <option value="تلميع" {{ $item->order_fix_type == 'تلميع' ? 'selected' : '' }}>تلميع</option>
            </select>
        </div>

        <div class="form-group">
            <label for="quantity_{{ $index }}">الكمية</label>
            <input type="number" class="form-control" name="quantity[]" value="{{ $item->quantity }}">
        </div>

        <div class="form-group">
            <label for="gold_color_{{ $index }}">اللون</label>
            <select name="gold_color[]" class="form-control">
                @foreach ($gold_colors as $gold_color)
                    <option value="{{ $gold_color }}" {{ $item->gold_color == $gold_color ? 'selected' : '' }}>{{ $gold_color }}</option>
                @endforeach
            </select>
        </div>
    </div>
@endforeach

    </div>

    <button type="button" id="add-item" class="btn-custom">Add Item</button>

    <div class="form-group">
        <button style="margin: 20px 200px" type="submit" class="btn btn-primary">Update Order</button>
    </div>
</form>
<script src="{{ asset('js/order_details.js') }}"></script>
</body>
</html>
