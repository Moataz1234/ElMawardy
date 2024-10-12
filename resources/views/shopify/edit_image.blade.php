@extends('layouts.app')

@section('content')
    <h1>Shopify Products with Media</h1>
    <div class="product-grid">
        @if(count($products) > 0)
            @foreach ($products as $product)
                <div class="product-item">
                    {{-- Display Product Name (Title) --}}
                    <h2>{{ $product['node']['title'] ?? 'No Title Available' }}</h2>

                    {{-- Display Product SKU above Description --}}
                    <p><strong>SKU:</strong> {{ $product['node']['variants']['edges'][0]['node']['sku'] ?? 'No SKU Available' }}</p>

                    {{-- Truncate product description initially and show 'See More' --}}
                    <div class="product-description" id="description-{{ $loop->index }}">
                        <p class="short-description">
                            {{ Str::limit($product['node']['description'], 300) }} {{-- Limit to 300 characters initially --}}
                        </p>
                        <p class="full-description" style="display:none;">
                            {{ $product['node']['description'] ?? 'No Description Available' }}
                        </p>
                        @if(strlen($product['node']['description']) > 300)
                            <a href="javascript:void(0);" class="see-more" data-id="{{ $loop->index }}">See More</a>
                        @endif
                    </div>

                    {{-- Display Product Images --}}
                    @if (!empty($product['node']['media']['edges']))
                        <div class="product-images">
                            @foreach ($product['node']['media']['edges'] as $media)
                                @if ($media['node']['mediaContentType'] === 'IMAGE')
                                    <img src="{{ $media['node']['image']['url'] }}" 
                                         alt="{{ $media['node']['image']['altText'] ?? 'No Alt Text' }}" 
                                         width="150">
                                @endif
                            @endforeach
                        </div>
                    @else
                        <p>No images available</p>
                    @endif

                    {{-- Display Variants, Prices, and Available Inventory --}}
                    <ul>
                        @if (!empty($product['node']['variants']['edges']))
                            @foreach ($product['node']['variants']['edges'] as $variant)
                                <li>
                                    {{ $variant['node']['title'] ?? 'No Variant Title' }} - 
                                    ${{ $variant['node']['price'] ?? '0.00' }} 
                                    | Available Quantity: {{ $variant['node']['inventoryItem']['inventoryLevels']['edges'][0]['node']['availableQuantity'] ?? 'Not Available' }}
                                    | In Stock: {{ $variant['node']['availableForSale'] ? 'Yes' : 'No' }}
                                </li>
                            @endforeach
                        @else
                            <p>No variants available</p>
                        @endif
                    </ul>

                    {{-- Edit Button --}}
                    <button class="btn btn-warning edit-btn" data-id="{{ $product['node']['id'] }}" data-title="{{ $product['node']['title'] }}" data-description="{{ $product['node']['description'] }}">Edit</button>
                </div>
            @endforeach
        @else
            <p>No products available</p>
        @endif
    </div>

    {{-- Modal for Editing Product --}}
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <form id="editProductForm" action="{{ route('shopify.updateProduct', ['product' => '']) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT') {{-- Use PUT for updates --}}

                <input type="hidden" id="product_id" name="product_id">
                
                {{-- Product Title --}}
                <div class="form-group">
                    <label for="title">Product Title</label>
                    <input type="text" name="title" id="product_title" class="form-control">
                </div>

                {{-- Product Description --}}
                <div class="form-group">
                    <label for="description">Product Description</label>
                    <textarea name="description" id="product_description" class="form-control"></textarea>
                </div>

                {{-- Submit Button --}}
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editButtons = document.querySelectorAll('.edit-btn');
        const modal = document.getElementById('editModal');
        const closeModalBtn = document.querySelector('.close');
        const form = document.getElementById('editProductForm');
        const titleInput = document.getElementById('product_title');
        const descriptionInput = document.getElementById('product_description');
        const productIdInput = document.getElementById('product_id');

        // Open modal when edit button is clicked
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-id');
                const productTitle = this.getAttribute('data-title');
                const productDescription = this.getAttribute('data-description');

                titleInput.value = productTitle;
                descriptionInput.value = productDescription;
                productIdInput.value = productId;

                form.action = "{{ route('shopify.updateProduct', ['product' => '']) }}/" + productId;

                modal.style.display = 'block';
            });
        });

        // Close modal when "x" is clicked
        closeModalBtn.addEventListener('click', function() {
            modal.style.display = 'none';
        });

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        };
    });
</script>
@endsection
