<!DOCTYPE html>
<html>
<head>
    <title>Edit Product Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        form {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }
        input[type="text"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            margin-top: 15px;
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Edit Product Details</h1>
    <form action="{{ route('shopify.updateProduct', ['product' => $productId]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <input type="hidden" name="product_id" value="{{ $productId }}">
        <input type="hidden" name="image_id" value="{{ $imageId }}">
        
        <label for="new_image">Upload New Image:</label>
        <input type="file" id="new_image" name="new_image" accept="image/*">

        <label for="title">Title:</label>
        <input type="text" id="title" name="title" placeholder="Enter product title">

        <label for="description">Description:</label>
        <textarea id="description" name="description" placeholder="Enter product description"></textarea>

        <label for="vendor">Vendor:</label>
        <input type="text" id="vendor" name="vendor" placeholder="Enter vendor name">

        <label for="product_type">Product Type:</label>
        <input type="text" id="product_type" name="product_type" placeholder="Enter product type">

        <label for="tags">Tags:</label>
        <input type="text" id="tags" name="tags" placeholder="Enter tags, separated by commas">

        <button type="submit">Update Product</button>
    </form>
</body>
</html>
