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
    {{-- <link href="{{ asset('css/input.css') }}" rel="stylesheet"> --}}
</head>

<body>
    <h1 style="text-align: center">Items</h1>
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <div class="parent">
        <form class="create-form" action="{{ route('gold-items.store') }}" method="POST" enctype="multipart/form-data">
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
            <!-- Dynamic fields section -->
            <div id="dynamic-fields-container">
                <div class="dynamic-field">
                    {{-- <div class="form-row"> --}}
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

            <button type="button" id="add-field-btn">Add More</button>
            <button type="submit">Create Gold Item</button>
        </form>
        <div class="model-details">
            <div class="model-image-container">
                <h3>Model Image</h3>
                <div id="model-image"></div>
            </div>
            <div class="table-container">
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
    </div>
    </div>
    <script>
             function checkModelExists(modelInput, createRoute) {
            const model = modelInput.value.trim();

            if (model) {
                // Make an AJAX request to check if the model exists
                fetch(`/check-model-exists?model=${model}`)
                    .then(response => response.json())
                    .then(data => {
                        if (!data.exists) {
                            // Construct the URL
                            const url = `${createRoute}?model=${encodeURIComponent(model)}`;
                            console.log('Redirecting to:', url); // Debugging
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
