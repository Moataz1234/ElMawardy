<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Gold Inventory</title>
    {{-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> --}}
    <link href="{{ asset('css/Gold/shops_inventory.css') }}" rel="stylesheet">
   
</head>
<body>
    <div class="container-fluid">
        <div class="spreadsheet">
            <div class="table-responsive">
                <!-- Forms for Sell and Transfer -->
                <form id="sellForm" action="{{ route('shop-items.bulkSell') }}" method="POST" style="display: none;">
                    @csrf
                    <input type="hidden" name="ids" id="selectedIdsForSell">
                </form>

                <form id="transferForm" action="{{ route('gold-items.bulk-transfer') }}" method="POST" style="display: none;">
                    @csrf
                    <input type="hidden" name="ids" id="selectedIdsForTransfer">
                </form>
                <div class="button-container">
                    <button id="sellItemsButton" class="btn btn-primary">بيع</button>
                    <button id="transferItemsButton" class="btn btn-danger">تحويل</button>
                    <button id="workshopButton" class="btn btn-warning">Workshop</button>
                </div>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Select</th>
                            <th>Image</th>
                            <th>Serial Number</th>
                            <th>Shop Name</th>
                            <th>Kind</th>
                            <th>Model</th>
                            <th>Gold Color</th>
                            <th>Weight</th>
                            <th>Stars</th>
                        </tr>
                    </thead>
                    <tbody id="table-body">
                        @foreach ($goldItems as $item)
                            @php
                                $isOuter = \App\Models\Outer::where('gold_serial_number', $item->serial_number)
                                    ->where('is_returned', false)
                                    ->exists();
                                $isPending = \App\Models\TransferRequest::where('gold_item_id', $item->id)
                                    ->where('status', 'pending')
                                    ->exists();
                            @endphp
                            <tr style="{{ $isOuter ? 'background-color: yellow;' : '' }}">
                                <td>
                                    @if($isPending)
                                        <span class="pending-badge" style="font-size: 16px;">في انتظار  الموافقة على التحويل</span>
                                    @elseif($item->status === 'pending_sale')
                                        <span class="pending-badge" style="font-size: 16px;">في انتظار الموافقة على البيع</span>
                                    @elseif($item->status === 'pending_workshop' || $item->status === 'pending_kasr')
                                        <span class="pending-badge" style="font-size: 16px;">في انتظار الموافقة على الكسر</span>
                                    @else
                                        <input type="checkbox" class="select-item" data-id="{{ $item->id }}" data-serial="{{ $item->serial_number }}" data-model="{{ $item->model }}">
                                    @endif
                                </td>
                                <td>
                                    @if ($item->modelCategory && $item->modelCategory->scanned_image)
                                        <img src="{{ asset('storage/' . $item->modelCategory->scanned_image) }}"
                                            alt="Scanned Image" width="50">
                                    @else
                                        No Image
                                    @endif
                                </td>
                                <td>{{ $item->serial_number }}</td>
                                <td>{{ $item->shop_name }}</td>
                                <td>{{ $item->kind }}</td>
                                <td>
                                    <a href="#" class="model-link" data-model="{{ $item->model }}">
                                        {{ $item->model }}
                                    </a>
                                </td>
                                <td>{{ $item->gold_color }}</td>
                                <td>{{ $item->weight }}</td>
                                <td>{{ $item->modelCategory->stars ?? 'No Stars' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

              
            </div>
        </div>
        <!-- Model Details Modal -->
        <div class="modal " id="modelDetailsModal" tabindex="-1" role="dialog"
            aria-labelledby="modelDetailsModalLabel" aria-hidden="true">
            <div style="background-color: #babfc5;margin:10px 250px" class="modal-content">
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
        <!-- Add this modal for pound sales -->
        <div class="modal" id="poundSaleModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Associated Gold Pounds</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>The following gold pounds are associated with the sold items:</p>
                        <div id="poundsList"></div>
                        <form id="poundSaleForm" action="{{ route('gold-pounds.sell') }}" method="POST">
                            @csrf
                            <input type="hidden" name="customer_id" id="customerIdInput">
                            <div id="poundInputs"></div>
                            <button type="submit" class="btn btn-primary">Sell Associated Pounds</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Required JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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

            @if (isset($cleared_items))
                const clearedData = @json($cleared_items);
                const currentTime = Math.floor(Date.now() / 1000);

                // Only clear if the data is recent (within last 5 seconds)
                if (currentTime - clearedData.timestamp < 5) {
                    clearedData.items.forEach(itemId => {
                        const checkbox = document.querySelector(`.select-item[data-id="${itemId}"]`);
                        if (checkbox) {
                            checkbox.checked = false;
                        }
                    });
                }

                localStorage.removeItem('selectedItems');
            @endif

            document.getElementById('sellForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.associatedPounds && data.associatedPounds.length > 0) {
                        // Populate the pounds modal
                        const poundsList = document.getElementById('poundsList');
                        const poundInputs = document.getElementById('poundInputs');
                        poundsList.innerHTML = '';
                        poundInputs.innerHTML = '';
                        
                        data.associatedPounds.forEach(pound => {
                            poundsList.innerHTML += `<p>Serial Number: ${pound.serial_number}</p>`;
                            poundInputs.innerHTML += `
                                <input type="hidden" name="serial_numbers[]" value="${pound.serial_number}">
                                <div class="form-group">
                                    <label>Price for ${pound.serial_number}</label>
                                    <input type="number" name="prices[${pound.serial_number}]" class="form-control" required>
                                </div>
                            `;
                        });
                        
                        document.getElementById('customerIdInput').value = data.customer_id;
                        $('#poundSaleModal').modal('show');
                    } else {
                        window.location.reload();
                    }
                });
            });

            // Add workshop button handler
            document.getElementById('workshopButton').addEventListener('click', function() {
                const selectedItems = document.querySelectorAll('.select-item:checked');
                if (selectedItems.length === 0) {
                    alert('Please select items to transfer to workshop');
                    return;
                }

                // Populate the modal with selected items
                const itemsList = document.getElementById('workshop-items-list');
                itemsList.innerHTML = '';
                
                selectedItems.forEach(item => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>
                            <input type="checkbox" class="workshop-item-checkbox" checked 
                                data-id="${item.dataset.id}" 
                                data-serial="${item.dataset.serial}" 
                                data-model="${item.dataset.model}">
                        </td>
                        <td>${item.dataset.serial}</td>
                        <td>${item.dataset.model}</td>
                    `;
                    itemsList.appendChild(row);
                });
                
                // Show the modal
                $('#workshopItemsModal').modal('show');
            });
            
            // Add confirm workshop button handler
            document.getElementById('confirm-workshop-btn').addEventListener('click', function() {
                const selectedCheckboxes = document.querySelectorAll('.workshop-item-checkbox:checked');
                if (selectedCheckboxes.length === 0) {
                    alert('Please select at least one item');
                    return;
                }
                
                const reason = document.getElementById('workshop-reason').value;
                if (!reason || reason.trim() === '') {
                    alert('Please enter a reason for transfer');
                    return;
                }
                
                // Get selected item IDs
                const selectedIds = Array.from(selectedCheckboxes).map(checkbox => checkbox.dataset.id);
                
                // Create form data
                const formData = new FormData();
                formData.append('items', JSON.stringify(selectedIds));
                formData.append('transfer_reason', reason);
                formData.append('_token', '{{ csrf_token() }}');
                
                // Send request
                fetch('{{ route("workshop.requests.create") }}', {
                    method: 'POST',
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Workshop transfer request submitted successfully');
                        window.location.reload();
                    } else {
                        alert('Failed to submit request: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to submit request');
                });
                
                // Close the modal
                $('#workshopItemsModal').modal('hide');
            });
        });
    </script>

    <!-- Workshop Items Confirmation Modal -->
    <div class="modal" id="workshopItemsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Workshop Items</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>The following items will be sent to workshop:</p>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Select</th>
                                    <th>Serial Number</th>
                                    <th>Model</th>
                                </tr>
                            </thead>
                            <tbody id="workshop-items-list">
                                <!-- Items will be inserted here -->
                            </tbody>
                        </table>
                    </div>
                    <div class="form-group mt-4">
                        <label for="workshop-reason">Reason for transfer:</label>
                        <textarea id="workshop-reason" class="form-control" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" id="confirm-workshop-btn" class="btn btn-warning">Submit Request</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>