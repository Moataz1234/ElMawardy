<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">

    @include('components.navbar')
</head>
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <form class="form-inline" id="filterForm">
                <div class="form-group mx-2">
                    <label for="filter_date" class="mr-2">Select Date:</label>
                    <input type="date" class="form-control" id="filter_date" name="filter_date" 
                           value="{{ request('filter_date', date('Y-m-d')) }}">
                </div>
                <div class="form-group mx-2">
                    <label for="status" class="mr-2">Status:</label>
                    <select class="form-control" id="status" name="status">
                        <option value="pending" {{ request('status', 'pending') === 'pending' ? 'selected' : '' }}> Pending Sale</option>
                        {{-- <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved </option> --}}
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected </option>
                        {{-- <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>All</option> --}}
                    </select>
                </div>
                <button type="submit" class="btn btn-primary mx-2">Filter</button>
                <button type="button" id="exportExcel" class="btn btn-success">Export to Excel</button>
            </form>
        </div>
    </div>
    <table class="table table-striped table-hover">
        <thead class="thead-dark">
            <tr>
                <th><input type="checkbox" id="selectAll" /></th>
                <th>Serial Number</th>
                <th>Shop Name</th>
                <th>Weight</th>
                <th>Price</th>
                <th>Price/Gram</th>
                <th>Payment Method</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Sold Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($soldItemRequests as $request)
                @php
                    $weight = $request->item_type === 'pound' ? $request->weight : ($request->goldItem->weight ?? 0);
                    $pricePerGram = $weight > 0 ? round($request->price / $weight, 2) : 0;
                @endphp
                <tr>
                    <td>
                        @if ($request->status === 'pending')
                            <input type="checkbox" name="requests[]" value="{{ $request->id }}" class="request-checkbox" />
                        @endif
                    </td>
                    <td>
                        <div class="item-container">
                            <!-- Serial number links to item details -->
                            <a href="#" class="item-details text-primary" 
                               data-toggle="modal" 
                               data-target="#itemModal_{{ $request->id }}">
                                {{ $request->item_serial_number }}
                            </a>
                            <!-- + badge for pound details -->
                            @if ($request->associatedPound)
                                <a href="#" class="badge badge-info ml-2" data-toggle="collapse" 
                                   data-target="#poundDetails_{{ $request->id }}">+</a>
                            @endif
                        </div>
                        <!-- Pound details collapse -->
                        @if ($request->associatedPound)
                            <div id="poundDetails_{{ $request->id }}" class="collapse mt-2">
                                <div class="card card-body">
                                    <p><strong>Pound Serial:</strong> {{ $request->associatedPound->item_serial_number }}</p>
                                    <p><strong>Weight:</strong> {{ $request->associatedPound->weight }}g</p>
                                    <p><strong>Purity:</strong> {{ $request->associatedPound->purity }}K</p>
                                    <p><strong>Price:</strong> {{ $request->associatedPound->price }} {{ config('app.currency') }}</p>
                                </div>
                            </div>
                        @endif
                    </td>
{{--                         
                        <a href="#" class="item-details text-primary" 
                           data-serial="{{ $request->item_serial_number }}"
                           data-toggle="modal" 
                           data-target="#itemModal_{{ $request->id }}">
                            {{ $request->item_serial_number }}
                        </a>
                        @if($request->item_type === 'pound')
                            <span class="badge badge-info">Pound</span>
                        @endif
                         --}}
                        <!-- Modal for this specific item -->
                        <div class="modal fade" id="itemModal_{{ $request->id }}" tabindex="-1" role="dialog">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content d-flex">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title">
                                            @if($request->item_type === 'pound')
                                                Pound Details
                                            @else
                                                Item Details
                                            @endif
                                        </h5>
                                        <button type="button" class="close text-white" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body d-flex justify-content-between">
                                        @if($request->item_type === 'pound')
                                            <!-- Pound Details -->
                                            {{-- <div class="row"> --}}
                                                <div class="col-md-5">
                                                    <h6 class="font-weight-bold">Pound Information</h6>
                                                    <p><strong>Serial Number:</strong> {{ $request->item_serial_number }}</p>
                                                    <p><strong>Type:</strong> {{ $request->kind ?? 'N/A' }}</p>
                                                    <p><strong>Weight:</strong> {{ $request->weight ?? 'N/A' }}g</p>
                                                    <p><strong>Purity:</strong> {{ $request->purity ?? 'N/A' }}K</p>
                                                    <p><strong>Price:</strong> {{ $request->price }} {{ config('app.currency') }}</p>
                                                    <p><strong>Price/Gram:</strong> {{ $pricePerGram }} {{ config('app.currency') }}g</p>
                                                </div>
                                                {{-- <div class="col-md-6">
                                                    <h6 class="font-weight-bold">Item Information</h6>
                                                    <p><strong>Serial Number:</strong> {{ $request->goldItem->serial_number ?? 'N/A' }}</p>
                                                    <p><strong>Kind:</strong> {{ $request->goldItem->kind ?? 'N/A' }}</p>
                                                    <p><strong>Model:</strong> {{ $request->goldItem->model ?? 'N/A' }}</p>
                                                    <p><strong>Gold Color:</strong> {{ $request->goldItem->gold_color ?? 'N/A' }}</p>
                                                    <p><strong>Weight:</strong> {{ $request->goldItem->weight ?? 'N/A' }}</p>
                                                    <p><strong>Metal Purity:</strong> {{ $request->goldItem->metal_purity ?? 'N/A' }}</p>
                                                </div> --}}
                                            {{-- </div> --}}
                                        @else
                                            <!-- Regular Item Details -->
                                            {{-- <div class="row"> --}}
                                                <div class="col-md-5">
                                                    <h6 class="font-weight-bold">Item Information</h6>
                                                    <p><strong>Serial Number:</strong> {{ $request->goldItem->serial_number ?? 'N/A' }}</p>
                                                    <p><strong>Kind:</strong> {{ $request->goldItem->kind ?? 'N/A' }}</p>
                                                    <p><strong>Model:</strong> {{ $request->goldItem->model ?? 'N/A' }}</p>
                                                    <p><strong>Gold Color:</strong> {{ $request->goldItem->gold_color ?? 'N/A' }}</p>
                                                    <p><strong>Weight:</strong> {{ $request->goldItem->weight ?? 'N/A' }}g</p>
                                                    <p><strong>Metal Purity:</strong> {{ $request->goldItem->metal_purity ?? 'N/A' }}</p>
                                                    <p><strong>Price:</strong> {{ $request->price }} {{ config('app.currency') }}</p>
                                                    <p><strong>Price/Gram:</strong> {{ $pricePerGram }} {{ config('app.currency') }}/g</p>
                                                </div>
                                                
                                                <!-- Show associated pound if exists -->
                                                @php
                                                    $poundRequest = null;
                                                    if ($request->goldItem && in_array($request->goldItem->model, [
                                                        '5-1416', '1-1068', '5-1338-C', '2-1928', '5-1290',
                                                        '2-1899', '5-1369', '1-1291',
                                                        '9-0194', '7-1329', '7-1013-A', '4-0854', '5-1370', '7-1386'
                                                    ])) {
                                                        $poundRequest = \App\Models\SaleRequest::where('item_serial_number', $request->item_serial_number)
                                                            ->where('item_type', 'pound')
                                                            ->first();
                                                    }
                                                @endphp
                                                
                                                @if($poundRequest)
                                                    <div class="col-md-6">
                                                        <h6 class="font-weight-bold">Associated Pound</h6>
                                                        <p><strong>Status:</strong> {{ $poundRequest->status }}</p>
                                                        <p><strong>Price:</strong> {{ $poundRequest->price }} {{ config('app.currency') }}</p>
                                                        <p><strong>Weight:</strong> {{ $poundRequest->weight }}g</p>
                                                        <p><strong>Purity:</strong> {{ $poundRequest->purity }}K</p>
                                                    </div>
                                                @endif
                                            {{-- </div> --}}
                                        @endif

                                        @if($request->customer)
                                            <div class="row mt-3">
                                                <div class="col-6">
                                                    <h6 class="font-weight-bold">Customer Information</h6>
                                                    <p><strong>Name:</strong> {{ $request->customer->first_name }} {{ $request->customer->last_name }}</p>
                                                    <p><strong>Phone:</strong> {{ $request->customer->phone_number ?? 'N/A' }}</p>
                                                    <p><strong>Email:</strong> {{ $request->customer->email ?? 'N/A' }}</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>{{ $request->shop_name }}</td>
                    <td>{{ $weight }}g</td>
                    <td>{{ $request->price }} {{ config('app.currency') }}</td>
                    <td>{{ $pricePerGram }} {{ config('app.currency') }}/g</td>
                    <td>{{ $request->payment_method ?? 'N/A' }}</td>
                    <td>
                        @if($request->status === 'pending')
                            <span class="badge badge-warning p-2">Pending Sale</span>
                        @elseif($request->status === 'approved')
                            <span class="badge badge-success p-2">Approved</span>
                        @elseif($request->status === 'rejected')
                            <span class="badge badge-danger p-2">Rejected</span>
                        @endif
                    </td>
                    <td>{{ $request->created_at->format('Y-m-d H:i:s') }}</td>
                    <td>{{ $request->sold_date }}</td>
                    <td>
                        @if ($request->status === 'pending')
                            {{-- <button class="btn btn-success btn-sm approve-btn" data-request-id="{{ $request->id }}">
                                Approve
                            </button> --}}
                            <button class="btn btn-danger btn-sm reject-btn" data-request-id="{{ $request->id }}">
                                Reject
                            </button>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <button type="button" id="approveAll" class="btn btn-success mx-2">Approve Selected</button>
