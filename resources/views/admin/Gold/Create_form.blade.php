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
    {{-- <link href="{{ asset('css/checkbox.css') }}" rel="stylesheet"> --}}
    {{-- <link href="{{ asset('css/input.css') }}" rel="stylesheet"> --}}
</head>

<body>
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
                    <input list="models" name="model" id="model" required>
                    <datalist id="models">
                        @foreach ($models as $model)
                            <option value="{{ $model->model }}"></option>
                        @endforeach
                    </datalist>
                </div>
                <div style="width: 40%" class="form-group">
                    <label for="kind">Kind:</label>
                    <input type="text" name="kind" id="kind" readonly required>
                </div>
            </div>
            {{-- <div  class="form-group">
            <label for="kind">Kind:</label>
            <select style="width:250px" name="kind" id="kind" required>
                @foreach ($kinds as $kind)
                    <option value="{{ $kind }}">{{ $kind }}</option>
                @endforeach
            </select>
        </div> --}}

            <div class="dynamic-field">
                <div class="form-group">
                    <label for="metal_type">Metal Type:</label>
                    <select style="width: 250px" name="metal_type" id="metal_type" required>
                        @foreach ($metalTypes as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
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

                    {{-- </div> --}}

                    {{-- <button type="button" class="remove-field-btn">Remove</button> --}}
                </div>
            </div>

            <button type="button" id="add-field-btn">Add More</button>
            <button type="submit">Create Gold Item</button>
        </form>
        {{-- <div class="model_image">
        @if ($item->modelCategory && $item->modelCategory->scanned_image)
        <img src="{{ asset($item->modelCategory->scanned_image) }}" alt="Scanned Image" width="50">
    @else
        No Image
    @endif
    </div> --}}
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
        document.addEventListener('DOMContentLoaded', function() {
            const modelInput = document.getElementById('model');
            const kindInput = document.getElementById('kind');
            const addFieldBtn = document.getElementById('add-field-btn');
            const dynamicFieldsContainer = document.getElementById('dynamic-fields-container');
            const tableBody = document.querySelector('#shop-data-table tbody');
            const modelImageDiv = document.getElementById('model-image');

            function determineKind(modelValue) {
                if (!modelValue) return '';
                const firstChar = modelValue.split('-')[0];
                const kindMapping = {
                    '1': 'Pendant',
                    '2': 'Bracelet',
                    '4': 'Earring',
                    '5': 'Necklace',
                    '7': 'Ring',
                    '8': 'Brooch',
                    '9': 'Cufflink',
                    '10': 'Anklet'
                };
                return kindMapping[firstChar] || '';
            }

            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }

            function fetchItems(modelValue) {
                if (!modelValue) {
                    tableBody.innerHTML = '';
                    modelImageDiv.innerHTML = '';
                    return;
                }

                modelImageDiv.innerHTML = '<p>Loading...</p>';
                tableBody.innerHTML = '<tr><td colspan="2">Loading...</td></tr>';

                fetch(`/gold-items/model-details?model=${encodeURIComponent(modelValue)}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        tableBody.innerHTML = '';
                        modelImageDiv.innerHTML = '';

                        if (data.modelDetails && data.modelDetails.scanned_image) {
                            const imageElement = document.createElement('img');
                            imageElement.src = `/storage/${data.modelDetails.scanned_image}`;
                            imageElement.alt = `Model ${data.modelDetails.model}`;
                            imageElement.className = 'model-scanned-image';

                            const modelInfo = document.createElement('div');
                            modelInfo.className = 'model-info';

                            modelImageDiv.appendChild(modelInfo);
                            modelImageDiv.appendChild(imageElement);
                        } else {
                            modelImageDiv.innerHTML = '<p>No image available for this model</p>';
                        }

                        if (data.items.length === 0) {
                            const row = document.createElement('tr');
                            row.innerHTML = '<td colspan="3">No items found</td>';
                            tableBody.appendChild(row);
                            return;
                        }

                        data.items.forEach(item => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                    <td>${item.shop_name}</td>
                    <td>${item.weight}</td>
                    <td>${item.serial_number}</td>
                    <td>${item.gold_color}</td>
                `;
                            tableBody.appendChild(row);
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        tableBody.innerHTML = '<tr><td colspan="3">Error fetching data</td></tr>';
                        modelImageDiv.innerHTML = '<p>Error loading model details</p>';
                    });
            }

            // Event Listeners
            modelInput.addEventListener('input', function(e) {
                const modelValue = e.target.value;
                kindInput.value = determineKind(modelValue);
                debounce(fetchItems, 300)(modelValue);
            });

            modelInput.addEventListener('change', function(e) {
                const modelValue = e.target.value;
                kindInput.value = determineKind(modelValue);
            });

            addFieldBtn.addEventListener('click', function() {
                const index = dynamicFieldsContainer.children.length;
                const fieldHTML = `
            <div class="dynamic-field">
                <div class="form-group">
                    <label for="shop_id">Shop:</label>
                    <select name="shops[${index}][shop_id]" required>
                        @foreach ($shops as $shop)
                            <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="gold_color">Gold Color:</label>
                    <select name="shops[${index}][gold_color]" required>
                        @foreach ($goldColors as $color)
                            <option value="{{ $color }}">{{ $color }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="weight">Weight:</label>
                    <input type="number" step="0.01" name="shops[${index}][weight]" required>
                </div>
                <div>
                      <label for="talab_${index}">Talab:</label>
        <input type="hidden" name="shops[${index}][talab]" value="0">
        <input type="checkbox" class="checkboxInput" id="talab_${index}" name="shops[${index}][talab]" value="1">
        <label for="talab_${index}" class="toggleSwitch"></label>
        </div>
            </div>
            
            <button type="button" class="remove-field-btn">Remove</button>
        `;

                const newField = document.createElement('div');
                newField.innerHTML = fieldHTML;
                dynamicFieldsContainer.appendChild(newField);

                newField.querySelector('.remove-field-btn').addEventListener('click', function() {
                    newField.remove();
                });
            });
        });
    </script>
</body>

</html>
