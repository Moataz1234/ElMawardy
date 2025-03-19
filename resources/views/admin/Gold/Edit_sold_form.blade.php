<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Sold Gold Item</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px 0;
        }
        .form-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 20px;
        }
        .page-title {
            color: #2c3e50;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #eee;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            font-weight: 600;
            color: #34495e;
            margin-bottom: 8px;
        }
        .form-control {
            border-radius: 5px;
            border: 1px solid #ddd;
            padding: 10px;
        }
        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.2rem rgba(52,152,219,0.25);
        }
        .btn-section {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        .btn-primary {
            background-color: #3498db;
            border: none;
            padding: 10px 25px;
        }
        .btn-secondary {
            background-color: #95a5a6;
            border: none;
            padding: 10px 25px;
        }
        .btn-warning {
            background-color: #f1c40f;
            border: none;
            padding: 10px 25px;
            color: #fff;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        @media (max-width: 992px) {
            .form-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2 class="page-title">Edit Sold Gold Item</h2>
            
            <form action="{{ route('gold-items-sold.update', $goldItemSold->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-grid">
                    <!-- Basic Information -->
                    <div class="form-group">
                        <label class="form-label" for="serial_number">Serial Number</label>
                        <input type="text" class="form-control" name="serial_number" value="{{ $goldItemSold->serial_number }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="shop_name">Shop Name</label>
                        <input type="text" class="form-control" name="shop_name" value="{{ $goldItemSold->shop_name }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="kind">Kind</label>
                        <input type="text" class="form-control" name="kind" value="{{ $goldItemSold->kind }}">
                    </div>

                    <!-- Product Details -->
                    <div class="form-group">
                        <label class="form-label" for="model">Model</label>
                        <input type="text" class="form-control" name="model" value="{{ $goldItemSold->model }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="gold_color">Gold Color</label>
                        <select class="form-control" name="gold_color">
                            <option value="Yellow" {{ $goldItemSold->gold_color == 'Yellow' ? 'selected' : '' }}>Yellow</option>
                            <option value="White" {{ $goldItemSold->gold_color == 'White' ? 'selected' : '' }}>White</option>
                            <option value="Rose" {{ $goldItemSold->gold_color == 'Rose' ? 'selected' : '' }}>Rose</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="stones">Stones</label>
                        <input type="text" class="form-control" name="stones" value="{{ $goldItemSold->stones }}">
                    </div>

                    <!-- Specifications -->
                    <div class="form-group">
                        <label class="form-label" for="metal_type">Metal Type</label>
                        <input type="text" class="form-control" name="metal_type" value="{{ $goldItemSold->metal_type }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="metal_purity">Metal Purity</label>
                        <input type="text" class="form-control" name="metal_purity" value="{{ $goldItemSold->metal_purity }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="quantity">Quantity</label>
                        <input type="number" class="form-control" name="quantity" value="{{ $goldItemSold->quantity }}" required>
                    </div>

                    <!-- Measurements -->
                    <div class="form-group">
                        <label class="form-label" for="weight">Weight (g)</label>
                        <input type="number" class="form-control" name="weight" step="0.01" value="{{ $goldItemSold->weight }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="price">Price</label>
                        <input type="number" class="form-control" name="price" step="0.01" value="{{ $goldItemSold->price }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="stars">Stars</label>
                        <input type="text" class="form-control" name="stars" value="{{ $goldItemSold->stars }}">
                    </div>

                    <!-- Additional Information -->
                    <div class="form-group">
                        <label class="form-label" for="source">Source</label>
                        <input type="text" class="form-control" name="source" value="{{ $goldItemSold->source }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="sold_date">Sold Date</label>
                        <input type="date" class="form-control" name="sold_date" value="{{ $goldItemSold->sold_date }}" required>
                    </div>
                </div>

                <div class="btn-section">
                    <button type="submit" class="btn btn-primary">Update Item</button>
                    <a href="{{ route('admin.sold-items') }}" class="btn btn-secondary">Cancel</a>
                    
                    {{-- <form action="{{ route('gold-items-sold.markAsRest', $goldItemSold->id) }}" method="POST" style="display: inline-block;">
                        @csrf
                        <button type="submit" class="btn btn-warning">Return to Stock</button>
                    </form> --}}
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