</div>

<script>
$(document).ready(function() {
    // Filter form submission
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        let filterDate = $('#filter_date').val();
        let status = $('#status').val();
        window.location.href = `${window.location.pathname}?filter_date=${filterDate}&status=${status}`;
    });

    // Export to Excel
    $('#exportExcel').click(function() {
        let filterDate = $('#filter_date').val();
        let status = $('#status').val();
        window.location.href = `/export-sales?filter_date=${filterDate}&status=${status}`;
    });
});
</script>
<script>
    $(document).ready(function() {
        // Existing code for filter and export
    
        // Checkbox for selecting all
        $('#selectAll').on('click', function() {
            $('.request-checkbox').prop('checked', this.checked);
        });
    
        // Single approval
        $('.approve-btn').on('click', function(e) {
            e.preventDefault();
            const requestId = $(this).data('request-id');
            
            console.log('Approving request:', requestId);
            
            approveRequests([requestId]);
        });

        // Bulk approval
        $('#approveAll').on('click', function() {
            var selectedRequests = [];

            $('.request-checkbox:checked').each(function() {
                const row = $(this).closest('tr');
                selectedRequests.push($(this).val());
            });

            console.log('Selected requests:', selectedRequests);

            if (selectedRequests.length > 0) {
                approveRequests(selectedRequests);
            } else {
                alert('Please select at least one request to approve.');
            }
        });

        function approveRequests(requestIds) {
            console.log('Sending approval request:', { requests: requestIds });

            $.ajax({
                url: "{{ route('sell-requests.bulk-approve') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    requests: requestIds
                },
                success: function(response) {
                    console.log('Approval response:', response);
                    if (response.success) {
                        alert('Selected requests have been approved.');
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    console.error('Approval error:', xhr.responseText);
                    alert('An error occurred while approving requests.');
                }
            });
        }

        // Add reject functionality
        $('.reject-btn').on('click', function() {
            const requestId = $(this).data('request-id');
            if (confirm('Are you sure you want to reject this request?')) {
                $.ajax({
                    url: `/Acc_sell_requests/${requestId}/reject`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        alert('Request rejected successfully');
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('Error rejecting request');
                    }
                });
            }
        });
    });
</script>
