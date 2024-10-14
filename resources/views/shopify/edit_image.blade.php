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
    {{-- edit_product.blade.php --}}
<form action="{{ route('shopify.products.editProduct', ['product_id' => $product['id']]) }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="product_id" value="{{ $product['id'] }}">

    <label for="title">Title:</label>
    <input type="text" name="title" value="{{ old('title', $product['title']) }}" required>

    <label for="description">Description:</label>
    <textarea name="description">{{ old('description', $product['description']) }}</textarea>

    <label for="vendor">Vendor:</label>
    <input type="text" name="vendor" value="{{ old('vendor', $product['vendor']) }}" required>

    <label for="product_type">Product Type:</label>
    <input type="text" name="product_type" value="{{ old('product_type', $product['productType']) }}">

    <label for="tags">Tags:</label>
    <input type="text" name="tags" value="{{ old('tags', is_array($product['tags']) ? implode(', ', $product['tags']) : $product['tags']) }}">

    <label for="new_image">New Image:</label>
    <input type="file" name="new_image">

    {{-- Display current images from Shopify --}}
    @if (!empty($product['media']['edges']))
        <div class="product-images">
            @foreach ($product['media']['edges'] as $media)
                @if ($media['node']['mediaContentType'] === 'IMAGE')
                    <img src="{{ $media['node']['image']['url'] }}" alt="{{ $media['node']['image']['altText'] ?? 'No Alt Text' }}" width="150">
                @endif
            @endforeach
        </div>
    @else
        <p>No images available</p>
    @endif

    <button type="submit" class="btn btn-primary">Save Changes</button>
</form>
    
    
</body>
</html>
