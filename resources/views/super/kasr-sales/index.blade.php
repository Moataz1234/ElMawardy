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

        <!-- Tabs Navigation -->
        <div class="row">
            <div class="col-12">
                <div class="card kasr-sales-card">
                    <div class="card-header bg-white">
                        <ul class="nav nav-tabs card-header-tabs" id="kasrTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="all-kasr-tab" data-bs-toggle="tab" data-bs-target="#all-kasr" type="button" role="tab">
                                    <i class="bx bx-list-ul me-2"></i>All Kasr Sales
                                    <span class="badge bg-primary ms-2">{{ $kasrSales->total() }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pending-kasr-tab" data-bs-toggle="tab" data-bs-target="#pending-kasr" type="button" role="tab">
                                    <i class="bx bx-time me-2"></i>Pending
                                    <span class="badge bg-warning text-dark ms-2">{{ $pendingKasrCount }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="completed-kasr-tab" data-bs-toggle="tab" data-bs-target="#completed-kasr" type="button" role="tab">
                                    <i class="bx bx-check-circle me-2"></i>Completed
                                    <span class="badge bg-success ms-2">{{ $completedKasrCount }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="kasr-items-tab" data-bs-toggle="tab" data-bs-target="#kasr-items" type="button" role="tab">
                                    <i class="bx bx-package me-2"></i>Kasr Items
                                    <span class="badge bg-info ms-2">{{ $kasrSales->sum(function($sale) { return $sale->items->count(); }) }}</span>
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="kasrTabsContent">
                            <!-- All Kasr Sales Tab -->
                            <div class="tab-pane fade show active" id="all-kasr" role="tabpanel">
                                <!-- Filters -->
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <form method="GET" action="{{ route('super.kasr-sales') }}">
                                            <div class="row g-3">
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search customer name/phone...">
                                                </div>
                                                <div class="col-md-2">
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
                                                    <select class="form-select" name="status">
                                                        <option value="">All Status</option>
                                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="date" class="form-control" name="date" value="{{ request('date') }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <button type="submit" class="btn btn-primary me-2">
                                                        <i class="bx bx-search me-1"></i>Filter
                                                    </button>
                                                    <a href="{{ route('super.kasr-sales') }}" class="btn btn-outline-secondary">
                                                        <i class="bx bx-refresh me-1"></i>Reset
                                                    </a>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Customer</th>
                                                <th>Shop</th>
                                                <th>Items Count</th>
                                                <th>Total Weight</th>
                                                <th>Offered Price</th>
                                                <th>Status</th>
                                                <th>Order Date</th>
                                                <th>Image</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($kasrSales as $sale)
                                            <tr>
                                                <td><strong>#{{ $sale->id }}</strong></td>
                                                <td>
                                                    <div>
                                                        <strong>{{ $sale->customer_name }}</strong><br>
                                                        @if($sale->customer_phone)
                                                            <small class="text-muted">{{ $sale->customer_phone }}</small>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark">{{ $sale->shop_name }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ $sale->items->count() }}</span>
                                                </td>
                                                <td>
                                                    <strong class="text-success">{{ $sale->getTotalWeight() }}g</strong><br>
                                                    <small class="text-muted">Net: {{ $sale->getTotalNetWeight() }}g</small>
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
                                                        {{ \Carbon\Carbon::parse($sale->order_date)->format('M d, Y') }}
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($sale->image_path)
                                                        <img src="{{ asset('storage/' . $sale->image_path) }}" alt="Kasr Image" class="kasr-image" data-bs-toggle="modal" data-bs-target="#imageModal" onclick="showImage('{{ asset('storage/' . $sale->image_path) }}')">
                                                    @else
                                                        <span class="text-muted">No image</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button class="btn btn-sm btn-outline-info" 
                                                                onclick="viewKasrItems('{{ $sale->id }}')" 
                                                                title="View Items">
                                                            <i class="bx bx-list-ul"></i>
                                                        </button>
                                                        <a href="{{ route('kasr-sales.edit', $sale->id) }}" 
                                                           class="btn btn-sm btn-outline-primary ms-1" 
                                                           title="Edit Sale">
                                                            <i class="bx bx-edit"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-center">
                                    {{ $kasrSales->appends(request()->query())->links('vendor.pagination.custom-super') }}
                                </div>
                            </div>

                            <!-- Pending Kasr Sales Tab -->
                            <div class="tab-pane fade" id="pending-kasr" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Customer</th>
                                                <th>Shop</th>
                                                <th>Items</th>
                                                <th>Weight</th>
                                                <th>Order Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($kasrSales->where('status', 'pending') as $sale)
                                            <tr>
                                                <td><strong>#{{ $sale->id }}</strong></td>
                                                <td>{{ $sale->customer_name }}</td>
                                                <td>{{ $sale->shop_name }}</td>
                                                <td>{{ $sale->items->count() }}</td>
                                                <td>{{ $sale->getTotalWeight() }}g</td>
                                                <td>{{ $sale->order_date ? \Carbon\Carbon::parse($sale->order_date)->format('M d, Y') : 'N/A' }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-info" onclick="viewKasrItems('{{ $sale->id }}')">
                                                        <i class="bx bx-show"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Completed Kasr Sales Tab -->
                            <div class="tab-pane fade" id="completed-kasr" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Customer</th>
                                                <th>Shop</th>
                                                <th>Weight</th>
                                                <th>Price</th>
                                                <th>Completion Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($kasrSales->where('status', 'completed') as $sale)
                                            <tr>
                                                <td><strong>#{{ $sale->id }}</strong></td>
                                                <td>{{ $sale->customer_name }}</td>
                                                <td>{{ $sale->shop_name }}</td>
                                                <td>{{ $sale->getTotalWeight() }}g</td>
                                                <td>
                                                    @if($sale->offered_price)
                                                        ${{ number_format($sale->offered_price, 2) }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>{{ $sale->updated_at->format('M d, Y') }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-info" onclick="viewKasrItems('{{ $sale->id }}')">
                                                        <i class="bx bx-show"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Kasr Items Tab -->
                            <div class="tab-pane fade" id="kasr-items" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Sale ID</th>
                                                <th>Customer</th>
                                                <th>Kind</th>
                                                <th>Metal Purity</th>
                                                <th>Weight</th>
                                                <th>Net Weight</th>
                                                <th>Item Type</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($kasrSales as $sale)
                                                @foreach($sale->items as $item)
                                                <tr>
                                                    <td><strong>#{{ $sale->id }}</strong></td>
                                                    <td>{{ $sale->customer_name }}</td>
                                                    <td>
                                                        @if($item->kind)
                                                            <span class="badge bg-primary">{{ $item->kind }}</span>
                                                        @else
                                                            <span class="text-muted">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($item->metal_purity)
                                                            <span class="badge bg-success">{{ $item->metal_purity }}</span>
                                                        @else
                                                            <span class="text-muted">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td><strong class="text-success">{{ $item->weight }}g</strong></td>
                                                    <td><strong class="text-primary">{{ $item->net_weight }}g</strong></td>
                                                    <td>
                                                        @if($item->item_type)
                                                            <span class="badge bg-info">{{ $item->item_type }}</span>
                                                        @else
                                                            <span class="text-muted">N/A</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
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
                    <h5 class="modal-title">Kasr Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="Kasr Image" class="img-fluid">
                </div>
            </div>
        </div>
    </div>

    <!-- Kasr Items Modal -->
    <div class="modal fade" id="kasrItemsModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Kasr Sale Items</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="kasrItemsContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showImage(imageSrc) {
            document.getElementById('modalImage').src = imageSrc;
        }

        function viewKasrItems(saleId) {
            fetch(`/admin/kasr-sales/${saleId}/items`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('kasrItemsContent').innerHTML = data;
                    new bootstrap.Modal(document.getElementById('kasrItemsModal')).show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading kasr items');
                });
        }
    </script>
</body>
</html> 