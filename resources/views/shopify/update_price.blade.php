<form action="{{ route('shopify.updateFromExcel') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <label for="excel_file">Upload Excel File:</label>
    <input type="file" name="excel_file" required>
    <button type="submit">Update Products</button>
</form>

<!-- Gold Price Update Form -->
{{-- <form action="{{ route('admin.update-gold-prices') }}" method="POST">
    @csrf
    <label for="gold_price_per_gram">Gold Price Per Gram:</label>
    <input type="number" step="0.01" name="gold_price_per_gram" id="gold_price_per_gram" required>
    <button type="submit">Update Gold Prices</button>
</form> --}}

<form action="{{ route('shopify.updateGold') }}" method="POST">
    @csrf
    <label for="price_per_gram">Price per Gram:</label>
    <input type="number" name="price_per_gram" id="price_per_gram" step="0.01" required>
    <button type="submit">Update Prices</button>
</form>
<!-- Diamond Price Update Form -->
<form action="{{ route('admin.update-diamond-prices') }}" method="POST">
    @csrf
    <label for="diamond_price_per_carat">Diamond Price Per gram:</label>
    <input type="number" step="0.01" name="diamond_price_per_carat" id="diamond_price_per_carat" required>
    <button type="submit">Update Diamond Prices</button>
</form>