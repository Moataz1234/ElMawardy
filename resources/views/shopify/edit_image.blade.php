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
        <button type="submit">Update Image</button>
    </form>
</body>
</html>
