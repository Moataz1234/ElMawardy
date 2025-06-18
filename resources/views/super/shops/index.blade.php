<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shops Management - Super User</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- BoxIcons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .shop-card {
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
                <h2 class="fw-bold text-dark">Shops Management</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('super.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item active">Shops</li>
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

        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card filter-card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('super.shops') }}">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Search Shop Name/Address</label>
                                    <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search by shop name or address...">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bx bx-search me-1"></i>Filter
                                        </button>
                                        <a href="{{ route('super.shops') }}" class="btn btn-outline-secondary">
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

        <!-- Shops Table -->
        <div class="row">
            <div class="col-12">
                <div class="card shop-card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="bx bx-store me-2"></i>All Shops</h5>
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('super.shops') }}?{{ http_build_query(request()->query()) }}&export=excel" 
                               class="btn btn-outline-success btn-sm">
                                <i class="bx bx-export me-1"></i>Export
                            </a>
                            <span class="badge bg-info fs-6">Total: {{ $shops->total() }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Shop Name</th>
                                        <th>Address</th>
                                        <th>Users Count</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($shops as $shop)
                                    <tr>
                                        <td><strong>#{{ $shop->id }}</strong></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                                     style="width: 40px; height: 40px; color: white; font-weight: bold;">
                                                    {{ substr($shop->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $shop->name }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($shop->address)
                                                <span class="text-muted">{{ $shop->address }}</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $shop->users->count() }} users</span>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $shop->created_at}}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('super.shops.edit', $shop->id) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="Edit Shop">
                                                    <i class="bx bx-edit"></i>
                                                </a>
                                                <button class="btn btn-sm btn-outline-info ms-1" 
                                                        onclick="viewShopDetails('{{ $shop->id }}')" 
                                                        title="View Users">
                                                    <i class="bx bx-show"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="bx bx-store fs-1 d-block mb-2"></i>
                                                No shops found
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        @if($shops->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $shops->appends(request()->query())->links('vendor.pagination.custom-super') }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Shop Details Modal -->
    <div class="modal fade" id="shopDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Shop Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="shopDetailsContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewShopDetails(shopId) {
            // This would fetch shop details - implement as needed
            alert('View details for shop ID: ' + shopId);
        }
    </script>
</body>
</html> 