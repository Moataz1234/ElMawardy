<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super User Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- BoxIcons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .stat-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-2px);
        }
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
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
                <h2 class="fw-bold text-dark">Super User Dashboard</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-primary me-3">
                                <i class="bx bx-cube"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Total Gold Items</h6>
                                <h3 class="mb-0 fw-bold">{{ $stats['total_gold_items'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-success me-3">
                                <i class="bx bx-check-circle"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Total Sold Items</h6>
                                <h3 class="mb-0 fw-bold">{{ $stats['total_sold_items'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-info me-3">
                                <i class="bx bx-user"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Total Users</h6>
                                <h3 class="mb-0 fw-bold">{{ $stats['total_users'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-warning me-3">
                                <i class="bx bx-store"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Total Shops</h6>
                                <h3 class="mb-0 fw-bold">{{ $stats['total_shops'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Requests -->
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-muted mb-1">Pending Add Requests</h6>
                                <h3 class="mb-0 fw-bold text-danger">{{ $stats['pending_add_requests'] }}</h3>
                            </div>
                            <a href="{{ route('super.requests') }}" class="btn btn-sm btn-primary">View</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-muted mb-1">Pending Transfer Requests</h6>
                                <h3 class="mb-0 fw-bold text-danger">{{ $stats['pending_transfer_requests'] }}</h3>
                            </div>
                            <a href="{{ route('super.requests') }}" class="btn btn-sm btn-primary">View</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-muted mb-1">Pending Sale Requests</h6>
                                <h3 class="mb-0 fw-bold text-danger">{{ $stats['pending_sale_requests'] }}</h3>
                            </div>
                            <a href="{{ route('super.requests') }}" class="btn btn-sm btn-primary">View</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-muted mb-1">Pending Orders</h6>
                                <h3 class="mb-0 fw-bold text-danger">{{ $stats['pending_orders'] }}</h3>
                            </div>
                            <a href="{{ route('super.orders') }}" class="btn btn-sm btn-primary">View</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-lg-3 col-md-6">
                                <a href="{{ route('super.users') }}" class="btn btn-primary w-100 py-3">
                                    <i class="bx bx-user fs-4 d-block mb-2"></i>
                                    Manage Users
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <a href="{{ route('super.shops') }}" class="btn btn-info w-100 py-3">
                                    <i class="bx bx-store fs-4 d-block mb-2"></i>
                                    Manage Shops
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <a href="{{ route('super.gold-items') }}" class="btn btn-warning w-100 py-3">
                                    <i class="bx bx-cube fs-4 d-block mb-2"></i>
                                    Gold Items
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <a href="{{ route('super.models') }}" class="btn btn-success w-100 py-3">
                                    <i class="bx bx-grid fs-4 d-block mb-2"></i>
                                    Models
                                </a>
                            </div>
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-lg-3 col-md-6">
                                <a href="{{ route('super.requests') }}" class="btn btn-danger w-100 py-3">
                                    <i class="bx bx-time fs-4 d-block mb-2"></i>
                                    All Requests
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <a href="{{ route('super.analytics') }}" class="btn btn-dark w-100 py-3">
                                    <i class="bx bx-chart fs-4 d-block mb-2"></i>
                                    Analytics
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <a href="{{ route('super.sold-items') }}" class="btn btn-secondary w-100 py-3">
                                    <i class="bx bx-check-circle fs-4 d-block mb-2"></i>
                                    Sold Items
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <a href="{{ route('super.settings') }}" class="btn btn-outline-primary w-100 py-3">
                                    <i class="bx bx-cog fs-4 d-block mb-2"></i>
                                    Settings
                                </a>
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
