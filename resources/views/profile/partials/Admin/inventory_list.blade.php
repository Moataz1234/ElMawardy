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
            padding: 2px 4px;
            /* Reduced padding */
            font-size: 11px;
            /* Smaller font size */
            height: 40px;
            /* Reduced row height */
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
        .table td,
        .table th {
            padding: 2px 4px;
            /* Reduced padding */
            font-size: 11px;
            /* Smaller font size */
            height: 40px;
            /* Reduced row height */
            vertical-align: middle;
            white-space: nowrap;
        }

        .table img {
            width: 50px;
            /* Increased from 40px to 100px */
            height: 40px;
            /* Kept height at 40px */
            /* object-fit: cover; */
            border-radius: 2px;
        }

        /* Image column */
        .table td:nth-child(2),
        .table th:nth-child(2) {
            padding: 0;
            width: 60px;
            /* Increased from 40px to 100px */
        }

        /* Serial number column */
        .table td:nth-child(3),
        .table th:nth-child(3) {
            width: 60px;
            /* Serial number */
        }

        /* Shop name column */
        .table td:nth-child(4),
        .table th:nth-child(4) {
            width: 80px;
            /* Shop name */
        }

        /* Kind column */
        .table td:nth-child(5),
        .table th:nth-child(5) {
            width: 60px;
            /* Kind */
        }

        /* Model column */
        .table td:nth-child(6),
        .table th:nth-child(6) {
            width: 70px;
            /* Model */
        }

        /* Gold color column */
        .table td:nth-child(7),
        .table th:nth-child(7) {
            width: 50px;
            /* Gold color */
        }

        /* Metal type & purity columns */
        .table td:nth-child(8),
        .table td:nth-child(9),
        .table th:nth-child(8),
        .table th:nth-child(9) {
            width: 60px;
            /* Metal type & purity */
        }

        /* Quantity column */
        .table td:nth-child(10),
        .table th:nth-child(10) {
            width: 40px;
            /* Quantity */
        }

        /* Weight column */
        .table td:nth-child(11),
        .table th:nth-child(11) {
            width: 50px;
            /* Weight */
        }

        /* Stars column */
        .table td:nth-child(12),
        .table th:nth-child(12) {
            width: 40px;
            /* Stars */
        }

        /* Stones column */
        .table td:nth-child(13),
        .table th:nth-child(13) {
            width: 60px;
            /* Stones */
        }

        /* Source column */
        .table td:nth-child(14),
        .table th:nth-child(14) {
            width: 40px;
            /* Source */
        }

        /* Average of stones column */
        .table td:nth-child(15),
        .table th:nth-child(15) {
            width: 20px;
            /* Average of stones */
        }

        /* Actions column */
        .table td:last-child,
        .table th:last-child {
            width: 40px;
            /* Actions */
        }

        /* Checkbox column */
        .table td:first-child,
        .table th:first-child {
            width: 50px;
            /* Increased width to fit both elements */
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
            gap: 8px;
            /* Space between checkbox and icon */
        }

        /* Style for edit icon */
        .edit-icon {
            color: #3182ce;
            /* Blue color */
            font-size: 13px;
            /* Slightly smaller */
            display: inline-flex;
            align-items: center;
            padding: 2px;
            transition: color 0.2s;
        }

        .edit-icon:hover {
            color: #2c5282;
            /* Darker blue on hover */
        }

        /* Remove box around icon */
        .bi-pencil {
            line-height: 1;
        }

        /* Badge styles for request status */
        .requested-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #ef4444;
            color: white;
            font-size: 8px;
            padding: 2px 4px;
            border-radius: 10px;
            z-index: 5;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 500;
            text-align: center;
            margin-left: 5px;
        }

        .status-pending_kasr {
            background-color: #f97316;
            color: white;
        }

        .status-pending_sale {
            background-color: #3b82f6;
            color: white;
        }

        .print-icon {
            color: #4299e1;
            /* Blue color */
            font-size: 13px;
            /* Slightly smaller */
            display: inline-flex;
            align-items: center;
            padding: 2px;
            transition: color 0.2s;
        }

        .print-icon:hover {
            color: #2c5282;
            /* Darker blue on hover */
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
                                    'rest_since' =>'rest_since',
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
                                        {{-- <div style="position: relative;">
                                            <input type="checkbox" name="selected_items[]" value="{{ $item->id }}"
                                                {{ $item->status == 'pending_kasr' ? 'disabled' : '' }} />
                                            @if ($item->status == 'pending_kasr')
                                                <span class="status-badge status-pending_kasr">Requested</span>
                                            @elseif($item->status == 'pending_sale')
                                                <span class="status-badge status-pending_sale">Sale</span>
                                            @endif
                                        </div> --}}
                                        <a href="{{ route('gold-items.edit', $item->id) }}" class="edit-icon">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    {{-- </div> --}}
                                    {{-- <div class="actions-container"> --}}
                                        <!-- Existing actions like checkbox and edit icon -->
                                        <a href="{{ route('item.export.barcode', $item->id) }}" class="print-icon">
                                            <i class="bi bi-printer"></i>
                                        </a>   
                                    </div>
                                </td>
                                <td>
                                    @if ($item->modelCategory && $item->modelCategory->scanned_image)
                                        <img src="{{ asset('storage/' . $item->modelCategory->scanned_image) }}"
                                            alt="Scanned Image">
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
                                <td>{{ $item->rest_since ?? 'No stars' }}</td>
                                <td>{{ $item->source }}</td>
                                <td>{{ $item->stones }}</td>
                                <td>{{ $item->modelCategory->average_of_stones ?? 'No avg' }}</td>
                             
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="button-container">
                {{-- <button class="delete_btn" type="button" name="action" value="delete" form="bulkActionForm">Delete</button> --}}
                {{-- <button class="request_btn" type="submit" name="action" value="request">Request Item</button> --}}
                <button class="workshop_btn" type="button" name="action" value="workshop" form="bulkActionForm">
                    Request Item</button>
            </div>
        </form>
    </div>
    <!-- Model Details Modal -->
    <div class="modal fade" id="modelDetailsModal" tabindex="-1" role="dialog"
        aria-labelledby="modelDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modelDetailsModalLabel">
                        <i class="bi bi-list-ul me-2"></i>Items with Same Model
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" id="modal-body-content">
                    <div class="text-center p-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Model Selection Modal for Workshop Transfer -->
    <div class="modal fade" id="modelSelectionModal" tabindex="-1" role="dialog"
        aria-labelledby="modelSelectionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="modal-title" id="modelSelectionModalLabel">
                        <i class="bi bi-list-check me-2"></i>Select Items to Transfer (DID)
                    </h5>
                </div>
                <div class="modal-body p-3" style="max-height: 60vh; overflow-y: auto;">
                    {{-- <div class="alert alert-primary d-flex align-items-center">
                        <div>Review the items below and select which ones to transfer to the workshop. Reason: <strong>DID</strong></div>
                    </div> --}}
                    <div id="model-selection-content" class="mt-3">
                        <div class="text-center p-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination Controls -->
                    <div class="pagination-controls mt-4 d-flex justify-content-between align-items-center">
                        <div>
                            <button id="prev-page" class="btn btn-sm btn-outline-secondary" disabled>
                                <i class="bi bi-chevron-left"></i> Previous
                            </button>
                            <button id="next-page" class="btn btn-sm btn-outline-primary ms-2">
                                Next <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                        <div class="page-info">
                            <span class="badge bg-secondary">
                                Page <span id="current-page">1</span> of <span id="total-pages">1</span>
                            </span>
                            <span class="ms-2 text-muted">
                                (<span id="showing-items">0-0</span> of <span id="total-items">0</span> items)
                            </span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-primary" id="continue-transfer">
                        <i class="bi bi-check-circle me-1"></i> Accept
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Pagination variables for model selection modal
            let currentPage = 1;
            let itemsPerPage = 5;
            let allModelItems = [];
            let totalPages = 1;
            let modelTransferReason = '';
            let transferAllModelsOption = false;
            // Add a map to track selected item IDs
            let selectedItemIds = new Map();

            // Pagination control functions
            function updatePagination() {
                document.getElementById('current-page').textContent = currentPage;
                document.getElementById('total-pages').textContent = totalPages;

                const startItem = (currentPage - 1) * itemsPerPage + 1;
                const endItem = Math.min(currentPage * itemsPerPage, allModelItems.length);
                document.getElementById('showing-items').textContent = `${startItem}-${endItem}`;
                document.getElementById('total-items').textContent = allModelItems.length;

                // Enable/disable pagination buttons
                document.getElementById('prev-page').disabled = currentPage === 1;
                document.getElementById('next-page').disabled = currentPage === totalPages;

                displayItemsForCurrentPage();
            }

            function displayItemsForCurrentPage() {
                const startIndex = (currentPage - 1) * itemsPerPage;
                const endIndex = Math.min(startIndex + itemsPerPage, allModelItems.length);
                const currentItems = allModelItems.slice(startIndex, endIndex);

                let html = `
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 40px;" class="text-center">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="select-all-page">
                                            <label class="form-check-label" for="select-all-page"></label>
                                        </div>
                                    </th>
                                    <th>Serial Number</th>
                                    <th>Shop</th>
                                    <th>Gold Color</th>
                                    <th>Model</th>
                                    <th>Weight</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                currentItems.forEach(item => {
                    // Important: Check if status is pending_kasr and disable those items
                    const isDisabled = item.status === 'pending_kasr';
                    const statusText = isDisabled ? 'Already Requested' : '';
                    const statusBadge = isDisabled ?
                        '<span class="badge bg-warning text-dark">Requested</span>' : '';

                    // Check if this item is in our selected items map
                    const isSelected = !isDisabled && selectedItemIds.has(item.id.toString());

                    // Set color-coding for gold color
                    let colorClass = '';
                    if (item.gold_color === 'Yellow') colorClass = 'text-warning';
                    else if (item.gold_color === 'White') colorClass = 'text-secondary';
                    else if (item.gold_color === 'Rose') colorClass = 'text-danger';

                    html += `
                        <tr class="${isSelected ? 'table-active' : ''}">
                            <td class="text-center align-middle">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input model-item-checkbox" 
                                        id="item-${item.id}"
                                        data-id="${item.id}" 
                                        data-serial="${item.serial_number}"
                                        data-model="${item.model}"
                                        data-status="${item.status || ''}"
                                        data-gold-color="${item.gold_color || ''}"
                                        ${isDisabled ? 'disabled' : ''}
                                        ${isSelected ? 'checked' : ''}>
                                    <label class="form-check-label" for="item-${item.id}"></label>
                                </div>
                            </td>
                            <td class="align-middle"><strong>${item.serial_number}</strong></td>
                            <td class="align-middle">${item.shop_name || 'Admin'}</td>
                            <td class="align-middle ${colorClass}"><strong>${item.gold_color || ''}</strong></td>
                            <td class="align-middle">${item.model}</td>
                            <td class="align-middle">${item.weight} g</td>
                            <td class="align-middle">${statusBadge} ${statusText}</td>
                        </tr>
                    `;
                });

                html += `
                            </tbody>
                        </table>
                    </div>
                    <div class="alert alert-info mt-3 d-flex align-items-center">
                        <i class="bi bi-exclamation-circle me-2 fs-5"></i>
                        <div>
                            <strong>Note:</strong> Only checked items will be transferred. Items marked as "Already Requested" cannot be selected.
                        </div>
                    </div>
                `;

                document.getElementById('model-selection-content').innerHTML = html;

                // Add event listeners to track checkbox changes
                document.querySelectorAll('.model-item-checkbox').forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        const itemId = this.dataset.id;
                        if (this.checked) {
                            selectedItemIds.set(itemId, {
                                id: itemId,
                                serial: this.dataset.serial,
                                model: this.dataset.model,
                                gold_color: this.dataset.goldColor
                            });
                        } else {
                            selectedItemIds.delete(itemId);
                        }
                        console.log('Updated selected items:', Array.from(selectedItemIds
                    .values()));
                    });
                });

                // Add select all functionality for current page
                const selectAllCheckbox = document.getElementById('select-all-page');
                selectAllCheckbox.addEventListener('change', function() {
                    const checkboxes = document.querySelectorAll('.model-item-checkbox:not([disabled])');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                        const itemId = checkbox.dataset.id;
                        if (this.checked) {
                            selectedItemIds.set(itemId, {
                                id: itemId,
                                serial: checkbox.dataset.serial,
                                model: checkbox.dataset.model,
                                gold_color: checkbox.dataset.goldColor
                            });
                        } else {
                            selectedItemIds.delete(itemId);
                        }
                    });
                    console.log('Updated selected items after select all:', Array.from(selectedItemIds
                        .values()));
                });
            }

            // Set up pagination controls
            document.getElementById('next-page').addEventListener('click', function() {
                if (currentPage < totalPages) {
                    currentPage++;
                    updatePagination();
                }
            });

            document.getElementById('prev-page').addEventListener('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    updatePagination();
                }
            });

            // Add an initialization step to pre-check valid checkboxes
            function initializeSelectedItems() {
                selectedItemIds.clear();

                // We no longer automatically select all items
                // Let the user explicitly choose which items to transfer
                console.log('Initialized empty selection of items');
            }

            // Update continue-transfer button handler to use the map
            document.getElementById('continue-transfer').addEventListener('click', function() {
                console.log('Selected items from map:', Array.from(selectedItemIds.values()));

                if (selectedItemIds.size === 0) {
                    Swal.fire({
                        title: 'No Items Selected',
                        text: 'Please select at least one valid item to transfer.',
                        icon: 'warning',
                        confirmButtonColor: '#3085d6',
                    });
                    return false;
                }

                // Convert the map values to an array
                const selectedItems = Array.from(selectedItemIds.values());

                console.log('Final selected items for transfer:', selectedItems);
                console.log('Transfer reason:', modelTransferReason);
                console.log('Transfer all models option:', transferAllModelsOption);

                // Hide the selection modal
                $('#modelSelectionModal').modal('hide');

                // Continue with the transfer process with ONLY the selected items
                createTransferRequest(selectedItems, modelTransferReason, transferAllModelsOption);
            });

            // Function to create the actual transfer request
            function createTransferRequest(items, reason, transferAllModels) {
                console.log('Creating transfer request with:', {
                    items: items,
                    reason: reason || 'DID', // Use 'DID' as default if reason is not provided
                    transferAllModels: transferAllModels
                });

                const reasonInput = document.createElement('input');
                reasonInput.type = 'hidden';
                reasonInput.name = 'transfer_reason';
                reasonInput.value = reason || 'DID'; // Use 'DID' as default if reason is not provided
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
                itemsInput.value = JSON.stringify(items);
                document.getElementById('bulkActionForm').appendChild(itemsInput);

                console.log('Form data prepared:', {
                    transfer_reason: reason || 'DID', // Use 'DID' as default if reason is not provided
                    transfer_all_models: transferAllModels,
                    items: JSON.stringify(items)
                });

                // Set form method and action
                document.getElementById('bulkActionForm').method = 'POST';
                document.getElementById('bulkActionForm').action = "{{ route('workshop.requests.create') }}";

                // Use AJAX instead of form submission
                const formData = new FormData(document.getElementById('bulkActionForm'));

                console.log('Sending AJAX request...');

                fetch("{{ route('workshop.requests.create') }}", {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => {
                        console.log('Response received:', response);
                        return response.json();
                    })
                    .then(data => {
                        console.log('Response data:', data);
                        if (data.success) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Workshop transfer requests created successfully',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = "{{ route('admin.inventory') }}";
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: data.message || 'Something went wrong',
                                icon: 'error'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Failed to create workshop requests',
                            icon: 'error'
                        });
                    });
            }

            // Workshop transfer button handler
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('workshop_btn')) {
                    e.preventDefault();

                    const selectedItems = Array.from(document.querySelectorAll(
                        'input[name="selected_items[]"]:checked:not(:disabled)'));

                    if (selectedItems.length === 0) {
                        Swal.fire('Error', 'Please select items to transfer', 'error');
                        return;
                    }

                    const mappedItems = selectedItems.map(checkbox => {
                        const row = checkbox.closest('tr');
                        return {
                            id: checkbox.value,
                            serial: row.querySelector('td:nth-child(3)').textContent,
                            model: row.querySelector('td:nth-child(6)').textContent,
                            shop_name: row.querySelector('td:nth-child(4)').textContent,
                            weight: row.querySelector('td:nth-child(8)').textContent,
                            status: checkbox.disabled ? 'pending_kasr' : ''
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
                        const modelsToTransfer = transferAllModels ?
                            [...new Set(mappedItems.map(item => item.model))] :
                            null;

                        // Set automatic reason "DID"
                        const reason = "DID";
                        modelTransferReason = reason;
                        transferAllModelsOption = transferAllModels;

                        // If we need all items with the same models, fetch them
                        if (transferAllModels) {
                            // Show the modal with loading indicator
                            $('#modelSelectionModal').modal('show');

                            // Get all items with the selected models
                            const models = modelsToTransfer;
                            const promises = models.map(model =>
                                fetch(
                                    `/gold-items/same-model?model=${encodeURIComponent(model)}`)
                                .then(response => response.json())
                            );

                            Promise.all(promises)
                                .then(results => {
                                    let allItems = [];
                                    results.forEach(data => {
                                        if (data.items && data.items.length) {
                                            // Log the raw data received to help debug
                                            console.log('Raw data received for model:',
                                                data.items[0].model, data.items);

                                            // Make sure we properly identify items that can't be selected
                                            // and include gold_color in the processed items
                                            const processedItems = data.items.map(
                                                item => {
                                                    return {
                                                        ...item,
                                                        // Make sure status is properly set
                                                        status: item.status || null,
                                                        // Ensure gold_color is available
                                                        gold_color: item
                                                            .gold_color || ''
                                                    };
                                                });

                                            allItems = allItems.concat(processedItems);
                                        }
                                    });

                                    // Log the items with their attributes to help debugging
                                    console.log('Items with attributes:', allItems.map(item =>
                                ({
                                        id: item.id,
                                        serial: item.serial_number,
                                        status: item.status,
                                        gold_color: item.gold_color
                                    })));

                                    // Set the items for pagination
                                    allModelItems = allItems;
                                    totalPages = Math.ceil(allItems.length / itemsPerPage);
                                    currentPage = 1;

                                    // Initialize the selected items (now empty by default)
                                    initializeSelectedItems();

                                    // Update the pagination display
                                    updatePagination();
                                })
                                .catch(error => {
                                    console.error('Error fetching items:', error);
                                    document.getElementById('model-selection-content')
                                        .innerHTML =
                                        '<div class="alert alert-danger">Error loading items. Please try again.</div>';
                                });
                        } else {
                            // Just use the selected items
                            allModelItems = mappedItems;
                            totalPages = Math.ceil(allModelItems.length / itemsPerPage);
                            currentPage = 1;

                            // Show the modal with the items
                            $('#modelSelectionModal').modal('show');

                            // Update the pagination display
                            updatePagination();
                        }
                    });
                }
            });

            // Model links handler
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
                                        <table class="table table-bordered table-hover table-sm">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Shop Name</th>
                                                    <th>Serial Number</th>
                                                    <th>Weight</th>
                                                    <th>Gold Color</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                `;
                                data.items.forEach(item => {
                                    // Set color-coding for gold color
                                    let colorClass = '';
                                    if (item.gold_color === 'Yellow') colorClass =
                                        'text-warning';
                                    else if (item.gold_color === 'White') colorClass =
                                        'text-secondary';
                                    else if (item.gold_color === 'Rose') colorClass =
                                        'text-danger';

                                    // Status badge
                                    let statusBadge = '';
                                    if (item.status === 'pending_kasr') statusBadge =
                                        '<span class="badge bg-warning text-dark">Pending Workshop</span>';
                                    else if (item.status === 'sold') statusBadge =
                                        '<span class="badge bg-success">Sold</span>';

                                    htmlContent += `
                                        <tr>
                                            <td class="align-middle">${item.shop_name || 'Admin'}</td>
                                            <td class="align-middle"><strong>${item.serial_number}</strong></td>
                                            <td class="align-middle">${item.weight}g</td>
                                            <td class="align-middle ${colorClass}"><strong>${item.gold_color || ''}</strong></td>
                                            <td class="align-middle">${statusBadge || '<span class="badge bg-secondary">Available</span>'}</td>
                                        </tr>
                                    `;
                                });
                                htmlContent += `
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="alert alert-info mt-3 d-flex align-items-center">
                                        <i class="bi bi-info-circle me-2"></i>
                                        <div>
                                            <strong>Total:</strong> ${data.items.length} items with this model
                                        </div>
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
                deleteBtn.addEventListener('click', function(e) {
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
