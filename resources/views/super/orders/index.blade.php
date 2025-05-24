<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Management - Super User</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- BoxIcons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .orders-card {
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
                <h2 class="fw-bold text-dark">Orders Management</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('super.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item active">Orders</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card text-center border-0 bg-warning text-dark">
                    <div class="card-body">
                        <i class="bx bx-time display-4"></i>
                        <h4 class="mt-2">{{ $pendingOrdersCount }}</h4>
                        <p class="mb-0">Pending Orders</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card text-center border-0 bg-info text-white">
                    <div class="card-body">
                        <i class="bx bx-loader display-4"></i>
                        <h4 class="mt-2">{{ $inProgressOrdersCount }}</h4>
                        <p class="mb-0">In Progress</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card text-center border-0 bg-success text-white">
                    <div class="card-body">
                        <i class="bx bx-check-circle display-4"></i>
                        <h4 class="mt-2">{{ $completedOrdersCount }}</h4>
                        <p class="mb-0">Completed</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card text-center border-0 bg-primary text-white">
                    <div class="card-body">
                        <i class="bx bx-package display-4"></i>
                        <h4 class="mt-2">{{ $orders->total() }}</h4>
                        <p class="mb-0">Total Orders</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card filter-card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('super.orders') }}">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Search Order/Customer</label>
                                    <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Order number or customer name...">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Shop</label>
                                    <select class="form-select" name="shop">
                                        <option value="">All Shops</option>
                                        @foreach(\App\Models\Shop::all() as $shop)
                                            <option value="{{ $shop->id }}" {{ request('shop') == $shop->id ? 'selected' : '' }}>
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
                                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
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
                                        <a href="{{ route('super.orders') }}" class="btn btn-outline-secondary btn-sm">
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

        <!-- Orders Table with Tabs -->
        <div class="row">
            <div class="col-12">
                <div class="card orders-card">
                    <div class="card-header bg-white">
                        <ul class="nav nav-tabs card-header-tabs" id="orderTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all-orders" type="button" role="tab">
                                    <i class="bx bx-list-ul me-2"></i>All Orders
                                    <span class="badge bg-secondary badge-count">{{ $orders->total() }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending-orders" type="button" role="tab">
                                    <i class="bx bx-time me-2"></i>Pending
                                    <span class="badge bg-warning badge-count text-dark">{{ $pendingOrdersCount }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="progress-tab" data-bs-toggle="tab" data-bs-target="#progress-orders" type="button" role="tab">
                                    <i class="bx bx-loader me-2"></i>In Progress
                                    <span class="badge bg-info badge-count">{{ $inProgressOrdersCount }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed-orders" type="button" role="tab">
                                    <i class="bx bx-check me-2"></i>Completed
                                    <span class="badge bg-success badge-count">{{ $completedOrdersCount }}</span>
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="orderTabsContent">
                            <!-- All Orders Tab -->
                            <div class="tab-pane fade show active" id="all-orders" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6>All Orders</h6>
                                    <a href="{{ route('super.orders') }}?{{ http_build_query(request()->query()) }}&export=excel" 
                                       class="btn btn-outline-success btn-sm">
                                        <i class="bx bx-export me-1"></i>Export All
                                    </a>
                                </div>
                                @include('super.orders.partials.orders-table', ['orders' => $orders])
                            </div>

                            <!-- Pending Orders Tab -->
                            <div class="tab-pane fade" id="pending-orders" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6>Pending Orders</h6>
                                    <a href="{{ route('super.orders') }}?status=pending&export=excel" 
                                       class="btn btn-outline-success btn-sm">
                                        <i class="bx bx-export me-1"></i>Export Pending
                                    </a>
                                </div>
                                @include('super.orders.partials.orders-table', ['orders' => $pendingOrders, 'is_collection' => true])
                            </div>

                            <!-- In Progress Orders Tab -->
                            <div class="tab-pane fade" id="progress-orders" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6>In Progress Orders</h6>
                                    <a href="{{ route('super.orders') }}?status=in_progress&export=excel" 
                                       class="btn btn-outline-success btn-sm">
                                        <i class="bx bx-export me-1"></i>Export In Progress
                                    </a>
                                </div>
                                @include('super.orders.partials.orders-table', ['orders' => $inProgressOrders, 'is_collection' => true])
                            </div>

                            <!-- Completed Orders Tab -->
                            <div class="tab-pane fade" id="completed-orders" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6>Completed Orders</h6>
                                    <a href="{{ route('super.orders') }}?status=completed&export=excel" 
                                       class="btn btn-outline-success btn-sm">
                                        <i class="bx bx-export me-1"></i>Export Completed
                                    </a>
                                </div>
                                @include('super.orders.partials.orders-table', ['orders' => $completedOrders, 'is_collection' => true])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div class="modal fade" id="orderDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="orderDetailsContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewOrderDetails(orderId) {
            fetch(`/super/orders/${orderId}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('orderDetailsContent').innerHTML = data;
                    new bootstrap.Modal(document.getElementById('orderDetailsModal')).show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading order details');
                });
        }

        function updateOrderStatus(orderId, status) {
            if (confirm(`Are you sure you want to change this order status to ${status}?`)) {
                // Implement status update functionality
                alert(`Order ${orderId} status would be updated to ${status}`);
            }
        }

        function editOrder(orderId) {
            window.location.href = `/super/orders/${orderId}/edit`;
        }

        function deleteOrder(orderId) {
            if (confirm('Are you sure you want to delete this order? This action cannot be undone.')) {
                // Implement delete functionality
                alert(`Order ${orderId} would be deleted`);
            }
        }
    </script>
</body>
</html> 