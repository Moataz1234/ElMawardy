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
                            <th>Category</th>
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
                                        <span class="pending-badge">Pending Transfer</span>
                                    @elseif($item->sale_request && $item->sale_request->status === 'pending')
                                        <span class="pending-badge">Pending Sale Approval</span>
                                    @else
                                        <input type="checkbox" class="select-item" data-id="{{ $item->id }}">
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
                                <td>{{ $item->shop->name }}</td>
                                <td>{{ $item->kind }}</td>
                                <td>
                                    <a href="#" class="model-link" data-model="{{ $item->model }}">
                                        {{ $item->model }}
                                    </a>
                                </td>
                                <td>{{ $item->gold_color }}</td>
                                <td>{{ $item->weight }}</td>
                                <td>{{ $item->modelCategory->stars ?? 'No Category' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="button-container">
                    <button id="sellItemsButton" class="btn btn-primary">Sell</button>
                    <button id="transferItemsButton" class="btn btn-danger">Transfer</button>
                </div>
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
        });
    </script>
</body>
</html>