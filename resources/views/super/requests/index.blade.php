<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requests Management - Super User</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- BoxIcons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .request-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .nav-tabs .nav-link {
            border: none;
            border-radius: 10px 10px 0 0;
            margin-right: 0.25rem;
        }
        .nav-tabs .nav-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }
        .badge-count {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            margin-left: 0.5rem;
        }
        /* Custom Pagination Styling */
        .pagination {
            justify-content: center;
            margin: 0;
        }
        .pagination .page-link {
            border: 1px solid #e9ecef;
            border-radius: 6px;
            margin: 0 2px;
            color: #667eea;
            font-weight: 500;
            padding: 0.375rem 0.625rem;
            font-size: 0.875rem;
            line-height: 1.25;
            transition: all 0.15s ease-in-out;
        }
        .pagination .page-link:hover {
            background-color: #667eea;
            border-color: #667eea;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(102, 126, 234, 0.25);
        }
        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea;
            color: white;
            box-shadow: 0 2px 4px rgba(102, 126, 234, 0.3);
        }
        .pagination .page-item.disabled .page-link {
            color: #6c757d;
            background-color: #f8f9fa;
            border-color: #dee2e6;
        }
        .pagination .page-item:first-child .page-link,
        .pagination .page-item:last-child .page-link {
            border-radius: 6px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    @include('components.navbar')

    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="fw-bold text-dark">All Requests Management</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('super.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item active">Requests</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="row">
            <div class="col-12">
                <div class="card request-card">
                    <div class="card-header bg-white">
                        <ul class="nav nav-tabs card-header-tabs" id="requestTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="add-tab" data-bs-toggle="tab" data-bs-target="#add-requests" type="button" role="tab">
                                    <i class="bx bx-plus me-2"></i>Add Requests
                                    <span class="badge bg-warning badge-count">{{ $addRequests->total() }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="transfer-tab" data-bs-toggle="tab" data-bs-target="#transfer-requests" type="button" role="tab">
                                    <i class="bx bx-transfer me-2"></i>Transfer Requests
                                    <span class="badge bg-info badge-count">{{ $transferRequests->total() }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="sale-tab" data-bs-toggle="tab" data-bs-target="#sale-requests" type="button" role="tab">
                                    <i class="bx bx-money me-2"></i>Sale Requests
                                    <span class="badge bg-success badge-count">{{ $saleRequests->total() }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pound-tab" data-bs-toggle="tab" data-bs-target="#pound-requests" type="button" role="tab">
                                    <i class="bx bx-coin-stack me-2"></i>Pound Requests
                                    <span class="badge bg-warning badge-count text-dark">{{ $poundRequests->total() }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="transfer-history-tab" data-bs-toggle="tab" data-bs-target="#transfer-history" type="button" role="tab">
                                    <i class="bx bx-history me-2"></i>Transfer History
                                    <span class="badge bg-secondary badge-count">{{ $transferRequestHistory->total() }}</span>
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="requestTabsContent">
                            <!-- Add Requests Tab -->
                            <div class="tab-pane fade show active" id="add-requests" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Serial Number</th>
                                                <th>Model</th>
                                                <th>Shop</th>
                                                <th>Kind</th>
                                                <th>Weight</th>
                                                <th>Status</th>
                                                <th>Created</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($addRequests as $request)
                                            <tr>
                                                <td><strong>{{ $request->id }}</strong></td>
                                                <td>{{ $request->serial_number }}</td>
                                                <td>{{ $request->model }}</td>
                                                <td>{{ $request->shop_name }}</td>
                                                <td>{{ $request->kind }}</td>
                                                <td>{{ $request->weight }}g</td>
                                                <td>
                                                    <span class="badge {{ $request->status == 'pending' ? 'bg-warning text-dark' : ($request->status == 'approved' ? 'bg-success' : 'bg-danger') }}">
                                                        {{ ucfirst($request->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $request->created_at->format('M d, Y H:i') }}</td>
                                                <td>
                                                    @if($request->status == 'pending')
                                                    <div class="btn-group" role="group">
                                                        <form method="POST" action="{{ route('super.handle-request', ['type' => 'add', 'id' => $request->id]) }}" style="display: inline;">
                                                            @csrf
                                                            <input type="hidden" name="action" value="approve">
                                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this request?')">
                                                                <i class="bx bx-check"></i> Approve
                                                            </button>
                                                        </form>
                                                        <form method="POST" action="{{ route('super.handle-request', ['type' => 'add', 'id' => $request->id]) }}" style="display: inline;">
                                                            @csrf
                                                            <input type="hidden" name="action" value="reject">
                                                            <button type="submit" class="btn btn-sm btn-danger ms-1" onclick="return confirm('Reject this request?')">
                                                                <i class="bx bx-x"></i> Reject
                                                            </button>
                                                        </form>
                                                    </div>
                                                    @else
                                                        <span class="text-muted">No actions available</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="d-flex justify-content-center">
                                        {{ $addRequests->appends(request()->query())->links('vendor.pagination.custom-super') }}
                                    </div>
                                </div>
                            </div>

                            <!-- Transfer Requests Tab -->
                            <div class="tab-pane fade" id="transfer-requests" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Item</th>
                                                <th>From Shop</th>
                                                <th>To Shop</th>
                                                <th>Type</th>
                                                <th>Status</th>
                                                <th>Created</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($transferRequests as $request)
                                            <tr>
                                                <td><strong>{{ $request->id }}</strong></td>
                                                <td>
                                                    @if($request->goldItem)
                                                        <div>
                                                            <strong>{{ $request->goldItem->serial_number }}</strong><br>
                                                            <small class="text-muted">{{ $request->goldItem->model }}</small>
                                                        </div>
                                                    @else
                                                        <span class="badge bg-secondary">Pound Transfer</span>
                                                    @endif
                                                </td>
                                                <td><span class="badge bg-light text-dark">{{ $request->from_shop_name }}</span></td>
                                                <td><span class="badge bg-light text-dark">{{ $request->to_shop_name }}</span></td>
                                                <td>{{ ucfirst($request->type) }}</td>
                                                <td>
                                                    <span class="badge {{ $request->status == 'pending' ? 'bg-warning text-dark' : ($request->status == 'approved' ? 'bg-success' : 'bg-danger') }}">
                                                        {{ ucfirst($request->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $request->created_at->format('M d, Y H:i') }}</td>
                                                <td>
                                                    @if($request->status == 'pending')
                                                    <div class="btn-group" role="group">
                                                        <form method="POST" action="{{ route('super.handle-request', ['type' => 'transfer', 'id' => $request->id]) }}" style="display: inline;">
                                                            @csrf
                                                            <input type="hidden" name="action" value="approve">
                                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this request?')">
                                                                <i class="bx bx-check"></i> Approve
                                                            </button>
                                                        </form>
                                                        <form method="POST" action="{{ route('super.handle-request', ['type' => 'transfer', 'id' => $request->id]) }}" style="display: inline;">
                                                            @csrf
                                                            <input type="hidden" name="action" value="reject">
                                                            <button type="submit" class="btn btn-sm btn-danger ms-1" onclick="return confirm('Reject this request?')">
                                                                <i class="bx bx-x"></i> Reject
                                                            </button>
                                                        </form>
                                                    </div>
                                                    @else
                                                        <span class="text-muted">No actions available</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="d-flex justify-content-center">
                                        {{ $transferRequests->appends(request()->query())->links('vendor.pagination.custom-super') }}
                                    </div>
                                </div>
                            </div>

                            <!-- Sale Requests Tab -->
                            <div class="tab-pane fade" id="sale-requests" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Item Serial</th>
                                                <th>Shop</th>
                                                <th>Customer</th>
                                                <th>Price</th>
                                                <th>Status</th>
                                                <th>Created</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($saleRequests as $request)
                                            <tr>
                                                <td><strong>{{ $request->id }}</strong></td>
                                                <td>{{ $request->item_serial_number }}</td>
                                                <td>{{ $request->shop_name }}</td>
                                                <td>
                                                    @if($request->customer)
                                                        {{ $request->customer->first_name }} {{ $request->customer->last_name }}
                                                    @else
                                                        {{ $request->customer_first_name }} {{ $request->customer_last_name }}
                                                    @endif
                                                </td>
                                                <td><strong class="text-success">${{ number_format($request->price, 2) }}</strong></td>
                                                <td>
                                                    <span class="badge {{ $request->status == 'pending' ? 'bg-warning text-dark' : ($request->status == 'approved' ? 'bg-success' : 'bg-danger') }}">
                                                        {{ ucfirst($request->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $request->created_at->format('M d, Y H:i') }}</td>
                                                <td>
                                                    @if($request->status == 'pending')
                                                    <div class="btn-group" role="group">
                                                        <form method="POST" action="{{ route('super.handle-request', ['type' => 'sale', 'id' => $request->id]) }}" style="display: inline;">
                                                            @csrf
                                                            <input type="hidden" name="action" value="approve">
                                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this request?')">
                                                                <i class="bx bx-check"></i> Approve
                                                            </button>
                                                        </form>
                                                        <form method="POST" action="{{ route('super.handle-request', ['type' => 'sale', 'id' => $request->id]) }}" style="display: inline;">
                                                            @csrf
                                                            <input type="hidden" name="action" value="reject">
                                                            <button type="submit" class="btn btn-sm btn-danger ms-1" onclick="return confirm('Reject this request?')">
                                                                <i class="bx bx-x"></i> Reject
                                                            </button>
                                                        </form>
                                                    </div>
                                                    @else
                                                        <span class="text-muted">No actions available</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="d-flex justify-content-center">
                                        {{ $saleRequests->appends(request()->query())->links('vendor.pagination.custom-super') }}
                                    </div>
                                </div>
                            </div>

                            <!-- Pound Requests Tab -->
                            <div class="tab-pane fade" id="pound-requests" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Serial Number</th>
                                                <th>Shop</th>
                                                <th>Type</th>
                                                <th>Weight</th>
                                                <th>Quantity</th>
                                                <th>Status</th>
                                                <th>Created</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($poundRequests as $request)
                                            <tr>
                                                <td><strong>{{ $request->id }}</strong></td>
                                                <td>{{ $request->serial_number }}</td>
                                                <td>{{ $request->shop_name }}</td>
                                                <td><span class="badge bg-info">{{ $request->type }}</span></td>
                                                <td>{{ $request->weight }}g</td>
                                                <td>{{ $request->quantity }}</td>
                                                <td>
                                                    <span class="badge {{ $request->status == 'pending' ? 'bg-warning text-dark' : ($request->status == 'approved' ? 'bg-success' : 'bg-danger') }}">
                                                        {{ ucfirst($request->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $request->created_at->format('M d, Y H:i') }}</td>
                                                <td>
                                                    @if($request->status == 'pending')
                                                    <div class="btn-group" role="group">
                                                        <form method="POST" action="{{ route('super.handle-request', ['type' => 'pound', 'id' => $request->id]) }}" style="display: inline;">
                                                            @csrf
                                                            <input type="hidden" name="action" value="approve">
                                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this request?')">
                                                                <i class="bx bx-check"></i> Approve
                                                            </button>
                                                        </form>
                                                        <form method="POST" action="{{ route('super.handle-request', ['type' => 'pound', 'id' => $request->id]) }}" style="display: inline;">
                                                            @csrf
                                                            <input type="hidden" name="action" value="reject">
                                                            <button type="submit" class="btn btn-sm btn-danger ms-1" onclick="return confirm('Reject this request?')">
                                                                <i class="bx bx-x"></i> Reject
                                                            </button>
                                                        </form>
                                                    </div>
                                                    @else
                                                        <span class="text-muted">No actions available</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="d-flex justify-content-center">
                                        {{ $poundRequests->appends(request()->query())->links('vendor.pagination.custom-super') }}
                                    </div>
                                </div>
                            </div>

                            <!-- Transfer Request History Tab -->
                            <div class="tab-pane fade" id="transfer-history" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Serial Number</th>
                                                <th>Model</th>
                                                <th>Kind</th>
                                                <th>From Shop</th>
                                                <th>To Shop</th>
                                                <th>Weight</th>
                                                <th>Status</th>
                                                <th>Transfer Date</th>
                                                <th>Sold Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($transferRequestHistory as $history)
                                            <tr>
                                                <td><strong>{{ $history->id }}</strong></td>
                                                <td>
                                                    <span class="badge bg-primary">{{ $history->serial_number }}</span>
                                                </td>
                                                <td>{{ $history->model }}</td>
                                                <td>
                                                    <span class="badge bg-light text-dark">{{ $history->kind }}</span>
                                                </td>
                                                <td>
                                                    @if($history->fromShop)
                                                        <span class="badge bg-info">{{ $history->fromShop->name }}</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ $history->from_shop_name }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($history->toShop)
                                                        <span class="badge bg-success">{{ $history->toShop->name }}</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ $history->to_shop_name }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $history->weight }}g</td>
                                                <td>
                                                    <span class="badge {{ $history->status == 'completed' ? 'bg-success' : 'bg-warning text-dark' }}">
                                                        {{ ucfirst($history->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($history->transfer_completed_at)
                                                        {{ \Carbon\Carbon::parse($history->transfer_completed_at)->format('M d, Y H:i') }}
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($history->item_sold_at)
                                                        <span class="text-success">{{ \Carbon\Carbon::parse($history->item_sold_at)->format('M d, Y H:i') }}</span>
                                                    @else
                                                        <span class="text-muted">Not sold</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="d-flex justify-content-center">
                                        {{ $transferRequestHistory->appends(request()->query())->links('vendor.pagination.custom-super') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 