<!DOCTYPE html>
<html>
<head>
    <title>Edit Image</title>
</head>
<body>
    <h1>Edit Image for Product</h1>
    <form action="{{ route('shopify.products.editImage') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="product_id" value="{{ $productId }}">
        <input type="hidden" name="image_id" value="{{ $imageId }}">
        
        <label for="new_image">Upload New Image:</label>
        <input type="file" id="new_image" name="new_image" accept="image/*">

        <label for="title">Title:</label>
        <input type="text" id="title" name="title" >

        <label for="description">Description:</label>
        <textarea id="description" name="description" ></textarea>

        <label for="product_type">Product Type:</label>
        <input type="text" id="product_type" name="product_type" >

        <button type="submit">Update Product</button>
    </form>
</body>
</html>
