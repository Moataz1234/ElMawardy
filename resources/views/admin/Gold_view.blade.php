<form action="{{ route('gold-items.store') }}" method="POST">
    @csrf

    <label for="link">Link:</label>
    <input type="text" name="link" id="link" required><br>

    <label for="serial_number">Serial Number:</label>
    <input type="text" name="serial_number" id="serial_number" required><br>

    <label for="shop_name">Shop Name:</label>
    <input type="text" name="shop_name" id="shop_name" required><br>

    <label for="shop_id">Shop ID:</label>
    <input type="number" name="shop_id" id="shop_id" required><br>

    <label for="kind">Kind:</label>
    <input type="text" name="kind" id="kind" required><br>

    <label for="model">Model:</label>
    <input type="text" name="model" id="model" required><br>

    <label for="talab">Talab:</label>
    <input type="text" name="talab" id="talab" required><br>

    <label for="gold_color">Gold Color:</label>
    <input type="text" name="gold_color" id="gold_color" required><br>

    <label for="stones">Stones:</label>
    <input type="text" name="stones" id="stones"><br>

    <label for="metal_type">Metal Type:</label>
    <input type="text" name="metal_type" id="metal_type" required><br>

    <label for="metal_purity">Metal Purity:</label>
    <input type="text" name="metal_purity" id="metal_purity" required><br>

    <label for="quantity">Quantity:</label>
    <input type="number" name="quantity" id="quantity" required><br>

    <label for="weight">Weight:</label>
    <input type="number" name="weight" step="0.01" id="weight" required><br>

    <label for="rest_since">Rest Since:</label>
    <input type="date" name="rest_since" id="rest_since" required><br>

    <label for="source">Source:</label>
    <input type="text" name="source" id="source" required><br>

    <label for="to_print">To Print:</label>
    <input type="checkbox" name="to_print" id="to_print" value="1"><br>

    <label for="price">Price:</label>
    <input type="number" name="price" step="0.01" id="price" required><br>

    <label for="semi_or_no">Semi or no:</label>
    <input type="text" name="semi_or_no" id="semi_or_no" required><br>

    <label for="average_of_stones">Average of Stones:</label>
    <input type="number" name="average_of_stones" step="0.01" id="average_of_stones"><br>

    <label for="net_weight">Net Weight:</label>
    <input type="number" name="net_weight" step="0.01" id="net_weight" required><br>

    <button type="submit">Submit</button>
</form>
