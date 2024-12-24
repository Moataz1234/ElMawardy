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
     <style>

        /* .table-container {
    margin-top: 20px;
    overflow-x: auto;
} */
/* 
.model-info {
    margin-bottom: 15px;
}

.model-info p {
    margin: 5px 0;
} */
</style>
 </head>
 <body>
    <div class="parent">
    <form class="create-form" action="{{ route('gold-items.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <!-- Fixed fields -->
        <div class="dynamic-field">
   
        <div style="width: 40%" class="form-group">
            <label for="model">Model:</label>
            <input  list="models" name="model" id="model" required>
            <datalist id="models">
                @foreach($models as $model)
                    <option value="{{ $model->model }}"></option>
                @endforeach
            </datalist>
        </div>
        <div  class="form-group">
            <label for="kind">Kind:</label>
            <select style="width:250px" name="kind" id="kind" required>
                @foreach($kinds as $kind)
                    <option value="{{ $kind }}">{{ $kind }}</option>
                @endforeach
            </select>
        </div>
    
        </div>
        <div class="dynamic-field">
        <div class="form-group">
            <label for="metal_type">Metal Type:</label>
            <select style="width: 250px" name="metal_type" id="metal_type" required>
                @foreach($metalTypes as $type)
                    <option value="{{ $type }}">{{ $type }}</option>
                @endforeach
            </select>
        </div>
            <div class="form-group">
            <label for="metal_purity">Metal Purity:</label>
            <select style="width: 250px" name="metal_purity" id="metal_purity" required>
                @foreach($metalPurities as $purity)
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
        <div style="width: 40%" >
            <label for="talab">Talab:</label>
            <input type="hidden" name="talab" value="0">
            <input type="checkbox" name="talab" id="talab" value="1">        
        </div>
        </div>
        <!-- Dynamic fields section -->
        <div id="dynamic-fields-container">
            <div class="dynamic-field">
                {{-- <div class="form-row"> --}}
                    <div class="form-group">
                    <label for="shop_id">Shop:</label>
                    <select name="shops[0][shop_id]" required>
                        @foreach($shops as $shop)
                            <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                        @endforeach
                    </select>
                    </div>
                    <div class="form-group">
                    <label for="gold_color">Gold Color:</label>
                    <select name="shops[0][gold_color]" required>
                        @foreach($goldColors as $color)
                            <option value="{{ $color }}">{{ $color }}</option>
                        @endforeach
                    </select>
                    </div>
                    <div class="form-group">
                    <label for="weight">Weight:</label>
                    <input type="number" step="0.01" name="shops[0][weight]" required>
                    </div>
                {{-- </div> --}}

                {{-- <button type="button" class="remove-field-btn">Remove</button> --}}
            </div>
        </div>
    
        <button type="button" id="add-field-btn">Add More</button>
        <button type="submit">Create Gold Item</button>
    </form>
    {{-- <div class="model_image">
        @if($item->modelCategory && $item->modelCategory->scanned_image)
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
                </tr>
            </thead>
            <tbody>
                <!-- Dynamic rows will be inserted here -->
            </tbody>
        </table>
    </div>
    </div>
    </div>
{{-- <div class="modal " id="modelDetailsModal" tabindex="-1" role="dialog" aria-labelledby="modelDetailsModalLabel" aria-hidden="true">
        <div style="background-color: #babfc5;margin:10px 250px" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modelDetailsModalLabel" >Items with Same Model</h5>
            </div>
            <div class="modal-body" id="modal-body-content">
                Loading...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
</div> --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
    const addFieldBtn = document.getElementById('add-field-btn');
    const dynamicFieldsContainer = document.getElementById('dynamic-fields-container');

    addFieldBtn.addEventListener('click', function () {
        const index = dynamicFieldsContainer.children.length;

        const fieldHTML = `
            <div class="dynamic-field">
                <div class="form-group">
                    <label for="shop_id">Shop:</label>
                    <select name="shops[${index}][shop_id]" required>
                        @foreach($shops as $shop)
                            <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="gold_color">Gold Color:</label>
                    <select name="shops[${index}][gold_color]" required>
                        @foreach($goldColors as $color)
                            <option value="{{ $color }}">{{ $color }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="weight">Weight:</label>
                    <input type="number" step="0.01" name="shops[${index}][weight]" required>
                </div>
            </div>
                            <button type="button" class="remove-field-btn">Remove</button>

        `;

        const newField = document.createElement('div');
        newField.innerHTML = fieldHTML;
        dynamicFieldsContainer.appendChild(newField);

        newField.querySelector('.remove-field-btn').addEventListener('click', function () {
            newField.remove();
        });
    });
    const modelInput = document.getElementById('model');
    const tableBody = document.querySelector('#shop-data-table tbody');
    const modelImageDiv = document.getElementById('model-image');

    // Debounce function to limit API calls
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

    // Function to fetch and display items and image
    function fetchItems(modelValue) {
        if (!modelValue) {
            tableBody.innerHTML = '';
            modelImageDiv.innerHTML = '';
            return;
        }

        // Show loading state
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
            // Clear existing rows
            tableBody.innerHTML = '';
            modelImageDiv.innerHTML = '';

            // Handle model details and image
            if (data.modelDetails && data.modelDetails.scanned_image) {
                const imageElement = document.createElement('img');
                imageElement.src = `/${data.modelDetails.scanned_image}`;
                imageElement.alt = `Model ${data.modelDetails.model}`;
                imageElement.className = 'model-scanned-image';
                
                const modelInfo = document.createElement('div');
                modelInfo.className = 'model-info';
                // modelInfo.innerHTML = `
                //     <p><strong>Model:</strong> ${data.modelDetails.model}</p>
                //     <p><strong>SKU:</strong> ${data.modelDetails.SKU || 'N/A'}</p>
                // `;
                
                modelImageDiv.appendChild(modelInfo);
                modelImageDiv.appendChild(imageElement);
            } else {
                modelImageDiv.innerHTML = '<p>No image available for this model</p>';
            }

            // Handle items table
            if (data.items.length === 0) {
                const row = document.createElement('tr');
                row.innerHTML = '<td colspan="2">No items found</td>';
                tableBody.appendChild(row);
                return;
            }

            // Add new rows
            data.items.forEach(item => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${item.shop_name}</td>
                    <td>${item.weight}</td>
                    <td>${item.serial_number}</td>
                `;
                tableBody.appendChild(row);
            });
        })
        .catch(error => {
            console.error('Error:', error);
            tableBody.innerHTML = '<tr><td colspan="2">Error fetching data</td></tr>';
            modelImageDiv.innerHTML = '<p>Error loading model details</p>';
        });
    }

    // Add event listener with debounce
    modelInput.addEventListener('input', debounce((e) => {
        fetchItems(e.target.value);
    }, 300));
});
</script>
 </body>
 </html>
