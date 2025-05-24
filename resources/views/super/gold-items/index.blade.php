<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gold Items Management - Super User</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- BoxIcons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .gold-items-card {
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
    </style>
</head>
<body>
    <!-- Navbar -->
    @include('components.navbar')

    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="fw-bold text-dark">Gold Items Management</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('super.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item active">Gold Items</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('super.gold-items') }}">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Search Serial/Model</label>
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
                                    <label class="form-label">Kind</label>
                                    <select class="form-select" name="kind">
                                        <option value="">All Kinds</option>
                                        <option value="خاتم" {{ request('kind') == 'خاتم' ? 'selected' : '' }}>خاتم</option>
                                        <option value="دبلة" {{ request('kind') == 'دبلة' ? 'selected' : '' }}>دبلة</option>
                                        <option value="سلسلة" {{ request('kind') == 'سلسلة' ? 'selected' : '' }}>سلسلة</option>
                                        <option value="أسورة" {{ request('kind') == 'أسورة' ? 'selected' : '' }}>أسورة</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" name="status">
                                        <option value="">All Status</option>
                                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                                        <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>Sold</option>
                                        <option value="reserved" {{ request('status') == 'reserved' ? 'selected' : '' }}>Reserved</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bx bx-search me-1"></i>Filter
                                        </button>
                                        <a href="{{ route('super.gold-items') }}" class="btn btn-outline-secondary">
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

        <!-- Gold Items Table -->
        <div class="row">
            <div class="col-12">
                <div class="card gold-items-card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="bx bx-diamond me-2"></i>All Gold Items</h5>
                        <div>
                            <span class="badge bg-info fs-6">Total: {{ $goldItems->total() }}</span>
                            <span class="badge bg-success fs-6">Weight: {{ $goldItems->sum('weight') }}g</span>
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
                                        <th>Kind</th>
                                        <th>Weight</th>
                                        <th>Gold Color</th>
                                        <th>Metal Purity</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($goldItems as $item)
                                    <tr>
                                        <td>
                                            <span class="badge bg-primary">{{ $item->serial_number }}</span>
                                        </td>
                                        <td>{{ $item->model }}</td>
                                        <td>
                                            <span class="badge bg-light text-dark">{{ $item->shop_name }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $item->kind }}</span>
                                        </td>
                                        <td>
                                            <strong class="text-success">{{ $item->weight }}g</strong>
                                        </td>
                                        <td>
                                            @if($item->gold_color)
                                                <span class="badge bg-warning text-dark">{{ $item->gold_color }}</span>
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
                                        <td>
                                            <span class="badge {{ 
                                                $item->status == 'available' ? 'bg-success' : 
                                                ($item->status == 'sold' ? 'bg-danger' : 'bg-warning text-dark')
                                            }}">
                                                {{ ucfirst($item->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $item->created_at->format('M d, Y') }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-outline-info" 
                                                        onclick="viewItemDetails('{{ $item->serial_number }}')" 
                                                        title="View Details">
                                                    <i class="bx bx-show"></i>
                                                </button>
                                                <a href="{{ route('item.details', $item->serial_number) }}" 
                                                   class="btn btn-sm btn-outline-primary ms-1" 
                                                   title="Full Details">
                                                    <i class="bx bx-detail"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        @if($goldItems->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $goldItems->appends(request()->query())->links('vendor.pagination.custom-super') }}
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
                    <h5 class="modal-title">Item Details</h5>
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
            fetch(`/item-details/${serialNumber}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('itemDetailsContent').innerHTML = data;
                    new bootstrap.Modal(document.getElementById('itemDetailsModal')).show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading item details');
                });
        }
    </script>
</body>
</html> 