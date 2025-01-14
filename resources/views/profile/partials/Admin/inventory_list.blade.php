<!DOCTYPE html>
<html>

<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <form method="POST" action="{{ route('bulk-action') }}" id="bulkActionForm">
        @csrf
        <input type="hidden" name="action" value="delete">
        <table class="table">
            <thead>
                <tr>
                    <th><input type="checkbox" id="select-all" /></th>
                    <th>Image</th>
                    @php
                        // Array of columns with their display names
                        $columns = [
                            'serial_number' => 'Serial Number',
                            'shop_name' => 'Shop Name',
                            'kind' => 'Kind',
                            'model' => 'Model',
                            'gold_color' => 'Gold Color',
                            // 'stones' => 'Stones',
                            // 'metal_type' => 'Metal Type',
                            'metal_purity' => 'Metal Purity',
                            // 'quantity' => 'Quantity',
                            'weight' => 'Weight',
                            'stars' => 'stars',
                            // 'source' => 'Source',
                            // 'average_of_stones' => 'Average of Stones',
                            // 'net_weight' => 'Net Weight',
                        ];
                    @endphp

                    @foreach ($columns as $field => $label)
                        <th>
                            {{ $label }}
                        </th>
                    @endforeach
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($goldItems as $item)
                    <tr>
                        <td><input type="checkbox" name="selected_items[]" value="{{ $item->id }}" /></td>
                        <td>
                            @if ($item->modelCategory)
                                @if ($item->modelCategory->scanned_image)
                                    <img src="{{ asset('storage/' . $item->modelCategory->scanned_image) }}"
                                        alt="Scanned Image" width="50">
                                @endif
                            @else
                                No matching model found
                            @endif
                        </td>
                        <td>{{ $item->serial_number }}</td>
                        <td>{{ $item->shop_name ?? 'Admin' }}</td>
                        <td>{{ $item->kind }}</td>
                        <td>
                            <a href="#" class="model-link"
                                data-model="{{ $item->model }}">{{ $item->model }}</a>
                        </td>
                        <td>{{ $item->gold_color }}</td>
                        {{-- <td>{{ $item->stones }}</td> --}}
                        {{-- <td>{{ $item->metal_type }}</td> --}}
                        <td>{{ $item->metal_purity }}</td>
                        {{-- <td>{{ $item->quantity }}</td> --}}
                        <td>{{ $item->weight }}</td>
                        <td>{{ $item->modelCategory->category ?? 'No Category' }}</td>
                        <td>
                            <a class="action_button" href="{{ route('gold-items.edit', $item->id) }}">Edit</a>
                        </td>
                        {{-- <td>{{ $item->source }}</td> --}}
                        {{-- <td>{{ $item->average_of_stones }}</td> --}}
                        {{-- <td>{{ $item->net_weight }}</td> --}}
                        {{-- <td>
                        <a class="action_button" href="{{ route('gold-items.edit', $item->id) }}" >Edit</a> --}}
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="button-container">
            <button class="delete_btn" type="button" name="action" value="delete" form="bulkActionForm">Delete</button>
            <button class="request_btn" type="submit" name="action" value="request">Request Item</button>
            <button class="workshop_btn" type="button" name="action" value="workshop" form="bulkActionForm"> Workshop</button>
        </div>
    </form>
    <!-- Model Details Modal -->
    <div class="modal" id="modelDetailsModal" tabindex="-1" role="dialog" aria-labelledby="modelDetailsModalLabel"
        aria-hidden="true">
        <div style="background-color: #babfc5; margin: 10px 250px;" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modelDetailsModalLabel">Items with Same Model</h5>
            </div>
            <div class="modal-body" id="modal-body-content">
                Loading...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Workshop transfer button handler
            const workshopBtn = document.querySelector('.workshop_btn');
            if (workshopBtn) {
                workshopBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const selectedItems = Array.from(document.querySelectorAll(
                        'input[name="selected_items[]"]:checked'));
                    
                    if (selectedItems.length === 0) {
                        Swal.fire('Error', 'Please select items to transfer', 'error');
                        return;
                    }

                    const mappedItems = selectedItems.map(checkbox => {
                        const row = checkbox.closest('tr');
                        return {
                            id: checkbox.value,
                            serial: row.querySelector('td:nth-child(3)').textContent,
                            model: row.querySelector('td:nth-child(6)').textContent
                        };
                    });

                    // First ask if they want to transfer just selected items or all items with same models
                    Swal.fire({
                        title: 'Transfer Options',
                        text: 'Do you want to transfer just the selected items or all items with the same models?',
                        showDenyButton: true,
                        showCancelButton: true,
                        confirmButtonText: 'Selected Items',
                        denyButtonText: 'All Items with Same Models',
                        cancelButtonText: 'Cancel'
                    }).then((firstResult) => {
                        if (firstResult.isDismissed) {
                            return; // User clicked cancel
                        }

                        const transferAllModels = firstResult.isDenied;
                        const modelsToTransfer = transferAllModels 
                            ? [...new Set(mappedItems.map(item => item.model))]
                            : null;

                        // Now ask for the reason
                        Swal.fire({
                            title: 'Confirm Transfer',
                            html: `
                                <p>You are about to transfer ${transferAllModels ? 'ALL ITEMS' : 'SELECTED ITEMS'}</p>
                                ${transferAllModels 
                                    ? `<p>Models to transfer: ${modelsToTransfer.join(', ')}</p>`
                                    : `<ul style="text-align: left; max-height: 200px; overflow-y: auto;">${mappedItems.map(item => 
                                        `<li>${item.serial} - ${item.model}</li>`
                                    ).join('')}</ul>`
                                }
                                <div class="form-group">
                                    <label>Reason for transfer:</label>
                                    <textarea id="transfer-reason" class="form-control" required></textarea>
                                </div>
                            `,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, transfer them!',
                            preConfirm: () => {
                                const reason = document.getElementById('transfer-reason').value;
                                if (!reason || reason.trim() === '') {
                                    Swal.showValidationMessage('Please enter a reason for transfer');
                                    return false;
                                }
                                return true;
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const reason = document.getElementById('transfer-reason').value;
                                const reasonInput = document.createElement('input');
                                reasonInput.type = 'hidden';
                                reasonInput.name = 'transfer_reason';
                                reasonInput.value = reason;
                                document.getElementById('bulkActionForm').appendChild(reasonInput);

                                // Add transfer mode input
                                const transferModeInput = document.createElement('input');
                                transferModeInput.type = 'hidden';
                                transferModeInput.name = 'transfer_all_models';
                                transferModeInput.value = transferAllModels;
                                document.getElementById('bulkActionForm').appendChild(transferModeInput);
                                
                                // Set the action value to workshop
                                document.querySelector('input[name="action"]').value = 'workshop';
                                
                                // Submit the form
                                document.getElementById('bulkActionForm').submit();
                            }
                        });
                    });
                });
            }
            const modelLinks = document.querySelectorAll('.model-link');

            modelLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();

                    const modelName = this.dataset.model;

                    // Update modal title
                    document.getElementById('modelDetailsModalLabel').innerText =
                        `Items with Model: ${modelName}`;

                    // Show "Loading..." while fetching data
                    const modalBody = document.getElementById('modal-body-content');
                    modalBody.innerHTML =
                        '<div class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>';

                    // Fetch items via AJAX
                    fetch(`/gold-items/same-model?model=${encodeURIComponent(modelName)}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.items.length > 0) {
                                let htmlContent = `
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Shop Name</th>
                                                <th>Weight</th>
                                                <th>Serial Number</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                            `;
                                data.items.forEach(item => {
                                    htmlContent += `
                                    <tr>
                                        <td>${item.shop_name}</td>
                                        <td>${item.weight}g</td>
                                        <td>${item.serial_number}</td>
                                    </tr>
                                `;
                                });
                                htmlContent += `
                                        </tbody>
                                    </table>
                                </div>
                            `;
                                modalBody.innerHTML = htmlContent;
                            } else {
                                modalBody.innerHTML =
                                    '<div class="alert alert-info">No other items found with this model.</div>';
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            modalBody.innerHTML =
                                '<div class="alert alert-danger">An error occurred while fetching data.</div>';
                        });

                    // Show the modal
                    $('#modelDetailsModal').modal('show');
                });
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteForm = document.getElementById('bulkActionForm');
            const deleteBtn = deleteForm.querySelector('.delete_btn');

            console.log('DOM Content Loaded');
            console.log('Delete form found:', deleteForm);
            console.log('Delete button found:', deleteBtn);

            if (!deleteForm) {
                console.error('Delete form not found');
                return;
            }
            if (!deleteBtn) {
                console.error('Delete button not found!');
                return;
            }

            deleteBtn.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Delete button clicked');

                const selectedItems = Array.from(deleteForm.querySelectorAll(
                    'input[name="selected_items[]"]:checked'));
                console.log('Selected items count:', selectedItems.length);

                if (selectedItems.length === 0) {
                    Swal.fire('Error', 'Please select items to delete', 'error');
                    return;
                }

                const mappedItems = selectedItems.map(checkbox => {
                    const row = checkbox.closest('tr');
                    return {
                        id: checkbox.value,
                        serial: row.querySelector('td:nth-child(3)').textContent,
                        model: row.querySelector('td:nth-child(6)').textContent
                    };
                });

                Swal.fire({
                    title: 'Confirm Deletion',
                    html: `
                <p>Are you sure you want to delete these items?</p>
                <ul style="text-align: left;">${mappedItems.map(item => 
                    `<li>${item.serial} - ${item.model}</li>`
                ).join('')}</ul>
                <div class="form-group">
                    <label>Reason for deletion:</label>
                    <textarea id="deletion-reason" class="form-control"></textarea>
                </div>
            `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete them!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const reason = document.getElementById('deletion-reason').value;
                        const reasonInput = document.createElement('input');
                        reasonInput.type = 'hidden';
                        reasonInput.name = 'deletion_reason';
                        reasonInput.value = reason;
                        deleteForm.appendChild(reasonInput);

                        console.log('Submitting delete form...');
                        deleteForm.submit();
                    }
                });
            });
        });
    </script>
</body>

</html>
