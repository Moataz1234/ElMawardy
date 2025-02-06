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
    modelInput.addEventListener('input', debounce(function (e) {
        const modelValue = e.target.value;
        kindInput.value = determineKind(modelValue);
        fetchItems(modelValue);
    }, 300));

    modelInput.addEventListener('change', function (e) {
        const modelValue = e.target.value;
        kindInput.value = determineKind(modelValue);
        fetchItems(modelValue);
    });

    addItemBtn.addEventListener('click', function () {
        const formData = new FormData(document.getElementById('gold-item-form'));
        const shopsData = Array.from(dynamicFieldsContainer.children).map((field, index) => {
            return {
                shop_id: formData.get(`shops[${index}][shop_id]`),
                gold_color: formData.get(`shops[${index}][gold_color]`),
                weight: formData.get(`shops[${index}][weight]`),
                talab: formData.get(`shops[${index}][talab]`) || 0
            };
        });

        const itemData = {
            model: formData.get('model'),
            kind: formData.get('kind'),
            metal_type: formData.get('metal_type'),
            metal_purity: formData.get('metal_purity'),
            quantity: formData.get('quantity'),
            shops: shopsData
        };addFieldBtn

        // Send the item data to the server
        fetch('/gold-items/add-to-session', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(itemData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update the UI with the new item
                data.item.shops.forEach(shop => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${data.item.model}</td>
                        <td>${shop.shop_id}</td>
                        <td>${shop.weight}</td>
                        <td>${data.item.kind}</td>
                        <td>${data.item.quantity}</td>
                        <td><button class="remove-item" data-id="${data.item.id}">Remove</button></td>
                    `;
                    document.querySelector('#items-table tbody').appendChild(row);
                });
                document.getElementById('items-count').textContent = data.total_items;
                document.getElementById('gold-item-form').reset();
            }
        })
        .catch(error => console.error('Error:', error));
    });
});
