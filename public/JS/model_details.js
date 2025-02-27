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
        tableBody.innerHTML = '<tr><td colspan="4">Loading...</td></tr>';

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

            // Handle model image
            if (data.modelDetails && data.modelDetails.scanned_image) {
                const imageElement = document.createElement('img');
                imageElement.src = `/storage/${data.modelDetails.scanned_image}`;
                imageElement.alt = `Model ${data.modelDetails.model}`;
                imageElement.className = 'model-scanned-image';
                modelImageDiv.appendChild(imageElement);
            } else {
                modelImageDiv.innerHTML = '<p>No image available for this model</p>';
            }

            // First display existing items
            if (data.items && data.items.length > 0) {
                data.items.forEach(item => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${item.shop_name} (${item.gold_color})</td>
                        <td>${parseFloat(item.weight).toFixed(2)}</td>
                        <td>${item.serial_number}</td>
                        <td>${item.quantity}</td>
                    `;
                    tableBody.appendChild(row);
                });
            }

            // Then display pending requests with yellow background and badge
            if (data.pendingRequests && data.pendingRequests.length > 0) {
                data.pendingRequests.forEach(item => {
                    const row = document.createElement('tr');
                    row.style.backgroundColor = 'rgb(44, 40, 25)';
                    row.innerHTML = `
                        <td>
                            ${item.shop_name} (${item.gold_color})
                            <span class="badge bg-warning text-dark ms-2">Pending</span>
                        </td>
                        <td>${parseFloat(item.weight).toFixed(2)}</td>
                        <td>${item.serial_number}</td>
                        <td>${item.quantity}</td>
                    `;
                    tableBody.appendChild(row);
                });
            }

            if ((!data.items || data.items.length === 0) && 
                (!data.pendingRequests || data.pendingRequests.length === 0)) {
                tableBody.innerHTML = '<tr><td colspan="4">No items found</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            tableBody.innerHTML = '<tr><td colspan="4">Error fetching data</td></tr>';
            modelImageDiv.innerHTML = '<p>Error loading model details</p>';
        });
    }

    // Event Listeners
    modelInput.addEventListener('input', function (e) {
        const modelValue = e.target.value;
        kindInput.value = determineKind(modelValue);
        debounce(fetchItems, 300)(modelValue);
    });

    modelInput.addEventListener('change', function (e) {
        const modelValue = e.target.value;
        kindInput.value = determineKind(modelValue);
        fetchItems(modelValue);
    });

    addFieldBtn.addEventListener('click', function () {
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
        };

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

    // Handle shop selection
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('shop-input')) {
            const selectedValue = e.target.value;
            const match = selectedValue.match(/^(.*?)\s*\(ID:\s*(\d+)\)$/);
            
            if (match) {
                const [_, shopName, shopId] = match;
                const index = e.target.dataset.index;
                document.querySelector(`input[name="shops[${index}][shop_id]"]`).value = shopId;
            }
        }
    });

   
    // Modify your existing code that adds new rows to include stars
    function addNewRow(item) {
        const newRow = `
            <tr data-id="${item.id}">
                <td>${item.model}</td>
                <td>${item.shops[0].shop_name || ''}</td>
                <td>${item.shops[0].weight}</td>
                <td>${item.kind}</td>
                <td>${item.quantity}</td>
          
                <td>
                    <button class="remove-item" data-id="${item.id}">Remove</button>
                </td>
            </tr>
        `;
        $('#items-table tbody').append(newRow);
    }

    function updateModelDetails(model) {
        if (!model) return;

        $.ajax({
            url: '/api/model-details/' + model,
            type: 'GET',
            success: function(data) {
                // Clear existing table
                $('#shop-data-table tbody').empty();

                // First show pending add requests with yellow background
                if (data.addRequests && data.addRequests.length > 0) {
                    data.addRequests.forEach(function(item) {
                        if (item.status === 'pending') {
                            $('#shop-data-table tbody').append(`
                                <tr style="background-color:rgb(44, 40, 25);">
                                    <td>${item.shop_name} - ${item.gold_color}</td>
                                    <td>${item.weight}</td>
                                    <td>${item.serial_number}</td>
                                    <td>${item.quantity}</td>
                                </tr>
                            `);
                        }
                    });
                }

                // Then show existing gold items
                if (data.goldItems && data.goldItems.length > 0) {
                    data.goldItems.forEach(function(item) {
                        $('#shop-data-table tbody').append(`
                            <tr>
                                <td>${item.shop_name} - ${item.gold_color}</td>
                                <td>${item.weight}</td>
                                <td>${item.serial_number}</td>
                                <td>${item.quantity}</td>
                            </tr>
                        `);
                    });
                }

                // Update model image if available
                if (data.image_url) {
                    $('#model-image').html(`<img src="${data.image_url}" alt="Model Image">`);
                } else {
                    $('#model-image').html('No image available');
                }
            }
        });
    }
});
