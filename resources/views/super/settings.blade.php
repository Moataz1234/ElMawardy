<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Super User</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- BoxIcons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .settings-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .setting-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
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
                <h2 class="fw-bold text-dark">System Settings</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('super.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item active">Settings</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Settings Sections -->
        <div class="row">
            <!-- Gold Price Settings -->
            <div class="col-lg-6 mb-4">
                <div class="card settings-card">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0"><i class="bx bx-dollar-circle me-2"></i>Gold Price Management</h5>
                    </div>
                    <div class="card-body">
                        @if($goldPrices)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Price Type</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Gold Buy</td>
                                        <td><strong class="text-success">${{ $goldPrices->gold_buy }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Gold Sell</td>
                                        <td><strong class="text-primary">${{ $goldPrices->gold_sell }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Percent</td>
                                        <td><span class="badge bg-warning text-dark">{{ $goldPrices->percent }}%</span></td>
                                    </tr>
                                    <tr>
                                        <td>Dollar Price</td>
                                        <td><strong>${{ $goldPrices->dollar_price }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Gold with Work</td>
                                        <td><strong>${{ $goldPrices->gold_with_work }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Gold in Diamond</td>
                                        <td><strong>${{ $goldPrices->gold_in_diamond }}</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('gold_prices.create') }}" class="btn btn-primary">
                                <i class="bx bx-edit me-2"></i>Update Prices
                            </a>
                        </div>
                        @else
                        <div class="text-center py-4">
                            <i class="bx bx-dollar-circle fs-1 text-muted"></i>
                            <h6 class="text-muted mt-2">No gold prices configured</h6>
                            <a href="{{ route('gold_prices.create') }}" class="btn btn-primary mt-2">
                                <i class="bx bx-plus me-2"></i>Set Initial Prices
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- System Information -->
            <div class="col-lg-6 mb-4">
                <div class="card settings-card">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0"><i class="bx bx-info-circle me-2"></i>System Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <strong><i class="bx bx-server me-2"></i>Application:</strong><br>
                                <span class="text-muted">ElMawardy Gold Management System</span>
                            </div>
                            <div class="col-12 mb-3">
                                <strong><i class="bx bx-code me-2"></i>Laravel Version:</strong><br>
                                <span class="text-muted">{{ app()->version() }}</span>
                            </div>
                            <div class="col-12 mb-3">
                                <strong><i class="bx bx-time me-2"></i>System Time:</strong><br>
                                <span class="text-muted">{{ now()->format('Y-m-d H:i:s T') }}</span>
                            </div>
                            <div class="col-12 mb-3">
                                <strong><i class="bx bx-user me-2"></i>Total Users:</strong><br>
                                <span class="badge bg-info">{{ \App\Models\User::count() }}</span>
                            </div>
                            <div class="col-12">
                                <strong><i class="bx bx-store me-2"></i>Total Shops:</strong><br>
                                <span class="badge bg-success">{{ \App\Models\Shop::count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="col-12">
                <div class="card settings-card">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0"><i class="bx bx-cog me-2"></i>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="setting-section text-center">
                                    <i class="bx bx-dollar-circle fs-1 mb-2"></i>
                                    <h6>Gold Prices</h6>
                                    <p class="small mb-3">Update current gold market prices</p>
                                    <a href="{{ route('gold_prices.create') }}" class="btn btn-light btn-sm">
                                        <i class="bx bx-edit me-1"></i>Update
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="setting-section text-center">
                                    <i class="bx bx-chart fs-1 mb-2"></i>
                                    <h6>Analytics</h6>
                                    <p class="small mb-3">View system analytics and reports</p>
                                    <a href="{{ route('super.analytics') }}" class="btn btn-light btn-sm">
                                        <i class="bx bx-chart me-1"></i>View
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="setting-section text-center">
                                    <i class="bx bx-user-plus fs-1 mb-2"></i>
                                    <h6>User Management</h6>
                                    <p class="small mb-3">Manage system users and permissions</p>
                                    <a href="{{ route('super.users') }}" class="btn btn-light btn-sm">
                                        <i class="bx bx-user me-1"></i>Manage
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="setting-section text-center">
                                    <i class="bx bx-export fs-1 mb-2"></i>
                                    <h6>Export Data</h6>
                                    <p class="small mb-3">Export sales and inventory data</p>
                                    <a href="{{ route('export.sales') }}" class="btn btn-light btn-sm">
                                        <i class="bx bx-export me-1"></i>Export
                                    </a>
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