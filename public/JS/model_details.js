document.addEventListener('DOMContentLoaded', function () {
    // Get DOM elements with null checks
    const modelInput = document.getElementById('model');
    const kindInput = document.getElementById('kind');
    const addFieldBtn = document.getElementById('add-field-btn');
    const dynamicFieldsContainer = document.getElementById('dynamic-fields-container');
    const tableBody = document.querySelector('#shop-data-table tbody');
    const modelImageDiv = document.getElementById('model-image');

    // Check if required elements exist before setting up event listeners
    if (!modelInput || !tableBody || !modelImageDiv) {
        console.error('Required DOM elements not found:', {
            modelInput: !!modelInput,
            tableBody: !!tableBody,
            modelImageDiv: !!modelImageDiv
        });
        return;
    }

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

        tableBody.innerHTML = '<tr><td colspan="4">Loading...</td></tr>';
        modelImageDiv.innerHTML = '<p>Loading...</p>';

        fetch(`/gold-items/model-details?model=${encodeURIComponent(modelValue)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Received data:', data);
                tableBody.innerHTML = '';
                modelImageDiv.innerHTML = '';

                // Set the source value from model details
                const sourceInput = document.querySelector('.source-input');
                if (sourceInput) {
                    if (data.modelDetails && data.modelDetails.source) {
                        sourceInput.value = data.modelDetails.source;
                    } else {
                        sourceInput.value = 'Production'; // Default value if no source found
                    }
                }

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

                // Display the shop data (regular items first, then pending requests)
                if (data.shopData && Array.isArray(data.shopData)) {
                    // First show regular items
                    data.shopData
                        .filter(item => !item.is_pending)
                        .forEach(item => addTableRow(item, false));

                    // Then show pending requests
                    data.shopData
                        .filter(item => item.is_pending)
                        .forEach(item => addTableRow(item, true));
                } else {
                    tableBody.innerHTML = '<tr><td colspan="4">No items found for this model</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                tableBody.innerHTML = '<tr><td colspan="4">Error fetching data</td></tr>';
                modelImageDiv.innerHTML = '<p>Error loading model details</p>';
            });
    }

    function addTableRow(item, isPending) {
        if (!item || !item.shop_name) {
            console.warn('Invalid item data:', item);
            return;
        }

        console.log('Item data:', {
            serialNumbers: item.serial_numbers,
            weights: item.weights,
            item: item
        });

        const row = document.createElement('tr');
        if (isPending) {
            row.classList.add('pending-request');
            row.style.backgroundColor = 'rgb(44, 40, 25)';
        }

        // Create arrays to store serial numbers and their corresponding weights
        const serialNumbers = Array.isArray(item.serial_numbers) ? item.serial_numbers : [];
        const weights = Array.isArray(item.weights) ? item.weights : [];

        // Format serial numbers and weights to be in one line each
        const formattedItems = serialNumbers.map((serial, index) => {
            const weight = weights[index] ? parseFloat(weights[index]).toFixed(2) : '0.00';
            return `${serial.trim()}: ${weight}g`;
        }).join('\n');

        row.innerHTML = `
            <td>
                ${item.shop_name} (ID: ${item.shop_id})
                ${isPending ? '<span class="badge bg-warning text-dark ms-2">Pending</span>' : ''}
            </td>
            <td>${item.gold_color || 'N/A'}</td>
            <td>${item.total_weight ? parseFloat(item.total_weight).toFixed(2) : '0.00'}</td>
            <td style="white-space: pre-line; font-family: monospace;">${formattedItems}</td>
            <td>${item.count || 0}</td>
        `;
        tableBody.appendChild(row);
    }

    // Event Listeners - Only add if elements exist
    modelInput.addEventListener('input', debounce(function(e) {
        const modelValue = e.target.value;
        if (kindInput) {
            kindInput.value = determineKind(modelValue);
        }
        fetchItems(modelValue);
    }, 300));

    modelInput.addEventListener('change', function(e) {
        const modelValue = e.target.value;
        if (kindInput) {
            kindInput.value = determineKind(modelValue);
        }
        fetchItems(modelValue);
    });

    // Only add these event listeners if the elements exist
    if (addFieldBtn) {
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
    }

    // Handle shop selection if the document exists
    if (document) {
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('shop-input')) {
                const selectedValue = e.target.value;
                const match = selectedValue.match(/^(.*?)\s*\(ID:\s*(\d+)\)$/);
                
                if (match) {
                    const [_, shopName, shopId] = match;
                    const index = e.target.dataset.index;
                    const shopIdInput = document.querySelector(`input[name="shops[${index}][shop_id]"]`);
                    if (shopIdInput) {
                        shopIdInput.value = shopId;
                    }
                }
            }
        });
    }

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

    // Add this CSS at the top of your file
    const styles = `
        #shop-data-table td:nth-child(4) {  /* Serial Numbers column */
            min-width: 200px;
            width: 40%;
            white-space: pre-line;
            font-family: monospace;  /* Use monospace font for better alignment */
        }

        #shop-data-table {
            table-layout: fixed;
            width: 100%;
        }

        #shop-data-table th:nth-child(1) { width: 20%; }  /* Shop column */
        #shop-data-table th:nth-child(2) { width: 10%; }  /* Color column */
        #shop-data-table th:nth-child(3) { width: 15%; }  /* Total Weight column */
        #shop-data-table th:nth-child(4) { width: 40%; }  /* Serial Numbers column */
        #shop-data-table th:nth-child(5) { width: 15%; }  /* Count column */
    `;

    // Add the styles to the document
    document.addEventListener('DOMContentLoaded', function() {
        const styleSheet = document.createElement("style");
        styleSheet.textContent = styles;
        document.head.appendChild(styleSheet);
    });
});
