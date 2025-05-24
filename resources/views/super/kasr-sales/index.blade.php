<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasr Sales Management - Super User</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- BoxIcons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .kasr-sales-card {
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
        .kasr-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
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
                <h2 class="fw-bold text-dark">Kasr Sales Management</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('super.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item active">Kasr Sales</li>
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
                        <h4 class="mt-2">{{ $pendingKasrCount }}</h4>
                        <p class="mb-0">Pending Kasr Sales</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card text-center border-0 bg-success text-white">
                    <div class="card-body">
                        <i class="bx bx-check-circle display-4"></i>
                        <h4 class="mt-2">{{ $completedKasrCount }}</h4>
                        <p class="mb-0">Completed Sales</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card text-center border-0 bg-info text-white">
                    <div class="card-body">
                        <i class="bx bx-package display-4"></i>
                        <h4 class="mt-2">{{ $kasrSales->total() }}</h4>
                        <p class="mb-0">Total Kasr Sales</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card text-center border-0 bg-primary text-white">
                    <div class="card-body">
                        <i class="bx bx-dollar display-4"></i>
                        <h4 class="mt-2">${{ number_format($kasrSales->sum('offered_price'), 2) }}</h4>
                        <p class="mb-0">Total Value</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card filter-card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('super.kasr-sales') }}">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Search Customer</label>
                                    <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Customer name or phone...">
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
                                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Date</label>
                                    <input type="date" class="form-control" name="date" value="{{ request('date') }}">
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
                                        <a href="{{ route('super.kasr-sales') }}" class="btn btn-outline-secondary btn-sm">
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

        <!-- Kasr Sales Table -->
        <div class="row">
            <div class="col-12">
                <div class="card kasr-sales-card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="bx bx-coin-stack me-2"></i>All Kasr Sales</h5>
                        <div>
                            <a href="{{ route('super.kasr-sales') }}?{{ http_build_query(request()->query()) }}&export=excel" 
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
                                        <th>ID</th>
                                        <th>Customer</th>
                                        <th>Shop</th>
                                        <th>Items</th>
                                        <th>Total Weight</th>
                                        <th>Net Weight</th>
                                        <th>Offered Price</th>
                                        <th>Status</th>
                                        <th>Order Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($kasrSales as $sale)
                                    <tr>
                                        <td><strong>#{{ $sale->id }}</strong></td>
                                        <td>
                                            <div>
                                                <strong>{{ $sale->customer_name }}</strong>
                                                @if($sale->customer_phone)
                                                    <br><small class="text-muted">{{ $sale->customer_phone }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">{{ $sale->shop_name }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $sale->items->count() }} items</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning text-dark">{{ $sale->getTotalWeight() }}g</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $sale->getTotalNetWeight() }}g</span>
                                        </td>
                                        <td>
                                            @if($sale->offered_price)
                                                <strong class="text-success">${{ number_format($sale->offered_price, 2) }}</strong>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ 
                                                $sale->status == 'pending' ? 'bg-warning text-dark' : 
                                                ($sale->status == 'completed' ? 'bg-success' : 'bg-danger')
                                            }}">
                                                {{ ucfirst($sale->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($sale->order_date)
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($sale->order_date)->format('M d, Y H:i') }}</small>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-outline-info" 
                                                        onclick="viewSaleDetails('{{ $sale->id }}')" 
                                                        title="View Details">
                                                    <i class="bx bx-show"></i>
                                                </button>
                                                @if($sale->image_path)
                                                <button class="btn btn-sm btn-outline-primary" 
                                                        onclick="viewImage('{{ asset('storage/' . $sale->image_path) }}')" 
                                                        title="View Image">
                                                    <i class="bx bx-image"></i>
                                                </button>
                                                @endif
                                                @if($sale->status == 'pending')
                                                <div class="btn-group ms-1" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" title="Update Status">
                                                        <i class="bx bx-check"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="#" onclick="updateSaleStatus('{{ $sale->id }}', 'completed')">Mark Completed</a></li>
                                                        <li><a class="dropdown-item" href="#" onclick="updateSaleStatus('{{ $sale->id }}', 'cancelled')">Cancel Sale</a></li>
                                                    </ul>
                                                </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="bx bx-coin-stack fs-1 d-block mb-2"></i>
                                                No kasr sales found
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        @if($kasrSales->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $kasrSales->appends(request()->query())->links('vendor.pagination.custom-super') }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Kasr Item Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="Kasr Item" class="img-fluid">
                </div>
            </div>
        </div>
    </div>

    <!-- Sale Details Modal -->
    <div class="modal fade" id="saleDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sale Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="saleDetailsContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewImage(imageSrc) {
            document.getElementById('modalImage').src = imageSrc;
            new bootstrap.Modal(document.getElementById('imageModal')).show();
        }

        function viewSaleDetails(saleId) {
            // This would fetch sale details - implement as needed
            alert('View details for sale ID: ' + saleId);
        }

        function updateSaleStatus(saleId, status) {
            if (confirm(`Are you sure you want to change this sale status to ${status}?`)) {
                // Implement status update functionality
                alert(`Sale ${saleId} status would be updated to ${status}`);
            }
        }
    </script>
</body>
</html> 