{{-- @include('layouts.app') --}}
<!DOCTYPE html>
<html>

<head>
    <style>
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 10px;
        }

        .product-item {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 8px;
            background-color: #ffffff;
            font-size: 1em;
            max-height: 350px;
            overflow-y: auto;
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

        .admin-controls {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
            border: 1px solid #e3e6f0;
        }

        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }

        .btn-warning:hover {
            background-color: #e0a800;
            color: #212529;
        }

        .flash-message {
            padding: 10px 15px;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .flash-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .flash-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .flash-warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }

        .control-group {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .control-group h4 {
            margin-top: 0;
            margin-bottom: 10px;
        }

        .control-description {
            margin-bottom: 10px;
            font-size: 0.9em;
            color: #6c757d;
        }
    </style>
</head>

<body>

    <h1>Shopify Products with Media</h1>

    <!-- Admin Control Panel -->
    <div class="admin-controls">
        <h3>Inventory Controls</h3>

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="flash-message flash-success">
                {!! session('success') !!}
            </div>
        @endif

        @if (session('error'))
            <div class="flash-message flash-error">
                {!! session('error') !!}
            </div>
        @endif

        @if (session('warning'))
            <div class="flash-message flash-warning">
                {!! session('warning') !!}
            </div>
        @endif

        <!-- G-SKU Inventory Update Controls -->
        <!-- Add this to your admin controls section in the products view -->

        <div class="control-group">
            <h4>Zero Inventory Management</h4>
            <p class="control-description">
                Update inventory for ALL products with zero inventory at the Cairo location. This will prevent any
                products
                from showing as "Sold Out" on your store.
            </p>

            <a href="{{ route('shopify.updateAllZeroInventory') }}" class="btn btn-warning">
                Update ALL Zero-Inventory Products to 1
            </a>
            <p><small>This processes your entire catalog but only updates products with 0 inventory. Products with
                    inventory > 0 will be skipped.</small></p>
        </div>
        {{-- import data is 0 in inventory --}}

        <div class="control-group">
            <h4>Set Specific SKUs to Zero Inventory</h4>
            <p class="control-description">
                Upload an Excel file with SKUs (in column A) to set those products to zero inventory across all
                locations.
                All variants of matching products will be updated.
            </p>

            <form action="{{ route('shopify.importSkusSetZero') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div style="margin-bottom: 10px;">
                    <input type="file" name="excel_file" required accept=".xlsx,.xls,.csv">
                </div>
                <button type="submit" class="btn btn-danger">
                    Import SKUs & Set to Zero Inventory (All Locations)
                </button>
            </form>
            <p><small>
                    Format: Excel file with SKUs in column A. All variants of matching products will be set to zero
                    inventory across all locations.
                    This will make products show as "Sold Out" on your store.
                </small></p>
        </div>
        <!-- Excel Upload Form -->
        <div class="control-group">
            <h4>Update From Excel</h4>
            <p class="control-description">
                Bulk update product data by uploading an Excel file with SKUs, prices, and weights.
            </p>

            <form action="{{ route('shopify.updateFromExcel') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="excel_file" required>
                <button type="submit" class="btn">Upload Excel</button>
            </form>
        </div>
    </div>

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
