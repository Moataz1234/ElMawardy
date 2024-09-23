<head>
    <link href="{{ url('css/style.css') }}" rel="stylesheet">

</head>
<form class="custom-form" action="{{ route('gold-items.store') }}" method="POST">
    @csrf

    <label for="link">Upload Image:</label>
    <input type="file" name="link" id="link" accept="image/*"><br>

    <label for="serial_number">Serial Number:</label>
    <input type="text" name="serial_number" id="serial_number"><br>

    <label for="shop_name">Shop Name:</label>
    <input type="text" name="shop_name" id="shop_name"><br>

    <label for="shop_id">Shop ID:</label>
    <input type="number" name="shop_id" id="shop_id"><br>

    <label for="kind">Kind:</label>
    <input type="text" name="kind" id="kind"><br>

    <label for="model">Model:</label>
    <input type="text" name="model" id="model"><br>

    <label for="talab">Talab:</label>
    <input type="text" name="talab" id="talab"><br>

    <label for="gold_color">Gold Color:</label>
    <input type="text" name="gold_color" id="gold_color"><br>

    <label for="stones">Stones:</label>
    <input type="text" name="stones" id="stones"><br>

    <label for="metal_type">Metal Type:</label>
    <input type="text" name="metal_type" id="metal_type"><br>

    <label for="metal_purity">Metal Purity:</label>
    <input type="text" name="metal_purity" id="metal_purity"><br>

    <label for="quantity">Quantity:</label>
    <input type="number" name="quantity" id="quantity"><br>

    <label for="weight">Weight:</label>
    <input type="number" name="weight" step="0.01" id="weight"><br>

    <label for="rest_since">Rest Since:</label>
    <input type="date" name="rest_since" id="rest_since"><br>

    <label for="source">Source:</label>
    <input type="text" name="source" id="source"><br>

    <label for="to_print">To Print:</label>
    <input type="checkbox" name="to_print" id="to_print" value="1"><br>

    <label for="price">Price:</label>
    <input type="number" name="price" step="0.01" id="price"><br>

    <label for="semi_or_no">Semi or no:</label>
    <input type="text" name="semi_or_no" id="semi_or_no"><br>

    <label for="average_of_stones">Average of Stones:</label>
    <input type="number" name="average_of_stones" step="0.01" id="average_of_stones"><br>

    <label for="net_weight">Net Weight:</label>
    <input type="number" name="net_weight" step="0.01" id="net_weight"><br>

    <button type="submit">Submit</button>
</form>
