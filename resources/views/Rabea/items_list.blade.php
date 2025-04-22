<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Rabea Items Inventory</title>

    <!-- CSS -->
    @include('components.navbar')
    {{-- @include('sidebars.user-sidebar') --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="{{ asset('css/sidebar.css') }}" rel="stylesheet">
    <link href="{{ asset('css/navbar.css') }}" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            border: none;
        }

        .card-header {
            border-radius: 8px 8px 0 0 !important;
        }

        .table-responsive {
            border-radius: 4px;
            overflow: hidden;
        }

        .model-link {
            color: #3490dc;
            text-decoration: none;
            font-weight: 500;
        }

        .model-link:hover {
            text-decoration: underline;
        }

        .button-container {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .content-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
            padding: 20px;
            margin-left: 250px;
            /* Adjust based on your sidebar width */
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>
    <!-- Include navbar and sidebar components -->

    <div class="content-wrapper">
        <div class="main-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h4><i class="bi bi-list-ul me-2"></i>Rabea Items Inventory</h4>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info d-flex align-items-center">
                                    <i class="bi bi-info-circle me-2 fs-5"></i>
                                    <div>Use the buttons below to manage items: <strong>DID</strong> sends items
                                        directly to workshop, <strong>Transfer</strong> sends items to another shop
                                    </div>
                                </div>

                                <!-- Forms for actions -->
                                <form id="didForm" action="{{ route('rabea.did.requests.handle') }}" method="POST"
                                    style="display: none;">
                                    @csrf
                                    <input type="hidden" name="items" id="selectedItemsForDID">
                                    <input type="hidden" name="transfer_reason" value="DID">
                                    <input type="hidden" name="transfer_all_models" value="false">
                                </form>

                                {{-- <form id="transferForm" action="{{ route('gold-items.bulk-transfer') }}" method="POST" style="display: none;">
                                    @csrf
                                    <input type="hidden" name="ids" id="selectedIdsForTransfer">
                                </form> --}}

                                <div class="button-container mb-3">
                                    <button id="didButton" class="btn btn-warning">
                                        <i class="bi bi-tools me-1"></i> DID
                                    </button>
                                    <button id="transferButton" class="btn btn-primary">
                                        <i class="bi bi-arrow-right-circle me-1"></i> Transfer to Shop
                                    </button>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped table-hover table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 40px;" class="text-center">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" id="select-all">
                                                        <label class="form-check-label" for="select-all"></label>
                                                    </div>
                                                </th>
                                                <th>Image</th>
                                                <th>Serial Number</th>
                                                <th>Kind</th>
                                                <th>Model</th>
                                                <th>Gold Color</th>
                                                <th>Weight</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($goldItems as $item)
                                                @php
                                                    $isDisabled =
                                                        $item->status === 'pending_kasr' ||
                                                        $item->status === 'pending_sale' ||
                                                        $item->status === 'pending_workshop';
                                                    $statusText = '';
                                                    $statusClass = '';

                                                    if ($item->status === 'pending_kasr') {
                                                        $statusText = 'Pending Workshop';
                                                        $statusClass = 'bg-warning text-dark';
                                                    } elseif ($item->status === 'pending_sale') {
                                                        $statusText = 'Pending Sale';
                                                        $statusClass = 'bg-info text-white';
                                                    } elseif ($item->status === 'pending_workshop') {
                                                        $statusText = 'Pending Workshop';
                                                        $statusClass = 'bg-warning text-dark';
                                                    }

                                                    // Color class for gold color
                                                    $colorClass = '';
                                                    if ($item->gold_color === 'Yellow') {
                                                        $colorClass = 'text-warning';
                                                    } elseif ($item->gold_color === 'White') {
                                                        $colorClass = 'text-secondary';
                                                    } elseif ($item->gold_color === 'Rose') {
                                                        $colorClass = 'text-danger';
                                                    }
                                                @endphp
                                                <tr>
                                                    <td class="text-center align-middle">
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input select-item"
                                                                data-id="{{ $item->id }}"
                                                                data-serial="{{ $item->serial_number }}"
                                                                data-model="{{ $item->model }}"
                                                                data-gold-color="{{ $item->gold_color }}"
                                                                {{ $isDisabled ? 'disabled' : '' }}>
                                                            <label class="form-check-label"
                                                                for="item-{{ $item->id }}"></label>
                                                        </div>
                                                    </td>
                                                    <td class="align-middle">
                                                        @if ($item->modelCategory && $item->modelCategory->scanned_image)
                                                            <img src="{{ asset('storage/' . $item->modelCategory->scanned_image) }}"
                                                                alt="Item Image" width="50" class="img-thumbnail">
                                                        @else
                                                            <span class="badge bg-secondary">No Image</span>
                                                        @endif
                                                    </td>
                                                    <td class="align-middle">
                                                        <strong>{{ $item->serial_number }}</strong></td>
                                                    <td class="align-middle">{{ $item->kind }}</td>
                                                    <td class="align-middle">
                                                        <a href="#" class="model-link"
                                                            data-model="{{ $item->model }}">
                                                            {{ $item->model }}
                                                        </a>
                                                    </td>
                                                    <td class="align-middle {{ $colorClass }}">
                                                        <strong>{{ $item->gold_color }}</strong></td>
                                                    <td class="align-middle">{{ $item->weight }} g</td>
                                                    <td class="align-middle">
                                                        @if ($statusText)
                                                            <span
                                                                class="badge {{ $statusClass }}">{{ $statusText }}</span>
                                                        @else
                                                            <span class="badge bg-success">Available</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="pagination-container mt-3">
                                    {{ $goldItems->links('pagination::bootstrap-5') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Add this modal to your items_list.blade.php file, just before the closing </body> tag -->

    <!-- Transfer Modal -->
    <div class="modal fade" id="transferModal" tabindex="-1" aria-labelledby="transferModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="transferModalLabel">
                        <i class="bi bi-arrow-right-circle me-2"></i>Transfer Items to Shop
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info d-flex align-items-center">
                        <i class="bi bi-info-circle me-2 fs-5"></i>
                        <div>Select a shop to transfer the selected items</div>
                    </div>

                    <form id="modalTransferForm">
                        @csrf
                        <div id="selectedItemsCount" class="mb-3"></div>

                        <div class="mb-3">
                            <label for="destination_shop" class="form-label">Destination Shop:</label>
                            <select class="form-select" id="destination_shop" name="shop_name" required>
                                <option value="">Select shop</option>
                                <!-- Shops will be loaded via AJAX -->
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-primary" id="confirmTransferBtn">
                        <i class="bi bi-send me-1"></i> Transfer Items
                    </button>
                </div>
            </div>
        </div>
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

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Select all checkbox functionality
            const selectAllCheckbox = document.getElementById('select-all');
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    const checkboxes = document.querySelectorAll('.select-item:not([disabled])');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                });
            }

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
                        '<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';

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

                    // Show the modal using Bootstrap 5
                    const modelDetailsModal = new bootstrap.Modal(document.getElementById(
                        'modelDetailsModal'));
                    modelDetailsModal.show();
                });
            });

            // DID Button Handler
            // DID Button Handler - Update this in your view file
            const didButton = document.getElementById('didButton');
            if (didButton) {
                didButton.addEventListener('click', function() {
                    const selectedItems = Array.from(document.querySelectorAll('.select-item:checked'));

                    if (selectedItems.length === 0) {
                        Swal.fire('Error', 'Please select items to send to workshop', 'error');
                        return;
                    }

                    // Prepare items array in the format expected by the controller
                    const itemsForDID = selectedItems.map(checkbox => {
                        return {
                            id: checkbox.dataset.id,
                            serial: checkbox.dataset.serial,
                            model: checkbox.dataset.model,
                            gold_color: checkbox.dataset.goldColor
                        };
                    });

                    // Confirm action
                    Swal.fire({
                        title: 'Send Items to Workshop',
                        html: `
                <div class="text-start">
                    <p>You are about to send <strong>${selectedItems.length}</strong> items directly to workshop.</p>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        This action will remove the items from inventory immediately.
                    </div>
                </div>
            `,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, send to workshop',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Get the form
                            const form = document.getElementById('didForm');

                            // Set the items and submit the form
                            document.getElementById('selectedItemsForDID').value = JSON.stringify(
                                itemsForDID);

                            // Add the action input for approve - remove existing one first if it exists
                            let existingAction = form.querySelector('input[name="action"]');
                            if (existingAction) {
                                form.removeChild(existingAction);
                            }

                            let actionInput = document.createElement('input');
                            actionInput.type = 'hidden';
                            actionInput.name = 'action';
                            actionInput.value = 'approve';
                            form.appendChild(actionInput);

                            // Submit the form
                            form.submit();
                        }
                    });
                });
            }

            // Transfer Button Handler - Update this in your items_list.blade.php file
            // Add this JavaScript to your items_list.blade.php file, inside the existing script tags

            // Transfer Button Handler - Replace the existing transfer button handler with this
            const transferButton = document.getElementById('transferButton');
            if (transferButton) {
                transferButton.addEventListener('click', function() {
                    const selectedItems = Array.from(document.querySelectorAll('.select-item:checked'));

                    if (selectedItems.length === 0) {
                        Swal.fire('Error', 'Please select items to transfer', 'error');
                        return;
                    }

                    // Get selected item IDs
                    const selectedIds = selectedItems.map(checkbox => checkbox.dataset.id);

                    // Update the count in the modal
                    document.getElementById('selectedItemsCount').innerHTML = `
            <div class="alert alert-success">
                <strong>${selectedItems.length}</strong> items selected for transfer
            </div>
        `;

                    // Fetch shops for dropdown
                    fetch('/rabea/get-shops', {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .content,
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            const shopSelect = document.getElementById('destination_shop');
                            shopSelect.innerHTML = '<option value="">Select shop</option>';

                            data.shops.forEach(shop => {
                                const option = document.createElement('option');
                                option.value = shop;
                                option.textContent = shop;
                                shopSelect.appendChild(option);
                            });

                            // Store selected IDs in the button's data attribute
                            document.getElementById('confirmTransferBtn').dataset.selectedIds = JSON
                                .stringify(selectedIds);

                            // Show the modal
                            const transferModal = new bootstrap.Modal(document.getElementById(
                                'transferModal'));
                            transferModal.show();
                        })
                        .catch(error => {
                            console.error('Error fetching shops:', error);
                            Swal.fire('Error', 'Failed to load shops. Please try again.', 'error');
                        });
                });
            }

            // Handle the confirm transfer button click
            document.getElementById('confirmTransferBtn').addEventListener('click', function() {
                const destinationShop = document.getElementById('destination_shop').value;

                if (!destinationShop) {
                    Swal.fire('Error', 'Please select a destination shop', 'error');
                    return;
                }

                // Get the selected IDs from the data attribute
                const selectedIds = JSON.parse(this.dataset.selectedIds);

                // Disable the button and show loading state
                this.disabled = true;
                this.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Processing...';

                // Process the transfer via AJAX
                fetch('/rabea/process-transfer', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            item_ids: selectedIds,
                            shop_name: destinationShop
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Hide the modal
                        bootstrap.Modal.getInstance(document.getElementById('transferModal')).hide();

                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: data.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                // Reload the page to refresh the item list
                                window.location.reload();
                            });
                        } else {
                            throw new Error(data.message || 'Transfer failed');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);

                        // Re-enable the button
                        this.disabled = false;
                        this.innerHTML = '<i class="bi bi-send me-1"></i> Transfer Items';

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message ||
                                'An error occurred while processing the transfer'
                        });
                    });
            });
        });
    </script>
</body>

</html>
