<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    @include('components.navbar')
    <style>
    .pagination {
        display: flex;
        justify-content: center;
        margin: 20px 0;
    }

    .pagination .page-item {
        margin: 0 2px;
    }

    .pagination .page-link {
        color: #333;
        padding: 8px 16px;
        border: 1px solid #ddd;
        border-radius: 4px;
        transition: all 0.3s ease;
    }

    .pagination .page-link:hover {
        background-color: #f8f9fa;
        border-color: #ddd;
        text-decoration: none;
    }

    .pagination .page-item.active .page-link {
        background-color: #007bff;
        border-color: #007bff;
        color: white;
    }

    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        background-color: #fff;
        border-color: #ddd;
    }

    /* Add responsive styling */
    @media (max-width: 768px) {
        .pagination .page-link {
            padding: 6px 12px;
            font-size: 14px;
        }
    }
    </style>
</head>

<div class="container mt-4">
    <div class="card mb-4">
        <div class="card-header">
            <h3>Advanced Filter</h3>
        </div>
        <div class="card-body">
            <form id="filterForm" class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>From Date:</label>
                        <input type="date" class="form-control" name="from_date" value="{{ request('from_date') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>To Date:</label>
                        <input type="date" class="form-control" name="to_date" value="{{ request('to_date') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Shop Name:</label>
                        <select class="form-control" name="shop_name">
                            <option value="">All Shops</option>
                            @foreach($shops as $shop)
                                <option value="{{ $shop }}" {{ request('shop_name') == $shop ? 'selected' : '' }}>
                                    {{ $shop }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                {{-- <div class="col-md-3">
                    <div class="form-group">
                        <label>Status:</label>
                        <select class="form-control" name="status">
                            <option value="all">All Status</option>
                            <option value="approved" {{ request('status', 'approved') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                </div> --}}
                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <button type="button" id="exportExcel" class="btn btn-success ml-2">Export to Excel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h3>Summary</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <h5>Total Items: {{ $totals->total_items }}</h5>
                </div>
                <div class="col-md-3">
                    <h5>Total Weight: {{ number_format($totals->total_weight, 2) }}g</h5>
                </div>
                <div class="col-md-3">
                    <h5>Total Revenue: {{ number_format($totals->total_revenue) }} {{ config('app.currency') }}</h5>
                </div>
                <div class="col-md-3">
                    <h5>Avg Price/g: {{ number_format($totals->avg_price_per_gram, 2) }} {{ config('app.currency') }}/g</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Table -->
    <table class="table table-striped">
        <thead class="thead-dark">
            <tr>
                <th>Serial Number</th>
                <th>Shop Name</th>
                <th>Weight</th>
                <th>Price</th>
                <th>Price/Gram</th>
                <th>Payment Method</th>
                <th>Customer Name</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($soldItemRequests as $request)
                <tr>
                    <td>
                        <a href="#" class="item-details text-primary" data-serial="{{ $request->serial_number }}">
                            {{ $request->serial_number }}
                        </a>
                    </td>
                    <td>{{ $request->shop_name }}</td>
                    <td>{{ $request->weight }}g</td>
                    <td>{{ $request->price > 0 ? number_format($request->price) : 0 }} </td>
                    <td>{{ $request->weight > 0 ? number_format($request->price / $request->weight, 2) : 0 }} /g</td>
                    <td>{{ $request->customer ? $request->customer->payment_method : 'N/A' }}</td>
                    <td>{{ $request->customer ? $request->customer->first_name . ' ' . $request->customer->last_name : 'N/A' }}</td>
                    <td>{{ $request->sold_date }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {!! $soldItemRequests->appends(request()->query())->links('pagination::bootstrap-4') !!}
    </div>
</div>
<div class="modal fade" id="itemModal" tabindex="-1" role="dialog" aria-labelledby="itemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="itemModalLabel">Item Details</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="itemDetails" class="p-3"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
</div>

<script>
$(document).ready(function() {
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        let formData = $(this).serialize();
        window.location.href = `${window.location.pathname}?${formData}`;
    });

    $('#exportExcel').click(function() {
        let formData = $('#filterForm').serialize();
        window.location.href = `/export-sales?${formData}`;
    });
});
</script> 

<script>
    $(document).ready(function() {
        $('.item-details').click(function(e) {
            e.preventDefault();
            let serial = $(this).data('serial');
            
            $.ajax({
                url: `/item-details/${serial}`,
                method: 'GET',
                success: function(data) {
                    let details = `
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold">Serial Number:</div>
                            <div class="col-7">${data.serial_number}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold">Kind:</div>
                            <div class="col-7">${data.kind}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold">Model:</div>
                            <div class="col-7">${data.model}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold">Gold Color:</div>
                            <div class="col-7">${data.gold_color}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold">Weight:</div>
                            <div class="col-7">${data.weight}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 font-weight-bold">Price:</div>
                            <div class="col-7">${data.price}</div>
                        </div>
                    `;
                    $('#itemDetails').html(details);
                    $('#itemModal').modal('show');
                }
            });
        });
    });
    </script>