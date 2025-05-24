<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Details - {{ $customer->first_name }} {{ $customer->last_name }}</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- BoxIcons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .customer-detail-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .customer-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 48px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .info-card {
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
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold text-dark">Customer Details</h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('super.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('super.customers') }}" class="text-decoration-none">Customers</a></li>
                                <li class="breadcrumb-item active">{{ $customer->first_name }} {{ $customer->last_name }}</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="{{ route('super.customers.edit', $customer->id) }}" class="btn btn-primary me-2">
                            <i class="bx bx-edit me-2"></i>Edit Customer
                        </a>
                        <a href="{{ route('super.customers') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-arrow-back me-2"></i>Back to Customers
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="row mb-4">
            <!-- Customer Profile -->
            <div class="col-lg-4">
                <div class="card customer-detail-card">
                    <div class="card-body text-center">
                        <div class="customer-avatar mx-auto mb-3">
                            {{ substr($customer->first_name, 0, 1) }}{{ substr($customer->last_name, 0, 1) }}
                        </div>
                        <h4 class="fw-bold">{{ $customer->first_name }} {{ $customer->last_name }}</h4>
                        <p class="text-muted mb-3">Customer ID: #{{ $customer->id }}</p>
                        
                        <div class="row text-start">
                            <div class="col-12 mb-3">
                                <strong><i class="bx bx-envelope me-2"></i>Email:</strong><br>
                                <span class="text-muted">{{ $customer->email ?? 'N/A' }}</span>
                            </div>
                            <div class="col-12 mb-3">
                                <strong><i class="bx bx-phone me-2"></i>Phone:</strong><br>
                                <span class="text-muted">{{ $customer->phone_number }}</span>
                            </div>
                            <div class="col-12 mb-3">
                                <strong><i class="bx bx-map me-2"></i>Address:</strong><br>
                                <span class="text-muted">{{ $customer->address ?? 'N/A' }}</span>
                            </div>
                            <div class="col-12 mb-3">
                                <strong><i class="bx bx-credit-card me-2"></i>Payment Method:</strong><br>
                                <span class="text-muted">{{ $customer->payment_method ?? 'N/A' }}</span>
                            </div>
                            <div class="col-12">
                                <strong><i class="bx bx-calendar me-2"></i>Member Since:</strong><br>
                                <span class="text-muted">{{ $customer->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Statistics -->
            <div class="col-lg-8">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="card info-card">
                            <div class="card-body text-center">
                                <i class="bx bx-shopping-bag fs-1 mb-2"></i>
                                <h3 class="fw-bold">{{ $customer->goldItemsSold->count() }}</h3>
                                <p class="mb-0">Total Purchases</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card info-card">
                            <div class="card-body text-center">
                                <i class="bx bx-dollar-circle fs-1 mb-2"></i>
                                <h3 class="fw-bold">${{ number_format($customer->goldItemsSold->sum('price'), 2) }}</h3>
                                <p class="mb-0">Total Spent</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card info-card">
                            <div class="card-body text-center">
                                <i class="bx bx-weight fs-1 mb-2"></i>
                                <h3 class="fw-bold">{{ $customer->goldItemsSold->sum('weight') }}g</h3>
                                <p class="mb-0">Total Weight</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card info-card">
                            <div class="card-body text-center">
                                <i class="bx bx-calendar fs-1 mb-2"></i>
                                <h3 class="fw-bold">
                                    @if($customer->goldItemsSold->count() > 0)
                                        {{ $customer->goldItemsSold->last()->sold_date ? \Carbon\Carbon::parse($customer->goldItemsSold->last()->sold_date)->diffForHumans() : 'N/A' }}
                                    @else
                                        Never
                                    @endif
                                </h3>
                                <p class="mb-0">Last Purchase</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card customer-detail-card">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0"><i class="bx bx-cog me-2"></i>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-outline-primary" onclick="sendEmail()">
                                <i class="bx bx-envelope me-2"></i>Send Email
                            </button>
                            <button class="btn btn-outline-success" onclick="callCustomer()">
                                <i class="bx bx-phone me-2"></i>Call Customer
                            </button>
                            <button class="btn btn-outline-info" onclick="viewPurchaseHistory()">
                                <i class="bx bx-history me-2"></i>Purchase History
                            </button>
                            <a href="{{ route('super.customers.edit', $customer->id) }}" class="btn btn-outline-warning">
                                <i class="bx bx-edit me-2"></i>Edit Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Purchase History -->
        <div class="row">
            <div class="col-12">
                <div class="card customer-detail-card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="bx bx-history me-2"></i>Purchase History</h5>
                        <span class="badge bg-info">{{ $customer->goldItemsSold->count() }} Items</span>
                    </div>
                    <div class="card-body">
                        @if($customer->goldItemsSold->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Serial Number</th>
                                        <th>Model</th>
                                        <th>Shop</th>
                                        <th>Kind</th>
                                        <th>Weight</th>
                                        <th>Price</th>
                                        <th>Payment Method</th>
                                        <th>Sold Date</th>
                                        <th>Stars</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customer->goldItemsSold->sortByDesc('sold_date') as $item)
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
                                                <div class="text-warning">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $item->stars)
                                                            <i class="bx bxs-star"></i>
                                                        @else
                                                            <i class="bx bx-star"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-5">
                            <i class="bx bx-shopping-bag fs-1 text-muted"></i>
                            <h5 class="text-muted mt-3">No Purchase History</h5>
                            <p class="text-muted">This customer hasn't made any purchases yet.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function sendEmail() {
            @if($customer->email)
                window.location.href = 'mailto:{{ $customer->email }}';
            @else
                alert('No email address available for this customer.');
            @endif
        }

        function callCustomer() {
            window.location.href = 'tel:{{ $customer->phone_number }}';
        }

        function viewPurchaseHistory() {
            document.querySelector('.card:last-child').scrollIntoView({ behavior: 'smooth' });
        }
    </script>
</body>
</html> 