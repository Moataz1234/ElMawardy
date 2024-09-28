<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Sold Gold Item</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ url('css/style.css') }}" rel="stylesheet">
</head>
<body>
    <form class="custom-form" action="{{ route('gold-items-sold.update', $goldItemSold->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <label for="serial_number">Serial Number:</label>
        <input type="text" name="serial_number" id="serial_number" value="{{ $goldItemSold->serial_number }}"><br>

        <label for="shop_name">Shop Name:</label>
        <input type="text" name="shop_name" id="shop_name" value="{{ $goldItemSold->shop_name }}"><br>

        <label for="shop_id">Shop ID:</label>
        <input type="number" name="shop_id" id="shop_id" value="{{ $goldItemSold->shop_id }}"><br>

        <label for="kind">Kind:</label>
        <input type="text" name="kind" id="kind" value="{{ $goldItemSold->kind }}"><br>

        <label for="model">Model:</label>
        <input type="text" name="model" id="model" value="{{ $goldItemSold->model }}"><br>

        <label for="talab">Talab:</label>
        <input type="text" name="talab" id="talab" value="{{ $goldItemSold->talab }}"><br>

        <label for="gold_color">Gold Color:</label>
        <input type="text" name="gold_color" id="gold_color" value="{{ $goldItemSold->gold_color }}"><br>

        <label for="stones">Stones:</label>
        <input type="text" name="stones" id="stones" value="{{ $goldItemSold->stones }}"><br>

        <label for="metal_type">Metal Type:</label>
        <input type="text" name="metal_type" id="metal_type" value="{{ $goldItemSold->metal_type }}"><br>

        <label for="metal_purity">Metal Purity:</label>
        <input type="text" name="metal_purity" id="metal_purity" value="{{ $goldItemSold->metal_purity }}"><br>

        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity" id="quantity" value="{{ $goldItemSold->quantity }}"><br>

        <label for="weight">Weight:</label>
        <input type="number" name="weight" step="0.01" id="weight" value="{{ $goldItemSold->weight }}"><br>

        <label for="add_date">Add Date:</label>
        <input type="date" name="add_date" id="add_date" value="{{ $goldItemSold->add_date }}"><br>

        <label for="source">Source:</label>
        <input type="text" name="source" id="source" value="{{ $goldItemSold->source }}"><br>

        <label for="to_print">To Print:</label>
        <input type="checkbox" name="to_print" id="to_print" value="1" {{ $goldItemSold->to_print ? 'checked' : '' }}><br>

        <label for="price">Price:</label>
        <input type="number" name="price" step="0.01" id="price" value="{{ $goldItemSold->price }}"><br>

        <label for="semi_or_no">Semi or no:</label>
        <input type="text" name="semi_or_no" id="semi_or_no" value="{{ $goldItemSold->semi_or_no }}"><br>

        <label for="average_of_stones">Average of Stones:</label>
        <input type="number" name="average_of_stones" step="0.01" id="average_of_stones" value="{{ $goldItemSold->average_of_stones }}"><br>

        <label for="net_weight">Net Weight:</label>
        <input type="number" name="net_weight" step="0.01" id="net_weight" value="{{ $goldItemSold->net_weight }}"><br>

        <label for="sold_date">Sold Date:</label>
        <input type="date" name="sold_date" id="sold_date" value="{{ $goldItemSold->sold_date }}"><br>

        <button type="submit">Update</button>
        
    
    </form>
    <form action="{{ route('gold-items-sold.markAsRest', $goldItemSold->id) }}" method="POST" style="display:inline;">
        @csrf
        <button class="rest_button" type="submit">Rest</button>
    </form> 
</body>
</html>
