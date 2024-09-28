<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Gold Item</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ url('css/style.css') }}" rel="stylesheet">
</head>
<body>
    <div class="item-details">
        <form class="custom-form" action="{{ route('gold-items.update', $goldItem->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <label for="serial_number">Serial Number:</label>
            <input type="text" name="serial_number" id="serial_number" value="{{ $goldItem->serial_number }}" readonly><br>

            <label for="shop_name">Shop Name:</label>
            <input type="text" name="shop_name" id="shop_name" value="{{ $goldItem->shop_name }}" readonly><br>

            <label for="shop_id">Shop ID:</label>
            <input type="number" name="shop_id" id="shop_id" value="{{ $goldItem->shop_id }}" readonly><br>

            <label for="kind">Kind:</label>
            <input type="text" name="kind" id="kind" value="{{ $goldItem->kind }}" readonly><br>

            <label for="model">Model:</label>
            <input type="text" name="model" id="model" value="{{ $goldItem->model }}" readonly><br>

            <label for="talab">Talab:</label>
            <input type="text" name="talab" id="talab" value="{{ $goldItem->talab }}" readonly><br>

            <label for="gold_color">Gold Color:</label>
            <input type="text" name="gold_color" id="gold_color" value="{{ $goldItem->gold_color }}" readonly><br>

            <label for="stones">Stones:</label>
            <input type="text" name="stones" id="stones" value="{{ $goldItem->stones }}" readonly><br>

            <label for="metal_type">Metal Type:</label>
            <input type="text" name="metal_type" id="metal_type" value="{{ $goldItem->metal_type }}" readonly><br>

            <label for="metal_purity">Metal Purity:</label>
            <input type="text" name="metal_purity" id="metal_purity" value="{{ $goldItem->metal_purity }}" readonly><br>

            <label for="quantity">Quantity:</label>
            <input type="number" name="quantity" id="quantity" value="{{ $goldItem->quantity }}" readonly><br>

            <label for="weight">Weight:</label>
            <input type="number" name="weight" step="0.01" id="weight" value="{{ $goldItem->weight }}" readonly><br>

            <label for="rest_since">Rest Since:</label>
            <input type="date" name="rest_since" id="rest_since" value="{{ $goldItem->rest_since }}" readonly><br>

            <label for="source">Source:</label>
            <input type="text" name="source" id="source" value="{{ $goldItem->source }}" readonly><br>

            <label for="to_print">To Print:</label>
            <input type="checkbox" name="to_print" id="to_print" value="1" {{ $goldItem->to_print ? 'checked' : '' }} disabled><br>

            <label for="price">Price:</label>
            <input type="number" name="price" step="0.01" id="price" value="{{ $goldItem->price }}" readonly><br>

            <label for="semi_or_no">Semi or no:</label>
            <input type="text" name="semi_or_no" id="semi_or_no" value="{{ $goldItem->semi_or_no }}" readonly><br>

            <label for="average_of_stones">Average of Stones:</label>
            <input type="number" name="average_of_stones" step="0.01" id="average_of_stones" value="{{ $goldItem->average_of_stones }}" readonly><br>

            <label for="net_weight">Net Weight:</label>
            <input type="number" name="net_weight" step="0.01" id="net_weight" value="{{ $goldItem->net_weight }}" readonly><br>

            <label for="link">Upload Image:</label>
            <input type="file" name="link" id="link" accept="image/*"><br>
        </form>

    </div>

    <div class="customer-details">
        <h2>Enter Customer Details</h2>
        <form action="{{ route('gold-items.markAsSold', $goldItem->id) }}" method="POST">
            @csrf

            <label for="first_name">First Name:</label>
            <input type="text" name="first_name" id="first_name" required><br>

            <label for="last_name">Last Name:</label>
            <input type="text" name="last_name" id="last_name" required><br>
            
            <label for="phone_number">Phone Number:</label>
            <input type="number" name="phone_number" id="phone_number" required><br>

            <label for="address">Address:</label>
            <input type="text" name="address" id="address" required><br>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required><br>

            <label for="payment_method">Payment Method:</label>
            <input type="text" name="payment_method" id="payment_method" required><br>

            <button type="submit">Save Customer</button>
        </form> 
    </div>
</body>
</html>
