<!DOCTYPE html>
<html lang="en">
<head>
    @include('components.navbar')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Gold Item</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding-bottom: 40px;
        }
        .form-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            padding: 25px;
            margin-top: 20px;
        }
        .form-title {
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 10px;
            margin-bottom: 25px;
            color: #0d6efd;
        }
        .form-section {
            background-color: #f8f9fa;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: #495057;
        }
        .form-label {
            font-weight: 500;
            margin-bottom: 5px;
        }
        .form-control, .form-select {
            border-radius: 4px;
            border: 1px solid #ced4da;
            padding: 8px 12px;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        .form-control:focus, .form-select:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
            padding: 8px 20px;
            font-weight: 500;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
        }
        .form-check-input {
            width: 1.2em;
            height: 1.2em;
            margin-top: 0.25em;
        }
    </style>
</head>
<body>
    <div class="container form-container">
        <h2 class="form-title">
            <i class="fas fa-edit me-2"></i>Edit Gold Item - {{ $goldItem->serial_number }}
        </h2>
        
        <form action="{{ route('gold-items.update', $goldItem->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-section">
                <div class="row">
                    <!-- First Row -->
                    <div class="col-md-4 mb-3">
                        <label for="serial_number" class="form-label">Serial Number:</label>
                        <input type="text" class="form-control" name="serial_number" id="serial_number" value="{{ $goldItem->serial_number }}" readonly>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="shop_name" class="form-label">Shop Name:</label>
                        <select name="shop_name" id="shop_name" class="form-select">
                            @foreach($shopNames as $shopNameOption)
                                @if($shopNameOption != 'France' && $shopNameOption != 'Damgha')
                                    <option value="{{ $shopNameOption }}" {{ $goldItem->shop_name == $shopNameOption ? 'selected' : '' }}>
                                        {{ $shopNameOption }}
                                    </option>
                                @endif
                            @endforeach
                            <option value="other" {{ !$shopNames->contains($goldItem->shop_name) || $goldItem->shop_name == 'France' || $goldItem->shop_name == 'Damgha' ? 'selected' : '' }}>Other</option>
                        </select>
                        <input type="text" id="custom-shop" name="custom_shop" class="form-control mt-2" 
                            style="display: {{ !$shopNames->contains($goldItem->shop_name) || $goldItem->shop_name == 'France' || $goldItem->shop_name == 'Damgha' ? 'block' : 'none' }};" 
                            value="{{ !$shopNames->contains($goldItem->shop_name) || $goldItem->shop_name == 'France' || $goldItem->shop_name == 'Damgha' ? $goldItem->shop_name : '' }}" 
                            placeholder="Enter custom shop name">
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="shop_id" class="form-label">Shop ID:</label>
                        <input type="number" class="form-control" name="shop_id" id="shop_id" value="{{ $goldItem->shop_id }}">
                    </div>
                    
                    <!-- Second Row -->
                    <div class="col-md-4 mb-3">
                        <label for="kind" class="form-label">Kind:</label>
                        <select name="kind" id="kind" class="form-select">
                            @foreach($kinds as $kindOption)
                                <option value="{{ $kindOption }}" {{ $goldItem->kind == $kindOption ? 'selected' : '' }}>
                                    {{ $kindOption }}
                                </option>
                            @endforeach
                            <option value="other" {{ !$kinds->contains($goldItem->kind) ? 'selected' : '' }}>Other</option>
                        </select>
                        <input type="text" id="custom-kind" name="custom_kind" class="form-control mt-2" 
                            style="display: {{ !$kinds->contains($goldItem->kind) ? 'block' : 'none' }};" 
                            value="{{ !$kinds->contains($goldItem->kind) ? $goldItem->kind : '' }}" 
                            placeholder="Enter custom kind">
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="model" class="form-label">Model:</label>
                        <input type="text" class="form-control" name="model" id="model" value="{{ $goldItem->model }}">
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="source" class="form-label">Source:</label>
                        <input type="text" class="form-control" name="source" id="source" value="{{ $goldItem->source ?? '' }}">
                    </div>
                    
                    <!-- Third Row -->
                    <div class="col-md-3 mb-3">
                        <label for="gold_color" class="form-label">Gold Color:</label>
                        <input type="text" class="form-control" name="gold_color" id="gold_color" value="{{ $goldItem->gold_color }}">
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="stones" class="form-label">Stones:</label>
                        <input type="text" class="form-control" name="stones" id="stones" value="{{ $goldItem->stones }}">
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="metal_type" class="form-label">Metal Type:</label>
                        <input type="text" class="form-control" name="metal_type" id="metal_type" value="{{ $goldItem->metal_type }}">
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="metal_purity" class="form-label">Metal Purity:</label>
                        <input type="text" class="form-control" name="metal_purity" id="metal_purity" value="{{ $goldItem->metal_purity }}">
                    </div>
                    
                    <!-- Fourth Row -->
                    <div class="col-md-3 mb-3">
                        <label for="quantity" class="form-label">Quantity:</label>
                        <input type="number" class="form-control" name="quantity" id="quantity" value="{{ $goldItem->quantity }}">
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="weight" class="form-label">Weight:</label>
                        <input type="number" class="form-control" name="weight" step="0.01" id="weight" value="{{ $goldItem->weight }}">
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="rest_since" class="form-label">Rest Since:</label>
                        <input type="date" class="form-control" name="rest_since" id="rest_since" value="{{ $goldItem->rest_since }}">
                    </div>
                    
                    <div class="col-md-3 mb-3 d-flex align-items-end">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="talab" id="talab" value="1" {{ $goldItem->talab ? 'checked' : '' }}>
                            <label class="form-check-label" for="talab">Talab</label>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="row mt-4">
                <div class="col-12 d-flex justify-content-between">
                    <a href="{{ route('admin.inventory') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Inventory
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Item
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Kind dropdown handling
            const kindSelect = document.getElementById('kind');
            const customKindInput = document.getElementById('custom-kind');
            
            kindSelect.addEventListener('change', function() {
                if (this.value === 'other') {
                    customKindInput.style.display = 'block';
                    customKindInput.focus();
                } else {
                    customKindInput.style.display = 'none';
                    customKindInput.value = '';
                }
            });
            
            // Shop name dropdown handling
            const shopSelect = document.getElementById('shop_name');
            const customShopInput = document.getElementById('custom-shop');
            
            shopSelect.addEventListener('change', function() {
                if (this.value === 'other') {
                    customShopInput.style.display = 'block';
                    customShopInput.focus();
                } else {
                    customShopInput.style.display = 'none';
                    customShopInput.value = '';
                }
            });
            
            // Form submission handling
            document.querySelector('form').addEventListener('submit', function(e) {
                // Validate kind field
                if (kindSelect.value === 'other' && customKindInput.value.trim() === '') {
                    e.preventDefault();
                    alert('Please enter a custom kind value');
                    customKindInput.focus();
                    return;
                }
                
                // Validate shop name field
                if (shopSelect.value === 'other' && customShopInput.value.trim() === '') {
                    e.preventDefault();
                    alert('Please enter a custom shop name');
                    customShopInput.focus();
                    return;
                }
                
                // Handle custom kind
                if (kindSelect.value === 'other') {
                    const hiddenKindInput = document.createElement('input');
                    hiddenKindInput.type = 'hidden';
                    hiddenKindInput.name = 'kind';
                    hiddenKindInput.value = customKindInput.value.trim();
                    this.appendChild(hiddenKindInput);
                }
                
                // Handle custom shop name
                if (shopSelect.value === 'other') {
                    const hiddenShopInput = document.createElement('input');
                    hiddenShopInput.type = 'hidden';
                    hiddenShopInput.name = 'shop_name';
                    hiddenShopInput.value = customShopInput.value.trim();
                    this.appendChild(hiddenShopInput);
                }
            });
        });
    </script>
</body>
</html>
