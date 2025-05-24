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

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="bx bx-check-circle fs-1 mb-2"></i>
                        <h3 class="fw-bold">{{ $soldItems->total() }}</h3>
                        <p class="mb-0">Total Sold Items</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="bx bx-dollar-circle fs-1 mb-2"></i>
                        <h3 class="fw-bold">${{ number_format($soldItems->sum('price'), 2) }}</h3>
                        <p class="mb-0">Total Revenue</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="bx bx-weight fs-1 mb-2"></i>
                        <h3 class="fw-bold">{{ $soldItems->sum('weight') }}g</h3>
                        <p class="mb-0">Total Weight</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="bx bx-calendar fs-1 mb-2"></i>
                        <h3 class="fw-bold">{{ $todaysSalesCount }}</h3>
                        <p class="mb-0">Today's Sales</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('super.sold-items') }}">
                            <div class="row g-3">
                                <div class="col-md-2">
                                    <label class="form-label">Search</label>
                                    <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Serial/Customer...">
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
                                    <input type="number" class="form-control" name="min_price" value="{{ request('min_price') }}" placeholder="$">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bx bx-search me-1"></i>Filter
                                        </button>
                                        <a href="{{ route('super.sold-items') }}" class="btn btn-outline-secondary">
                                            <i class="bx bx-refresh me-1"></i>Reset
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
                        <h5 class="card-title mb-0"><i class="bx bx-check-circle me-2"></i>Sold Items</h5>
                        <div>
                            <button class="btn btn-success btn-sm" onclick="exportData()">
                                <i class="bx bx-export me-1"></i>Export
                            </button>
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
                                        <th>Kind</th>
                                        <th>Weight</th>
                                        <th>Price</th>
                                        <th>Payment Method</th>
                                        <th>Sold Date</th>
                                        <th>Stars</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($soldItems as $item)
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
                                            <span class="badge bg-info">{{ $item->kind }}</span>
                                        </td>
                                        <td>
                                            <strong class="text-success">{{ $item->weight }}g</strong>
                                        </td>
                                        <td>
                                            <strong class="text-success">${{ number_format($item->price, 2) }}</strong>
                                        </td>
                                        <td>
                                            @if($item->payment_method)
                                                <span class="badge bg-success">{{ $item->payment_method }}</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->sold_date)
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($item->sold_date)->format('M d, Y H:i') }}</small>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->stars)
                                                <span class="badge bg-warning text-dark">{{ $item->stars }}</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-outline-info" 
                                                        onclick="viewSoldItemDetails('{{ $item->id }}')" 
                                                        title="View Details">
                                                    <i class="bx bx-show"></i>
                                                </button>
                                                @if($item->customer)
                                                <a href="{{ route('super.customers.show', $item->customer->id) }}" 
                                                   class="btn btn-sm btn-outline-primary ms-1" 
                                                   title="View Customer">
                                                    <i class="bx bx-user"></i>
                                                </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $soldItems->appends(request()->query())->links('vendor.pagination.custom-super') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sold Item Details Modal -->
    <div class="modal fade" id="soldItemDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sold Item Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="soldItemDetailsContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewSoldItemDetails(itemId) {
            // You can implement a route to get sold item details
            fetch(`/sold-item-details/${itemId}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('soldItemDetailsContent').innerHTML = data;
                    new bootstrap.Modal(document.getElementById('soldItemDetailsModal')).show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading item details');
                });
        }

        function exportData() {
            const params = new URLSearchParams(window.location.search);
            window.location.href = '/export-sales?' + params.toString();
        }
    </script>
</body>
</html> 