{{-- @include('layouts.app') --}}
<!DOCTYPE html>
<html>

<head>
    <style>
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            /* Reduced min-width for more items */
            gap: 10px;
            /* Reduced gap for more compact layout */
        }

        .product-item {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 8px;
            background-color: #ffffff;
            font-size: 1em;
            max-height: 350px;
            overflow-y: auto;
            /* Enable vertical scrolling */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: box-shadow 0.3s ease;
        }

        .product-item:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .product-images img {
            display: block;
            margin: 0 auto 5px;
            /* Reduced margin */
            max-width: 100%;
        }

        .btn {
            display: inline-block;
            padding: 8px 12px;
            margin-top: 10px;
            font-size: 0.9em;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>

    <h1>Shopify Products with Media</h1>
    <div class="product-grid">
        @if (count($products) > 0)
            @foreach ($products as $product)
                <div class="product-item">
                    <p><strong>Model:</strong>
                        {{ $product['node']['variants']['edges'][0]['node']['sku'] ?? 'No SKU Available' }}</p>
                    <a href="{{ route('shopify.products.showEditImageForm', ['product_id' => basename($product['node']['id'])]) }}"
                        class="btn">Edit Product</a>
                    {{-- Display Product Images --}}
                    @if (!empty($product['node']['media']['edges']))
                        <div class="product-images">
                            @foreach ($product['node']['media']['edges'] as $media)
                                @if ($media['node']['mediaContentType'] === 'IMAGE')
                                    <img src="{{ $media['node']['image']['url'] }}"
                                        alt="{{ $media['node']['image']['altText'] ?? 'No Alt Text' }}" width="150">
                                @endif
                            @endforeach
                        </div>
                    @else
                        <p>No images available</p>
                    @endif
                    <p><strong>Product Type:</strong>
                        {{ $product['node']['productType'] ?? 'No Product Type Available' }}</p>

                    {{-- Truncate product description initially and show 'See More' --}}
                    <div class="product-description" id="description-{{ $loop->index }}">
                        <p class="short-description">
                            {{ Str::limit($product['node']['description']) }} {{-- Limit to 300 characters initially --}}
                        </p>
                        <p class="full-description" style="display:none;">
                            {{ $product['node']['description'] ?? 'No Description Available' }}
                        </p>
                        @if (strlen($product['node']['description']) > 300)
                            <a href="javascript:void(0);" class="see-more" data-id="{{ $loop->index }}">See More</a>
                        @endif
                    </div>



                    {{-- Display Variants, Prices, and Inventory Quantities --}}
                    <ul>
                        @if (!empty($product['node']['variants']['edges']))
                            @foreach ($product['node']['variants']['edges'] as $variant)
                                <li>
                                    <strong>Gold Color:</strong>
                                    {{ $variant['node']['title'] ?? 'No Variant Title' }}<br>
                                    <strong>Price:</strong> {{ $variant['node']['price'] ?? '0.00' }}<br>
                                    <strong>Available:</strong>
                                    {{ $variant['node']['inventoryQuantity'] ?? 'Not Available' }}
                                </li>
                            @endforeach
                        @else
                            <p>No variants available</p>
                        @endif
                    </ul>
                </div>
            @endforeach
        @else
            <p>No products available</p>
        @endif
    </div>
    @if ($hasNextPage)
        <a href="{{ route('shopify.products', ['cursor' => $nextCursor]) }}" class="btn btn-primary">Next Page</a>
    @endif
    {{-- <form action="{{ route('shopify.updatePrices') }}" method="POST">
        @csrf --}}
    {{-- @foreach ($products as $product)
            <div>
                <label for="price_{{ $product['node']['variants']['edges'][0]['node']['id'] }}">
                    {{ $product['node']['title'] }}
                </label>
                <input 
                    type="number" 
                    name="prices[{{ $product['node']['variants']['edges'][0]['node']['id'] }}]" 
                    value="{{ $product['node']['variants']['edges'][0]['node']['price'] }}" 
                    step="0.01" 
                    min="0"
                >
            </div>
        @endforeach --}}
    {{-- <button type="submit">Update Prices</button> --}}
    {{-- </form> --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const seeMoreLinks = document.querySelectorAll('.see-more');
            seeMoreLinks.forEach(link => {
                link.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const shortDescription = document.querySelector(
                        `#description-${id} .short-description`);
                    const fullDescription = document.querySelector(
                        `#description-${id} .full-description`);

                    // Hide the short description and show the full description
                    shortDescription.style.display = 'none';
                    fullDescription.style.display = 'block';

                    // Hide the 'See More' link
                    this.style.display = 'none';
                });
            });
        });
    </script>
</body>

</html>
