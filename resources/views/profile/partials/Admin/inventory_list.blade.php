<!DOCTYPE html>
<html>

<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f5f5f5;
        }

        /* Table container styles */
        .table-container {
            margin: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            position: relative;
        }

        /* Table wrapper styles - this enables the horizontal scroll */
        .table-wrapper {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            position: relative;
        }

        /* Customize scrollbar appearance */
        .table-wrapper::-webkit-scrollbar {
            height: 8px;
        }

        .table-wrapper::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .table-wrapper::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .table-wrapper::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Keep the table header fixed */
        .table thead {
            position: sticky;
            top: 0;
            z-index: 1;
            background-color: #f8f9fa;
        }

        /* Ensure the table takes up enough width to be scrollable */
        .table {
            width: 100%;
            min-width: 100%;
            margin: 0;
            border-collapse: collapse;
            white-space: nowrap;
            background: white;
            line-height: 1.4;
            table-layout: fixed;
        }

        /* Header styles */
        .table th {
            padding: 10px;
            text-align: left;
            font-weight: 600;
            color: #2c3e50;
            border-bottom: 2px solid #edf2f7;
            font-size: 13px;
        }

        /* Cell styles */
        .table td {
            padding: 2px 4px;  /* Reduced padding */
            font-size: 11px;   /* Smaller font size */
            height: 40px;      /* Reduced row height */
            vertical-align: middle;
            white-space: nowrap;
        }

        /* Row hover effect */
        .table tbody tr:hover {
            background-color: #f8fafc;
        }

        /* Checkbox styles */
        input[type="checkbox"] {
            width: 12px;
            height: 12px;
            cursor: pointer;
        }

        /* Image styles */
       

        /* Link styles */
        .model-link {
            color: #3182ce;
            text-decoration: none;
            font-weight: 500;
        }

        .model-link:hover {
            text-decoration: underline;
        }

        .action_button {
            display: inline-block;
            padding: 2px 4px;
            background-color: #4299e1;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 10px;
            transition: background-color 0.2s;
        }

        .action_button:hover {
            background-color: #3182ce;
        }

        /* Button container styles */
        .button-container {
            position: sticky;
            left: 0;
            background: white;
            z-index: 1;
            padding: 10px;
            border-top: 1px solid #edf2f7;
            display: flex;
            gap: 10px;
        }

        /* Button styles */
        .button-container button {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            transition: background-color 0.2s;
        }

        .request_btn {
            background-color: #4299e1;
            color: white;
        }

        .request_btn:hover {
            background-color: #3182ce;
        }

        .workshop_btn {
            background-color: #48bb78;
            color: white;
        }

        .workshop_btn:hover {
            background-color: #38a169;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 800px;
        }

        .modal-header {
            padding-bottom: 15px;
            border-bottom: 1px solid #edf2f7;
            margin-bottom: 15px;
        }

        .modal-title {
            font-size: 18px;
            font-weight: 600;
            color: #2d3748;
        }

        .modal-body {
            margin-bottom: 15px;
        }

        .modal-footer {
            padding-top: 15px;
            border-top: 1px solid #edf2f7;
            text-align: right;
        }

        .btn-secondary {
            padding: 8px 16px;
            background-color: #718096;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-secondary:hover {
            background-color: #4a5568;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .table-container {
                margin: 10px;
            }

            .button-container {
                flex-direction: column;
            }

            .button-container button {
                width: 100%;
            }
        }

        /* Base table cell styles */
        .table td, .table th {
            padding: 2px 4px;  /* Reduced padding */
            font-size: 11px;   /* Smaller font size */
            height: 40px;      /* Reduced row height */
            vertical-align: middle;
            white-space: nowrap;
        }
         .table img {
            width: 50px;  /* Increased from 40px to 100px */
            height: 40px;  /* Kept height at 40px */
            /* object-fit: cover; */
            border-radius: 2px;
        }

        /* Image column */
        .table td:nth-child(2),
        .table th:nth-child(2) {
            padding: 0;
            width: 60px;  /* Increased from 40px to 100px */
        }

        /* Serial number column */
        .table td:nth-child(3), 
        .table th:nth-child(3) {
            width: 60px;       /* Serial number */
        }

        /* Shop name column */
        .table td:nth-child(4), 
        .table th:nth-child(4) {
            width: 80px;       /* Shop name */
        }

        /* Kind column */
        .table td:nth-child(5), 
        .table th:nth-child(5) {
            width: 60px;       /* Kind */
        }

        /* Model column */
        .table td:nth-child(6), 
        .table th:nth-child(6) {
            width: 70px;       /* Model */
        }

        /* Gold color column */
        .table td:nth-child(7), 
        .table th:nth-child(7) {
            width: 50px;       /* Gold color */
        }

        /* Metal type & purity columns */
        .table td:nth-child(8),
        .table td:nth-child(9),
        .table th:nth-child(8),
        .table th:nth-child(9) {
            width: 60px;       /* Metal type & purity */
        }

        /* Quantity column */
        .table td:nth-child(10), 
        .table th:nth-child(10) {
            width: 40px;       /* Quantity */
        }

        /* Weight column */
        .table td:nth-child(11), 
        .table th:nth-child(11) {
            width: 50px;       /* Weight */
        }

        /* Stars column */
        .table td:nth-child(12), 
        .table th:nth-child(12) {
            width: 40px;       /* Stars */
        }

        /* Stones column */
        .table td:nth-child(13), 
        .table th:nth-child(13) {
            width: 60px;       /* Stones */
        }

        /* Source column */
        .table td:nth-child(14), 
        .table th:nth-child(14) {
            width: 40px;       /* Source */
        }

        /* Average of stones column */
        .table td:nth-child(15), 
        .table th:nth-child(15) {
            width: 20px;       /* Average of stones */
        }

        /* Actions column */
        .table td:last-child, 
        .table th:last-child {
            width: 40px;       /* Actions */
        }

        /* Checkbox column */
        .table td:first-child,
        .table th:first-child {
            width: 50px;       /* Increased width to fit both elements */
            white-space: nowrap;
            padding: 2px 4px;
        }

        /* Ensure text doesn't wrap */
        .table td {
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Style for the actions container */
        .actions-container {
            display: flex;
            align-items: center;
            gap: 8px;         /* Space between checkbox and icon */
        }

        /* Style for edit icon */
        .edit-icon {
            color: #3182ce;   /* Blue color */
            font-size: 13px;  /* Slightly smaller */
            display: inline-flex;
            align-items: center;
            padding: 2px;
            transition: color 0.2s;
        }

        .edit-icon:hover {
            color: #2c5282;   /* Darker blue on hover */
        }

        /* Remove box around icon */
        .bi-pencil {
            line-height: 1;
        }
    </style>
</head>

<body>
    <div class="table-container">
        <form method="POST" action="{{ route('bulk-action') }}" id="bulkActionForm">
            @csrf
            <input type="hidden" name="action" value="delete">
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Actions</th>
                            <th>Image</th>
                            @php
                                // Array of columns with their display names
                                $columns = [
                                    'serial_number' => 'Serial Number',
                                    'shop_name' => 'Shop Name',
                                    'kind' => 'Kind',
                                    'model' => 'Model',
                                    'gold_color' => 'Gold Color',
                                    'weight' => 'Weight',
                                    'metal_type' => 'Metal Type',
                                    'metal_purity' => 'Metal Purity',
                                    'quantity' => 'Quantity',
                                    'stars' => 'stars',
                                    'source' => 'Source',
                                    'stones' => 'Stones',
                                    'average_of_stones' => 'Avg',
                                    // 'net_weight' => 'Net Weight',
                                ];
                            @endphp

                            @foreach ($columns as $field => $label)
                                <th>{{ $label }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($goldItems as $item)
                            <tr>
                                <td>
                                    <div class="actions-container">
                                        <input type="checkbox" name="selected_items[]" value="{{ $item->id }}" />
                                        <a href="{{ route('gold-items.edit', $item->id) }}" class="edit-icon">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    @if ($item->modelCategory && $item->modelCategory->scanned_image)
                                        <img src="{{ asset('storage/' . $item->modelCategory->scanned_image) }}" alt="Scanned Image">
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
                                <td>{{ $item->weight }}</td>
                                <td>{{ $item->metal_type }}</td>
                                <td>{{ $item->metal_purity }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ $item->modelCategory->stars ?? 'No stars' }}</td>
                                <td>{{ $item->source }}</td>
                                <td>{{ $item->stones }}</td>
                                <td>{{ $item->modelCategory->average_of_stones?? 'No avg' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="button-container">
                {{-- <button class="delete_btn" type="button" name="action" value="delete" form="bulkActionForm">Delete</button> --}}
                <button class="request_btn" type="submit" name="action" value="request">Request Item</button>
                <button class="workshop_btn" type="button" name="action" value="workshop" form="bulkActionForm"> Did</button>
            </div>
        </form>
    </div>
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
        document.addEventListener('DOMContentLoaded', function () {
            // Workshop transfer button handler
            document.addEventListener('click', function (e) {
                if (e.target.classList.contains('workshop_btn')) {
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
                                return reason;
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const reason = result.value;
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
    
                                // Create hidden input for items
                                const itemsInput = document.createElement('input');
                                itemsInput.type = 'hidden';
                                itemsInput.name = 'items';
                                itemsInput.value = JSON.stringify(mappedItems);
                                document.getElementById('bulkActionForm').appendChild(itemsInput);

                                // Set form method and action
                                document.getElementById('bulkActionForm').method = 'POST';
                                document.getElementById('bulkActionForm').action = "{{ route('workshop.requests.create') }}";

                                // Submit the form
                                document.getElementById('bulkActionForm').submit();
                            }
                        });
                    });
                }
            });
    
            // Model links handler
            const modelLinks = document.querySelectorAll('.model-link');
            modelLinks.forEach(link => {
                link.addEventListener('click', function (e) {
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
    
            // Delete button handler
            const deleteForm = document.getElementById('bulkActionForm');
            const deleteBtn = deleteForm?.querySelector('.delete_btn');
    
            if (deleteForm && deleteBtn) {
                deleteBtn.addEventListener('click', function (e) {
                    e.preventDefault();
    
                    const selectedItems = Array.from(deleteForm.querySelectorAll(
                        'input[name="selected_items[]"]:checked'));
    
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
    
                            deleteForm.submit();
                        }
                    });
                });
            }
        });
    </script>
</body>

</html>
