<!DOCTYPE html>
<html lang="en">
<head>
    {{-- @include('dashboard') --}}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Gold Item</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ url('css/style.css') }}" rel="stylesheet">
</head>
<body>
    <div class="item-details">
        <form action="{{ route('shop-items.bulkSell') }}" method="POST">
            @csrf
            @foreach($goldItems as $item)
            <div>

                <h3>Item {{ $item->serial_number }}</h3>

            <input type="hidden" name="ids[]" value="{{ $item->id }}">

            <p>Shop Name: {{ $item->shop_name }}</p>

            <p>Shop ID: {{ $item->shop_id }}</p>

            <p>Kind: {{ $item->kind }}</p>

            <p>Model: {{ $item->model }}</p>

            <p>Gold Color: {{ $item->gold_color }}</p>

            <p>Weight: {{ $item->weight }}</p>

            {{-- <label for="price">Price:</label>
            <input type="number" name="price" step="0.01" id="price" value="{{ $goldItem->price }}" readonly><br> --}}

    </div>
    @endforeach
    </div>

    <div class="customer-details">
        <h2 style="color: rgb(171, 245, 0)">Enter Customer Details</h2>
        {{-- <form class="custom-form" action="{{ route('gold-items.markAsSold', $goldItem->id) }}" method="POST" enctype="multipart/form-data"> --}}
            {{-- @csrf --}}

            <label for="first_name">الاسم الاول</label>
            <input type="text" name="first_name" id="first_name" required><br>

            <label for="last_name">الاسم الاخير</label>
            <input type="text" name="last_name" id="last_name" required><br>
            
            <label for="phone_number">رقم التلفيون</label>
            <input type="number" name="phone_number" id="phone_number" required><br>

            <label for="address">العنوان</label>
            <input type="text" name="address" id="address" required><br>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required><br>

            <div class="form-group">
                <label for="payment_method" class="form-label">طريقة الدفع</label>
                <select class="form-control" name="payment_method" id="payment_method">
                    <option value="visa">Visa</option>
                    <option value="value">Value</option>
                    <option value="cash">Cash</option>
                    <option value="instapay">Instapay</option>
                </select>
            </div>  
            <label for="total_price">السعر</label>
            <input type="number" name="total_price" step="0.01" id="total_price"  ><br>
          
            <button type="submit">Complete Sale</button>
        </form> 
    </div>
</body>
<script>
    document.getElementById('price').addEventListener('input', function() {
        var weight = parseFloat(document.getElementById('weight').value);
        var price = parseFloat(this.value);
        var totalPrice = weight * price;
        document.getElementById('total_price').value = totalPrice.toFixed(2);
    });
</script>
</html>
