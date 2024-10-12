<!DOCTYPE html>
<html>
<head>
    <title>Edit Image</title>
</head>
<body>
    <h1>Edit Image for Product</h1>
    <form action="{{ route('shopify.products.editImage') }}" method="POST">
        @csrf
        <input type="hidden" name="product_id" value="{{ $productId }}">
        <input type="hidden" name="image_id" value="{{ $imageId }}">
        
        <label for="new_image_url">New Image URL:</label>
        <input type="text" id="new_image_url" name="new_image_url" required>

        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>

        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea>

        <label for="vendor">Vendor:</label>
        <input type="text" id="vendor" name="vendor" required>

        <label for="product_type">Product Type:</label>
        <input type="text" id="product_type" name="product_type" required>

        <label for="tags">Tags (comma separated):</label>
        <input type="text" id="tags" name="tags" required>

        <button type="submit">Update Product</button>
    </form>
</body>
</html>
