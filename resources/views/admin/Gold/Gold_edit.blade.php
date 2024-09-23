<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Gold Item</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <form action="{{ route('gold-items.update', $goldItem->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <label for="link">Upload Image:</label>
        <input type="file" name="link" id="link" accept="image/*"><br>

        <label for="serial_number">Serial Number:</label>
        <input type="text" name="serial_number" id="serial_number" value="{{ $goldItem->serial_number }}"><br>

        <label for="shop_name">Shop Name:</label>
        <input type="text" name="shop_name" id="shop_name" value="{{ $goldItem->shop_name }}"><br>

        <label for="shop_id">Shop ID:</label>
        <input type="number" name="shop_id" id="shop_id" value="{{ $goldItem->shop_id }}"><br>

        <label for="kind">Kind:</label>
        <input type="text" name="kind" id="kind" value="{{ $goldItem->kind }}"><br>

        <label for="model">Model:</label>
        <input type="text" name="model" id="model" value="{{ $goldItem->model }}"><br>

        <label for="talab">Talab:</label>
        <input type="text" name="talab" id="talab" value="{{ $goldItem->talab }}"><br>

        <label for="gold_color">Gold Color:</label>
        <input type="text" name="gold_color" id="gold_color" value="{{ $goldItem->gold_color }}"><br>

        <label for="stones">Stones:</label>
        <input type="text" name="stones" id="stones" value="{{ $goldItem->stones }}"><br>

        <label for="metal_type">Metal Type:</label>
        <input type="text" name="metal_type" id="metal_type" value="{{ $goldItem->metal_type }}"><br>

        <label for="metal_purity">Metal Purity:</label>
        <input type="text" name="metal_purity" id="metal_purity" value="{{ $goldItem->metal_purity }}"><br>

        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity" id="quantity" value="{{ $goldItem->quantity }}"><br>

        <label for="weight">Weight:</label>
        <input type="number" name="weight" step="0.01" id="weight" value="{{ $goldItem->weight }}"><br>

        <label for="rest_since">Rest Since:</label>
        <input type="date" name="rest_since" id="rest_since" value="{{ $goldItem->rest_since }}"><br>

        <label for="source">Source:</label>
        <input type="text" name="source" id="source" value="{{ $goldItem->source }}"><br>

        <label for="to_print">To Print:</label>
        <input type="checkbox" name="to_print" id="to_print" value="1" {{ $goldItem->to_print ? 'checked' : '' }}><br>

        <label for="price">Price:</label>
        <input type="number" name="price" step="0.01" id="price" value="{{ $goldItem->price }}"><br>

        <label for="semi_or_no">Semi or no:</label>
        <input type="text" name="semi_or_no" id="semi_or_no" value="{{ $goldItem->semi_or_no }}"><br>

        <label for="average_of_stones">Average of Stones:</label>
        <input type="number" name="average_of_stones" step="0.01" id="average_of_stones" value="{{ $goldItem->average_of_stones }}"><br>

        <label for="net_weight">Net Weight:</label>
        <input type="number" name="net_weight" step="0.01" id="net_weight" value="{{ $goldItem->net_weight }}"><br>

        <button type="submit">Update</button>
    </form>
</body>
</html>
