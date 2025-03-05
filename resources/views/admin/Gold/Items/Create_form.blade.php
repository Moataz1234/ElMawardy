<!DOCTYPE html>
<html lang="en">

<head>
    @include('components.navbar')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Gold Item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="{{ asset('css/create_form.css') }}" rel="stylesheet">
    <link href="{{ asset('css/checkbox.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        #shop-data-table td:nth-child(4) {  /* Serial Numbers column */
            min-width: 200px;
            width: 40%;
            white-space: pre-line;
            font-family: monospace;
        }

        #shop-data-table {
            table-layout: fixed;
            width: 100%;
        }

        #shop-data-table th:nth-child(1) { width: 20%; }  /* Shop column */
        #shop-data-table th:nth-child(2) { width: 10%; }  /* Color column */
        #shop-data-table th:nth-child(3) { width: 20%; }  /* Total Weight column */
        #shop-data-table th:nth-child(4) { width: 40%; }  /* Serial Numbers column */
        #shop-data-table th:nth-child(5) { width: 10%; }  /* Count column */
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
                            <input type="number" name="quantity" id="quantity" value="1" class="form-control"
                                required>
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
                            <input type="date" name="rest_since" id="rest_since" value="<?php echo date('Y-m-d'); ?>"
                                class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="talab">Talab:</label>
                            <div>
                                <input type="hidden" name="shops[0][talab]" value="0">
                                <input type="checkbox" class="checkboxInput" id="checkboxInput" name="shops[0][talab]"
                                    value="1">
                                <label for="checkboxInput" class="toggleSwitch"></label>
                            </div>
                        </div>
                    </div>

                    <!-- Third Row (shop-weight-row) -->
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
                        <div class="form-group">
                            <label for="source">Source:</label>
                            <select name="shops[0][source]" class="form-control source-input" id="source">
                                <option value="">Select Source</option>
                                @foreach ($sources as $source)
                                    <option value="{{ $source }}">{{ $source }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <button class="add-new-model btn btn-primary" type="button" id="add-item-btn">Add to
                        List</button>
                    <a class="add-new-model btn btn-warning" href="{{ route('models.create') }}">Add New Model</a>
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
                                            <button class="remove-item btn"
                                                data-id="{{ $item['id'] }}">Remove</button>
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
                                    <th>Shop</th>
                                    <th>Color</th>
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
                            $('select[name="shops[0][gold_color]"]').val($(
                                'select[name="shops[0][gold_color]"] option:first')
                            .val());
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
                                    text: xhr.responseJSON.message ||
                                        'Error submitting items'
                                });
                            }
                        });
                    }
                });
            });

            // When model changes, fetch its details including default source
            $('input[name="model"]').on('change blur', function() {
                const model = $(this).val();
                if (model) {
                    // Log to check if event is firing
                    console.log('Model changed to:', model);

                    $.ajax({
                        url: `/models/${encodeURIComponent(model)}/details`,
                        method: 'GET',
                        success: function(data) {
                            console.log('Received model details:', data); // Debug log

                            if (data.modelDetails && data.modelDetails.source) {
                                // Set the default source from models table
                                $('.source-input').each(function() {
                                    $(this).val(data.modelDetails.source);
                                    console.log('Setting source to:', data.modelDetails
                                        .source); // Debug log
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching model details:', error);
                        }
                    });
                }
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
