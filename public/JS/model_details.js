document.addEventListener('DOMContentLoaded', function () {
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
            '7': 'Ring'
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
                imageElement.src = `/${data.modelDetails.scanned_image}`;
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

        newField.querySelector('.remove-field-btn').addEventListener('click', function () {
            newField.remove();
        });
    });
});