<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sold Items Management - Super User</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- BoxIcons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sold-items-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .filter-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
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
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
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
                <h2 class="fw-bold text-dark">Sold Items Management</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('super.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item active">Sold Items</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card text-center border-0 bg-primary text-white">
                    <div class="card-body">
                        <i class="bx bx-check-circle display-4"></i>
                        <h4 class="mt-2">{{ $soldItems->total() }}</h4>
                        <p class="mb-0">Total Sold Items</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card text-center border-0 bg-success text-white">
                    <div class="card-body">
                        <i class="bx bx-calendar display-4"></i>
                        <h4 class="mt-2">{{ $todaysSalesCount }}</h4>
                        <p class="mb-0">Today's Sales</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card text-center border-0 bg-info text-white">
                    <div class="card-body">
                        <i class="bx bx-dollar display-4"></i>
                        <h4 class="mt-2">${{ number_format($soldItems->sum('price'), 2) }}</h4>
                        <p class="mb-0">Total Revenue</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card text-center border-0 bg-warning text-dark">
                    <div class="card-body">
                        <i class="bx bx-weight display-4"></i>
                        <h4 class="mt-2">{{ $soldItems->sum('weight') }}g</h4>
                        <p class="mb-0">Total Weight</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card filter-card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('super.sold-items') }}">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Search Serial/Customer</label>
                                    <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search by serial or customer...">
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
                                    <label class="form-label">Date From</label>
                                    <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Date To</label>
                                    <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Min Price</label>
                                    <input type="number" class="form-control" name="min_price" value="{{ request('min_price') }}" placeholder="0">
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-flex flex-column gap-1">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="bx bx-search"></i>
                                        </button>
                                        <a href="{{ route('super.sold-items') }}" class="btn btn-outline-secondary btn-sm">
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

        <!-- Sold Items Table -->
        <div class="row">
            <div class="col-12">
                <div class="card sold-items-card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="bx bx-check-circle me-2"></i>All Sold Items</h5>
                        <div>
                            <a href="{{ route('super.sold-items') }}?{{ http_build_query(request()->query()) }}&export=excel" 
                               class="btn btn-outline-success btn-sm">
                                <i class="bx bx-export me-1"></i>Export
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Serial Number</th>
                                        <th>Model</th>
                                        <th>Shop</th>
                                        <th>Customer</th>
                                        <th>Price</th>
                                        <th>Weight</th>
                                        <th>Payment Method</th>
                                        <th>Sold Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($soldItems as $item)
                                    <tr>
                                        <td>
                                            <span class="badge bg-primary">{{ $item->serial_number }}</span>
                                        </td>
                                        <td>{{ $item->model }}</td>
                                        <td>
                                            <span class="badge bg-light text-dark">{{ $item->shop_name }}</span>
                                        </td>
                                        <td>
                                            @if($item->customer)
                                                <div>
                                                    <strong>{{ $item->customer->first_name }} {{ $item->customer->last_name }}</strong><br>
                                                    <small class="text-muted">{{ $item->customer->phone_number }}</small>
                                                </div>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong class="text-success">${{ number_format($item->price, 2) }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning text-dark">{{ $item->weight }}g</span>
                                        </td>
                                        <td>
                                            @if($item->payment_method)
                                                <span class="badge bg-info">{{ $item->payment_method }}</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($item->sold_date)->format('M d, Y H:i') }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-outline-info" 
                                                        onclick="viewItemDetails('{{ $item->serial_number }}')" 
                                                        title="View Details">
                                                    <i class="bx bx-show"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-success" 
                                                        onclick="generateReceipt('{{ $item->id }}')" 
                                                        title="Generate Receipt">
                                                    <i class="bx bx-receipt"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="bx bx-package fs-1 d-block mb-2"></i>
                                                No sold items found
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        @if($soldItems->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $soldItems->appends(request()->query())->links('vendor.pagination.custom-super') }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Item Details Modal -->
    <div class="modal fade" id="itemDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sold Item Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="itemDetailsContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewItemDetails(serialNumber) {
            // This would fetch item details - implement as needed
            alert('View details for: ' + serialNumber);
        }

        function generateReceipt(itemId) {
            // This would generate a receipt - implement as needed
            alert('Generate receipt for item ID: ' + itemId);
        }
    </script>
</body>
</html> 