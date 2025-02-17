<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- @include('components.navbar') --}}
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
                        <option value="pending" {{ request('status', 'pending') === 'pending' ? 'selected' : '' }}>Pending Sale</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>All</option>
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
                <th>Serial Number</th>
                <th>Shop Name</th>
                <th>Weight</th>
                <th>Price</th>
                <th>Price/Gram</th>
                <th>Payment Method</th>
                <th>Status</th>
                <th>Date</th>
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
                        <a href="#" class="item-details text-primary" 
                           data-serial="{{ $request->item_serial_number }}"
                           data-toggle="modal" 
                           data-target="#itemModal_{{ $request->id }}">
                            {{ $request->item_serial_number }}
                        </a>
                        @if($request->item_type === 'pound')
                            <span class="badge badge-info">Pound</span>
                        @endif
                        
                        <!-- Modal for this specific item -->
                        <div class="modal fade" id="itemModal_{{ $request->id }}" tabindex="-1" role="dialog">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
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
                                    <div class="modal-body">
                                        @if($request->item_type === 'pound')
                                            <!-- Pound Details -->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6 class="font-weight-bold">Pound Information</h6>
                                                    <p><strong>Serial Number:</strong> {{ $request->item_serial_number }}</p>
                                                    <p><strong>Type:</strong> {{ $request->kind ?? 'N/A' }}</p>
                                                    <p><strong>Weight:</strong> {{ $request->weight ?? 'N/A' }}g</p>
                                                    <p><strong>Purity:</strong> {{ $request->purity ?? 'N/A' }}K</p>
                                                    <p><strong>Price:</strong> {{ $request->price }} {{ config('app.currency') }}</p>
                                                    <p><strong>Price/Gram:</strong> {{ $pricePerGram }} {{ config('app.currency') }}/g</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6 class="font-weight-bold">Item Information</h6>
                                                    <p><strong>Serial Number:</strong> {{ $request->goldItem->serial_number ?? 'N/A' }}</p>
                                                    <p><strong>Kind:</strong> {{ $request->goldItem->kind ?? 'N/A' }}</p>
                                                    <p><strong>Model:</strong> {{ $request->goldItem->model ?? 'N/A' }}</p>
                                                    <p><strong>Gold Color:</strong> {{ $request->goldItem->gold_color ?? 'N/A' }}</p>
                                                    <p><strong>Weight:</strong> {{ $request->goldItem->weight ?? 'N/A' }}</p>
                                                    <p><strong>Metal Purity:</strong> {{ $request->goldItem->metal_purity ?? 'N/A' }}</p>
                                                    {{-- @if($request->goldItem->modelCategory)
                                                        <p><strong>Stars:</strong> {{ $request->goldItem->modelCategory->stars ?? 'N/A' }}</p>
                                                    @endif --}}
                                                </div>
                                                {{-- <div class="col-md-6">
                                                    <h6 class="font-weight-bold">Sale Information</h6>
                                                    <p><strong>Shop:</strong> {{ $request->shop_name }}</p>
                                                    <p><strong>Price:</strong> {{ $request->price }} {{ config('app.currency') }}</p>
                                                    <p><strong>Payment Method:</strong> {{ $request->payment_method }}</p>
                                                    <p><strong>Date:</strong> {{ $request->created_at->format('Y-m-d H:i') }}</p>
                                                </div> --}}
                                            </div>
                                        @else
                                            <!-- Item Details -->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6 class="font-weight-bold">Item Information</h6>
                                                    <p><strong>Serial Number:</strong> {{ $request->goldItem->serial_number ?? 'N/A' }}</p>
                                                    <p><strong>Kind:</strong> {{ $request->goldItem->kind ?? 'N/A' }}</p>
                                                    <p><strong>Model:</strong> {{ $request->goldItem->model ?? 'N/A' }}</p>
                                                    <p><strong>Gold Color:</strong> {{ $request->goldItem->gold_color ?? 'N/A' }}</p>
                                                    <p><strong>Weight:</strong> {{ $request->goldItem->weight ?? 'N/A' }}g</p>
                                                    <p><strong>Metal Purity:</strong> {{ $request->goldItem->metal_purity ?? 'N/A' }}</p>
                                                    <p><strong>Price:</strong> {{ $request->price }} {{ config('app.currency') }}</p>
                                                    <p><strong>Price/Gram:</strong> {{ $pricePerGram }} {{ config('app.currency') }}/g</p>
                                                    {{-- @if($request->goldItem->modelCategory)
                                                        <p><strong>Stars:</strong> {{ $request->goldItem->modelCategory->stars ?? 'N/A' }}</p>
                                                    @endif --}}
                                                </div>
                                                {{-- <div class="col-md-6">
                                                    <h6 class="font-weight-bold">Sale Information</h6>
                                                    <p><strong>Shop:</strong> {{ $request->shop_name }}</p>
                                                    <p><strong>Price:</strong> {{ $request->price }} {{ config('app.currency') }}</p>
                                                    <p><strong>Payment Method:</strong> {{ $request->payment_method }}</p>
                                                    <p><strong>Date:</strong> {{ $request->created_at->format('Y-m-d H:i') }}</p>
                                                </div> --}}
                                            </div>
                                        @endif

                                        @if($request->customer)
                                            <div class="row mt-3">
                                                <div class="col-12">
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
                    <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                        @if ($request->status === 'pending')
                            <form action="{{ route('sell-requests.approve', $request->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">Approve</button>
                            </form>
                            <form action="{{ route('sell-requests.reject', $request->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
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
