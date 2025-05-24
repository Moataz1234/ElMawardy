<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers Management - Super User</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- BoxIcons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .customer-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .customer-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 16px;
        }
        /* Custom Pagination Styling */
        .pagination {
            justify-content: center;
        }
        .pagination .page-link {
            border: none;
            border-radius: 8px;
            margin: 0 2px;
            color: #667eea;
            font-weight: 500;
            padding: 0.5rem 0.75rem;
        }
        .pagination .page-link:hover {
            background-color: #f8f9fa;
            color: #4c63d2;
            transform: translateY(-1px);
            transition: all 0.2s ease;
        }
        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .pagination .page-item.disabled .page-link {
            color: #6c757d;
            background-color: transparent;
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
                        <h2 class="fw-bold text-dark">Customers Management</h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('super.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                                <li class="breadcrumb-item active">Customers</li>
                            </ol>
                        </nav>
                    </div>
                    <a href="{{ route('super.customers.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus me-2"></i>Add New Customer
                    </a>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bx bx-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Customers Table -->
        <div class="row">
            <div class="col-12">
                <div class="card customer-card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="bx bx-users me-2"></i>All Customers</h5>
                        <div>
                            <span class="badge bg-info fs-6">Total: {{ $customers->total() }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Customer</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Address</th>
                                        <th>Payment Method</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customers as $customer)
                                    <tr>
                                        <td><strong>{{ $customer->id }}</strong></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="customer-avatar bg-primary me-3">
                                                    {{ substr($customer->first_name, 0, 1) }}{{ substr($customer->last_name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $customer->first_name }} {{ $customer->last_name }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($customer->email)
                                                <span class="text-muted">{{ $customer->email }}</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">{{ $customer->phone_number }}</span>
                                        </td>
                                        <td>
                                            @if($customer->address)
                                                <span class="text-muted">{{ Str::limit($customer->address, 30) }}</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($customer->payment_method)
                                                <span class="badge bg-success">{{ $customer->payment_method }}</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $customer->created_at->format('M d, Y H:i') }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('super.customers.show', $customer->id) }}" 
                                                   class="btn btn-sm btn-outline-info" 
                                                   title="View Customer">
                                                    <i class="bx bx-show"></i>
                                                </a>
                                                <a href="{{ route('super.customers.edit', $customer->id) }}" 
                                                   class="btn btn-sm btn-outline-primary ms-1" 
                                                   title="Edit Customer">
                                                    <i class="bx bx-edit"></i>
                                                </a>
                                                <form method="POST" 
                                                      action="{{ route('super.customers.delete', $customer->id) }}" 
                                                      style="display: inline;" 
                                                      onsubmit="return confirm('Are you sure you want to delete this customer?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-danger ms-1" 
                                                            title="Delete Customer">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        @if($customers->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            <nav aria-label="Customers pagination">
                                {{ $customers->links() }}
                            </nav>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 