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
        .requests-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .filter-card {
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
                <h2 class="fw-bold text-dark">Requests Management</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('super.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item active">Requests</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bx bx-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card text-center border-0 bg-warning text-dark">
                    <div class="card-body">
                        <i class="bx bx-plus display-4"></i>
                        <h4 class="mt-2">{{ $addRequests->where('status', 'pending')->count() }}</h4>
                        <p class="mb-0">Pending Add Requests</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card text-center border-0 bg-info text-white">
                    <div class="card-body">
                        <i class="bx bx-transfer display-4"></i>
                        <h4 class="mt-2">{{ $transferRequests->where('status', 'pending')->count() }}</h4>
                        <p class="mb-0">Pending Transfer Requests</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card text-center border-0 bg-success text-white">
                    <div class="card-body">
                        <i class="bx bx-dollar display-4"></i>
                        <h4 class="mt-2">{{ $saleRequests->where('status', 'pending')->count() }}</h4>
                        <p class="mb-0">Pending Sale Requests</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card text-center border-0 bg-primary text-white">
                    <div class="card-body">
                        <i class="bx bx-coin-stack display-4"></i>
                        <h4 class="mt-2">{{ $poundRequests->where('status', 'pending')->count() }}</h4>
                        <p class="mb-0">Pending Pound Requests</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card filter-card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('super.requests') }}">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Search Serial/Model/Customer</label>
                                    <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search...">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Shop</label>
                                    <select class="form-select" name="shop">
                                        <option value="">All Shops</option>
                                        @foreach(\App\Models\Shop::all() as $shop)
                                            <option value="{{ $shop->name }}" {{ request('shop') == $shop->name ? 'selected' : '' }}>
                                                {{ $shop->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" name="status">
                                        <option value="">All Status</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Date From</label>
                                    <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Date To</label>
                                    <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-flex flex-column gap-1">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="bx bx-search"></i>
                                        </button>
                                        <a href="{{ route('super.requests') }}" class="btn btn-outline-secondary btn-sm">
                                            <i class="bx bx-refresh"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Requests Tables with Tabs -->
        <div class="row">
            <div class="col-12">
                <div class="card requests-card">
                    <div class="card-header bg-white">
                        <ul class="nav nav-tabs card-header-tabs" id="requestTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="add-tab" data-bs-toggle="tab" data-bs-target="#add-requests" type="button" role="tab">
                                    <i class="bx bx-plus me-2"></i>Add Requests
                                    <span class="badge bg-secondary badge-count">{{ $addRequests->total() }}</span>
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
                                    <i class="bx bx-dollar me-2"></i>Sale Requests
                                    <span class="badge bg-success badge-count">{{ $saleRequests->total() }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pound-tab" data-bs-toggle="tab" data-bs-target="#pound-requests" type="button" role="tab">
                                    <i class="bx bx-coin-stack me-2"></i>Pound Requests
                                    <span class="badge bg-primary badge-count">{{ $poundRequests->total() }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#transfer-history" type="button" role="tab">
                                    <i class="bx bx-history me-2"></i>Transfer History
                                    <span class="badge bg-dark badge-count">{{ $transferRequestHistory->total() }}</span>
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="requestTabsContent">
                            <!-- Add Requests Tab -->
                            <div class="tab-pane fade show active" id="add-requests" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6>Add Requests</h6>
                                    <a href="{{ route('super.requests') }}?{{ http_build_query(request()->query()) }}&export=add_requests" 
                                       class="btn btn-outline-success btn-sm">
                                        <i class="bx bx-export me-1"></i>Export
                                    </a>
                                </div>
                                @include('super.requests.partials.add-requests-table', ['requests' => $addRequests])
                            </div>

                            <!-- Transfer Requests Tab -->
                            <div class="tab-pane fade" id="transfer-requests" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6>Transfer Requests</h6>
                                    <a href="{{ route('super.requests') }}?{{ http_build_query(request()->query()) }}&export=transfer_requests" 
                                       class="btn btn-outline-success btn-sm">
                                        <i class="bx bx-export me-1"></i>Export
                                    </a>
                                </div>
                                @include('super.requests.partials.transfer-requests-table', ['requests' => $transferRequests])
                            </div>

                            <!-- Sale Requests Tab -->
                            <div class="tab-pane fade" id="sale-requests" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6>Sale Requests</h6>
                                    <a href="{{ route('super.requests') }}?{{ http_build_query(request()->query()) }}&export=sale_requests" 
                                       class="btn btn-outline-success btn-sm">
                                        <i class="bx bx-export me-1"></i>Export
                                    </a>
                                </div>
                                @include('super.requests.partials.sale-requests-table', ['requests' => $saleRequests])
                            </div>

                            <!-- Pound Requests Tab -->
                            <div class="tab-pane fade" id="pound-requests" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6>Pound Requests</h6>
                                    <a href="{{ route('super.requests') }}?{{ http_build_query(request()->query()) }}&export=pound_requests" 
                                       class="btn btn-outline-success btn-sm">
                                        <i class="bx bx-export me-1"></i>Export
                                    </a>
                                </div>
                                @include('super.requests.partials.pound-requests-table', ['requests' => $poundRequests])
                            </div>

                            <!-- Transfer History Tab -->
                            <div class="tab-pane fade" id="transfer-history" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6>Transfer History</h6>
                                    <a href="{{ route('super.requests') }}?{{ http_build_query(request()->query()) }}&export=transfer_history" 
                                       class="btn btn-outline-success btn-sm">
                                        <i class="bx bx-export me-1"></i>Export
                                    </a>
                                </div>
                                @include('super.requests.partials.transfer-history-table', ['requests' => $transferRequestHistory])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Request Details Modal -->
    <div class="modal fade" id="requestDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Request Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="requestDetailsContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewRequestDetails(requestType, requestId) {
            // This would fetch request details - implement as needed
            alert(`View details for ${requestType} request ID: ${requestId}`);
        }

        function handleRequest(requestType, requestId, action) {
            if (confirm(`Are you sure you want to ${action} this ${requestType} request?`)) {
                // Create form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/super/requests/${requestType}/${requestId}/handle`;
                
                // Add CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                form.appendChild(csrfToken);
                
                // Add action
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = action;
                form.appendChild(actionInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html> 