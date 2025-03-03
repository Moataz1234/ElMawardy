<!DOCTYPE html>
<html lang="en">
<head>
    @include('components.navbar')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Gold Item</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/navbar.css') }}" rel="stylesheet">
    <link href="{{ asset('css/checkbox.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .create-form {
            background: rgb(133, 132, 132);
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 2rem;
            margin: 1rem;
            
        }

        .form-group {
            margin-bottom: 0.5rem;
        }

        .dynamic-field {
            display: flex;
            flex-wrap: nowrap;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .dynamic-field .form-group {
            flex: 1;
            min-width: 150px;
        }

        .form-control {
            padding: 0.375rem 0.5rem;
            font-size: 0.9rem;
        }

        .shop-weight-row .form-group {
            flex: 1;
        }

        .shop-weight-row .form-group:first-child {
            flex: 2;
        }

        .table-container {
            margin: 2rem 1rem;
        }

        #add-item-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        #add-item-btn:hover {
            background-color: #0056b3;
        }

        #submit-all-items {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        #submit-all-items:hover {
            background-color: #218838;
        }

        .submission-section {
            margin-top: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .model-details {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            margin: 1rem;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            display: grid;
            grid-template-rows: auto 1fr;
            gap: 1.5rem;
        }

        .model-image-container {
            text-align: center;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        .model-image-container h3 {
            margin-bottom: 1rem;
            color: #333;
            font-size: 1.2rem;
        }

        #model-image {
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #model-image img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        #shop-data-table-container {
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        #shop-data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        #shop-data-table th {
            background: #343a40;
            color: white;
            padding: 12px;
            font-weight: 500;
            text-align: left;
            border: none;
        }

        #shop-data-table td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
            background: white;
            vertical-align: top;
        }

        /* Column widths */
        #shop-data-table th:nth-child(1),
        #shop-data-table td:nth-child(1) {
            width: 25%;
        }

        #shop-data-table th:nth-child(2),
        #shop-data-table td:nth-child(2) {
            width: 15%;
        }

        #shop-data-table th:nth-child(3),
        #shop-data-table td:nth-child(3) {
            width: 45%;
            white-space: pre-line;
            line-height: 1.5;
            font-family: monospace;
            font-size: 0.9rem;
        }

        #shop-data-table th:nth-child(4),
        #shop-data-table td:nth-child(4) {
            width: 15%;
            text-align: center;
        }

        .pending-request td {
            background-color: #fff3cd !important;
        }

        /* Hover effects */
        #shop-data-table tbody tr:hover td {
            background-color: #f8f9fa;
        }

        /* Badge styling */
        .badge.bg-warning {
            background-color: #ffc107 !important;
            color: #000 !important;
            padding: 0.25em 0.6em;
            font-size: 0.75rem;
            font-weight: 500;
            border-radius: 4px;
        }

        /* Scrollbar styling */
        #shop-data-table-container::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        #shop-data-table-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        #shop-data-table-container::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        #shop-data-table-container::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Responsive adjustments */
        @media (max-width: 1200px) {
            .model-details {
                height: auto;
                min-height: 500px;
            }
        }

        .model-details img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .remove-item {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .remove-item:hover {
            background-color: #c82333;
        }

        .main-container {
            display: flex;
            gap: 1rem;
            margin: 1rem;
        }

        .form-section, .details-section {
            flex: 1;
            min-width: 0;
        }

        .rating-stars i {
            color: #ddd;
            cursor: pointer;
        }
        
        .rating-stars i.active {
            color: #ffd700;
        }
        
        .shop-input {
            width: 100%;
        }

        .form-row {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .form-row .form-group {
            flex: 1;
            min-width: 0; /* Prevents flex items from overflowing */
        }

        .form-control {
            padding: 0.375rem 0.5rem;
            font-size: 0.9rem;
            height: 38px; /* Make all inputs same height */
        }

        /* Adjust the shop-weight row specifically */
        .shop-weight-row {
            display: flex;
            gap: 1rem;
        }

        .shop-weight-row .form-group:first-child {
            flex: 2;
        }

        .shop-weight-row .form-group:last-child {
            flex: 1;
        }

        /* Style for pending requests */
        .pending-request {
            background-color: #fff3cd !important;
        }

        #shop-data-table td:nth-child(3) {
            /* Serial Numbers column */
            white-space: pre-line;  /* Allow line breaks */
            word-break: break-all;  
            vertical-align: top;    /* Align content to top */
            padding: 8px;          /* Add some padding */
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <h3 class="text-center ">Items</h3>
        
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="main-container">
            <!-- Form Section -->
            <div class="form-section">
                <form class="create-form" id="gold-item-form">
                    @csrf
                    <!-- First Row -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="model">Model:</label>
                            <input list="models" name="model" id="model" class="form-control" required>
                            <datalist id="models">
                                @foreach ($models as $model)
                                    <option value="{{ $model->model }}"></option>
                                @endforeach
                            </datalist>
                        </div>
                        <div class="form-group">
                            <label for="kind">Kind:</label>
                            <input type="text" name="kind" id="kind" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label for="metal_type">Metal Type:</label>
                            <select name="metal_type" id="metal_type" class="form-control" required>
                                <option value="Gold">Gold</option>
                                <option value="Platinium">Platinium</option>
                                <option value="Silver">Silver</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="metal_purity">Metal Purity:</label>
                            <select name="metal_purity" id="metal_purity" class="form-control" required>
                                @foreach ($metalPurities as $purity)
                                    <option value="{{ $purity }}">{{ $purity }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Second Row -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="quantity">Quantity:</label>
                            <input type="number" name="quantity" id="quantity" value="1" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="gold_color">Gold Color:</label>
                            <select name="shops[0][gold_color]" class="form-control" required>
                                @foreach ($goldColors as $color)
                                    <option value="{{ $color }}">{{ $color }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="rest_since">Rest Since:</label>
                            <input type="date" name="rest_since" id="rest_since" value="<?php echo date('Y-m-d'); ?>" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="talab">Talab:</label>
                            <div>
                                <input type="hidden" name="shops[0][talab]" value="0">
                                <input type="checkbox" class="checkboxInput" id="checkboxInput" name="shops[0][talab]" value="1">
                                <label for="checkboxInput" class="toggleSwitch"></label>
                            </div>
                        </div>
                    </div>

                    <!-- Third Row -->
                    <div class="form-row shop-weight-row">
                        <div class="form-group">
                            <label for="shop_id">Shop:</label>
                            <input list="shops-list" name="shops[0][shop_name]" class="form-control shop-input" required 
                                   placeholder="Select or type shop name" data-index="0">
                            <datalist id="shops-list">
                                @foreach ($shops as $shop)
                                    <option value="{{ $shop->name }} (ID: {{ $shop->id }})"></option>
                                @endforeach
                            </datalist>
                            <input type="hidden" name="shops[0][shop_id]" class="shop-id-input">
                        </div>
                        <div class="form-group">
                            <label for="weight">Weight:</label>
                            <input type="number" step="0.01" name="shops[0][weight]" class="form-control" required>
                        </div>
                    </div>

                    <button type="button" id="add-item-btn">Add to List</button>
                    <a class="reset-button btn btn-primary" href="{{ route('models.create') }}">Add New Model</a>
                </form>

                <!-- Session Items Table -->
                <div class="table-container">
                    <table id="items-table" class="table table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Model</th>
                                <th>Shop</th>
                                <th>Weight</th>
                                <th>Kind</th>
                                <th>Quantity</th>
                                <th>Stars</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (session('gold_items'))
                                @foreach (session('gold_items') as $item)
                                    <tr data-id="{{ $item['id'] }}">
                                        <td>{{ $item['model'] }}</td>
                                        <td>{{ $item['shops'][0]['shop_name'] ?? '' }}</td>
                                        <td>{{ $item['shops'][0]['weight'] }}</td>
                                        <td>{{ $item['kind'] }}</td>
                                        <td>{{ $item['quantity'] }}</td>
                                        <td>{{ $item['stars'] ?? '' }}</td>
                                        <td>
                                            <button class="remove-item btn" data-id="{{ $item['id'] }}">Remove</button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>

                    <div class="submission-section">
                        <p>Total Items: <span id="items-count">{{ count(session('gold_items') ?? []) }}</span></p>
                        <button id="submit-all-items">Submit All Items</button>
                    </div>
                </div>
            </div>

            <!-- Model Details Section -->
            <div class="details-section">
                <div class="model-details">
                    <div class="model-image-container">
                        <h3>Model Image</h3>
                        <div id="model-image"></div>
                    </div>
                    <div id="shop-data-table-container">
                        <table id="shop-data-table" class="table table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Shop & Color</th>
                                    <th>Total Weight</th>
                                    <th>Serial Numbers</th>
                                    <th>Count</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts (keeping all original functionality) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // All your original jQuery code remains unchanged
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '{{ session('error') }}'
                });
            @endif

            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: '{{ session('success') }}'
                });
            @endif

            // Add this function to extract shop ID from the selection
            function extractShopId(shopString) {
                const match = shopString.match(/\(ID: (\d+)\)/);
                return match ? match[1] : null;
            }

            // Add event listener for shop input changes
            $(document).on('input', '.shop-input', function() {
                const selectedValue = $(this).val();
                const shopId = extractShopId(selectedValue);
                $(this).siblings('.shop-id-input').val(shopId);
            });

            $('#add-item-btn').click(function(e) {
                e.preventDefault();
                
                // Validate shop selection before submission
                const shopInput = $('input[name="shops[0][shop_name]"]');
                const shopIdInput = $('input[name="shops[0][shop_id]"]');
                
                if (!shopIdInput.val()) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Please select a valid shop from the list'
                    });
                    return;
                }

                var formData = $('#gold-item-form').serialize();

                $.ajax({
                    url: '{{ route('gold-items.add-to-session') }}',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            var newRow = `
                                <tr data-id="${response.item.id}">
                                    <td>${response.item.model}</td>
                                    <td>${response.item.shops[0].shop_name}</td>
                                    <td>${response.item.shops[0].weight}</td>
                                    <td>${response.item.kind}</td>
                                    <td>${response.item.quantity}</td>
                                    <td>${response.item.stars || ''}</td>
                                    <td>
                                        <button class="remove-item" data-id="${response.item.id}">Remove</button>
                                    </td>
                                </tr>
                            `;
                            $('#items-table tbody').append(newRow);
                            $('#items-count').text(response.total_items);
                            
                            // Clear form fields
                            $('input[name="shops[0][shop_name]"]').val('');
                            $('input[name="shops[0][shop_id]"]').val('');
                            $('select[name="shops[0][gold_color]"]').val($('select[name="shops[0][gold_color]"] option:first').val());
                            $('input[name="shops[0][weight]"]').val('');
                            $('input[name="shops[0][talab]"]').prop('checked', false);
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON.message || 'Error adding item'
                        });
                    }
                });
            });

            $(document).on('click', '.remove-item', function() {
                var itemId = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to remove this item?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, remove it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('gold-items.remove-session-item') }}',
                            type: 'DELETE',
                            data: {
                                id: itemId,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    $(`tr[data-id="${itemId}"]`).remove();
                                    $('#items-count').text(response.total_items);
                                    Swal.fire(
                                        'Deleted!',
                                        'Item has been removed.',
                                        'success'
                                    );
                                }
                            }
                        });
                    }
                });
            });

            $('#submit-all-items').click(function() {
                Swal.fire({
                    title: 'Submit all items?',
                    text: "Are you sure you want to submit all items?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, submit all'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('gold-items.submit-all') }}',
                            type: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: 'All items submitted successfully',
                                    timer: 1500
                                }).then(() => {
                                    window.location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: xhr.responseJSON.message || 'Error submitting items'
                                });
                            }
                        });
                    }
                });
            });
        });

        // function checkModelExists(modelInput, createRoute) {
        //     const model = modelInput.value.trim();
        //     if (model) {
        //         fetch(`/check-model-exists?model=${model}`)
        //             .then(response => response.json())
        //             .then(data => {
        //                 if (!data.exists) {
        //                     const url = `${createRoute}?model=${encodeURIComponent(model)}`;
        //                     window.location.href = url;
        //                 }
        //             });
        //     }
        // }
    </script>

    <script>
        const shops = @json($shops);
        const goldColors = @json($goldColors);
    </script>
    <script src="{{ asset('js/model_details.js') }}"></script>
</body>
</html>