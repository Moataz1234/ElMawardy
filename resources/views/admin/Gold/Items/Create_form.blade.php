<!DOCTYPE html>
<html lang="en">

<head>
    @include('components.navbar')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Gold Item</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/navbar.css') }}" rel="stylesheet">
    <link href="{{ asset('css/create_form.css') }}" rel="stylesheet">
    <link href="{{ asset('css/checkbox.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> --}}
</head>

<body>
    <h1 style="text-align: center">Items</h1>
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <div class="parent">
        <div class="d-flex flex-column ">
            <form class="create-form" id="gold-item-form" style="width: 100%">
                @csrf
                <!-- Fixed fields -->
                <div class="dynamic-field">
                    <div style="width: 40%" class="form-group">
                        <label for="model">Model:</label>
                        <input list="models" name="model" id="model" required
                            onblur="checkModelExists(this, '{{ route('models.create') }}')">
                        <datalist id="models">
                            @foreach ($models as $model)
                                <option value="{{ $model->model }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                    <div style="width: 40%" class="form-group">
                        <label for="kind">Kind:</label>
                        <input type="text" name="kind" id="kind" readonly>
                    </div>
                </div>
                <div class="dynamic-field">
                    <div class="form-group">
                        <label for="metal_type">Metal Type:</label>
                        <select style="width: 250px" name="metal_type" id="metal_type" required>
                            <option value="Gold">Gold</option>
                            <option value="Platinium">Platinium</option>
                            <option value="Silver">Silver</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="metal_purity">Metal Purity:</label>
                        <select style="width: 250px" name="metal_purity" id="metal_purity" required>
                            @foreach ($metalPurities as $purity)
                                <option value="{{ $purity }}">{{ $purity }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="dynamic-field">
                    <div style="width: 40%" class="form-group">
                        <label for="quantity">Quantity:</label>
                        <input type="number" name="quantity" id="quantity" value="1" required>
                    </div>
                </div>

                <div id="dynamic-fields-container">
                    <div class="dynamic-field">
                        <div class="form-group">
                            <label for="shop_id">Shop:</label>
                            <select name="shops[0][shop_id]" required>
                                @foreach ($shops as $shop)
                                    <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="gold_color">Gold Color:</label>
                            <select name="shops[0][gold_color]" required>
                                @foreach ($goldColors as $color)
                                    <option value="{{ $color }}">{{ $color }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="weight">Weight:</label>
                            <input type="number" step="0.01" name="shops[0][weight]" required>
                        </div>
                        <div class="form-group">
                            <label for="talab">Talab:</label>
                            <input type="hidden" name="shops[0][talab]" value="0">
                            <input type="checkbox" class="checkboxInput" id="checkboxInput" name="shops[0][talab]"
                                value="1">
                            <label for="checkboxInput" class="toggleSwitch"></label>
                        </div>
                    </div>
                </div>
                {{-- <button type="button" id="add-field-btn">Add More</button> --}}
                <button type="button" id="add-item-btn">Add to List</button>
            </form>
            <!-- Session Items Table -->
            <div class="table-container">
                <table id="items-table" border="1">
                    <thead>
                        <tr>
                            <th>Model</th>
                            <th>Shop_id</th>
                            <th>Weight</th>
                            <th>Kind</th>
                            <th>Quantity</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (session('gold_items'))
                            @foreach (session('gold_items') as $item)
                                <tr data-id="{{ $item['id'] }}">
                                    <td>{{ $item['model'] }}</td>
                                    <td>{{ $item['shops'][0]['shop_id'] }}</td>
                                    <td>{{ $item['shops'][0]['weight'] }}</td>
                                    <td>{{ $item['kind'] }}</td>
                                    <td>{{ $item['quantity'] }}</td>
                                    <td>
                                        <button class="remove-item btn btn-danger"
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
        {{-- -- Model Details --}}
        <div class="model-details">
            <div class="model-image-container">
                <h3>Model Image</h3>
                <div id="model-image"></div>
            </div>
            <!-- Original Shop Data Table -->
            <table id="shop-data-table" border="1">
                <thead>
                    <tr>
                        <th>Shop Name</th>
                        <th>Weight</th>
                        <th>Serial Number</th>
                        <th>Gold Color</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Dynamic rows will be inserted here -->
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Display session messages using SweetAlert
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

            $('#add-item-btn').click(function(e) {
                e.preventDefault();
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
                                <td>${response.item.shops[0].id}</td>  // Add shop name
                                <td>${response.item.shops[0].weight}</td>
                                <td>${response.item.kind}</td>
                                <td>${response.item.quantity}</td>
                                <td>
                                    <button class="remove-item" data-id="${response.item.id}">Remove</button>
                                </td>
                            </tr>
                        `;
                            $('#items-table tbody').append(newRow);
                            $('#items-count').text(response.total_items);
                            $('#gold-item-form')[0].reset();

                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Item added successfully',
                                timer: 1500
                            });
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
                                _token: '{{ csrf_token() }}' // Add this line
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
        });

        function checkModelExists(modelInput, createRoute) {
            const model = modelInput.value.trim();
            if (model) {
                fetch(`/check-model-exists?model=${model}`)
                    .then(response => response.json())
                    .then(data => {
                        if (!data.exists) {
                            const url = `${createRoute}?model=${encodeURIComponent(model)}`;
                            window.location.href = url;
                        }
                    });
            }

        }
    </script>

    <script>
        const shops = @json($shops);
        const goldColors = @json($goldColors);
    </script>
    <script src="{{ asset('js/model_details.js') }}"></script>
    
</body>

</html>
