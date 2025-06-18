<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('components.navbar')
    <title>Production Orders - Elmawardy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar-brand {
            font-weight: bold;
            color: #fff !important;
        }
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: 1px solid rgba(0, 0, 0, 0.125);
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom: none;
        }
        .table {
            margin-bottom: 0;
        }
        .table thead th {
            border-top: none;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.875rem;
            letter-spacing: 0.5px;
        }
        .progress {
            border-radius: 0.5rem;
        }
        .btn {
            border-radius: 0.375rem;
        }
        .alert {
            border-radius: 0.5rem;
        }
        .badge {
            font-size: 0.75rem;
            padding: 0.5em 0.75em;
        }
        .empty-state {
            padding: 4rem 2rem;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }
        .action-buttons .btn {
            border-radius: 0.25rem;
            font-size: 0.875rem;
            padding: 0.375rem 0.75rem;
        }
    </style>
</head>
<body>

    <!-- Main Content -->
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-clipboard-list"></i>
                            Production Orders Management
                        </h4>
                        <div>
                            <a href="{{ route('production.import.show') }}" class="btn btn-success me-2">
                                <i class="fas fa-file-excel"></i> Import Excel
                            </a>
                            <a href="{{ route('production.create') }}" class="btn btn-light">
                                <i class="fas fa-plus"></i> Add New Order
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th><i class="fas fa-hashtag"></i> ID</th>
                                        <th><i class="fas fa-cube"></i> Model</th>
                                        <th><i class="fas fa-palette"></i> Gold Color</th>
                                        <th><i class="fas fa-calculator"></i> Total Quantity</th>
                                        <th><i class="fas fa-clock"></i> Not Finished</th>
                                        <th><i class="fas fa-chart-line"></i> Progress</th>
                                        <th><i class="fas fa-calendar"></i> Order Date</th>
                                        <th><i class="fas fa-cogs"></i> Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($productionOrders as $order)
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-primary">#{{ $order->id }}</span>
                                            </td>
                                            <td>
                                                <strong class="text-dark">{{ $order->model }}</strong>
                                            </td>
                                            <td>
                                                @if($order->gold_color)
                                                    @php
                                                        $colorClass = match($order->gold_color) {
                                                            'Yellow' => 'bg-warning text-dark',
                                                            'White' => 'bg-light text-dark',
                                                            'Rose' => 'bg-danger text-white',
                                                            default => 'bg-secondary text-white'
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $colorClass }}">
                                                        <i class="fas fa-circle"></i> {{ $order->gold_color }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">
                                                        <i class="fas fa-question"></i> N/A
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    <i class="fas fa-boxes"></i> {{ $order->quantity }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-hourglass-half"></i> {{ $order->not_finished }}
                                                </span>
                                            </td>
                                            <td style="min-width: 150px;">
                                                @php
                                                    $progress = $order->quantity > 0 
                                                        ? round((($order->quantity - $order->not_finished) / $order->quantity) * 100, 2)
                                                        : 0;
                                                    $progressClass = $progress >= 100 ? 'bg-success' : ($progress >= 50 ? 'bg-warning' : 'bg-danger');
                                                @endphp
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar {{ $progressClass }}" 
                                                         role="progressbar" 
                                                         style="width: {{ $progress }}%"
                                                         aria-valuenow="{{ $progress }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                        {{ $progress }}%
                                                    </div>
                                                </div>
                                                <small class="text-muted">
                                                    {{ $order->quantity - $order->not_finished }} / {{ $order->quantity }} completed
                                                </small>
                                            </td>
                                            <td>
                                                <span class="text-muted">
                                                    <i class="fas fa-calendar-day"></i>
                                                    {{ $order->order_date->format('d-m-Y') }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group action-buttons" role="group">
                                                    <a href="{{ route('production.edit', $order) }}" 
                                                       class="btn btn-sm btn-outline-primary"
                                                       title="Edit Order">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    <form action="{{ route('production.destroy', $order) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to delete production order #{{ $order->id }}? This action cannot be undone.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-outline-danger"
                                                                title="Delete Order">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">
                                                <div class="empty-state">
                                                    <i class="fas fa-inbox fa-4x text-muted mb-4"></i>
                                                    <h5 class="text-muted">No Production Orders Found</h5>
                                                    <p class="text-muted mb-4">
                                                        You haven't created any production orders yet. 
                                                        Get started by creating your first order or importing data from Excel.
                                                    </p>
                                                    <div>
                                                        <a href="{{ route('production.create') }}" class="btn btn-primary me-2">
                                                            <i class="fas fa-plus"></i> Create First Order
                                                        </a>
                                                        <a href="{{ route('production.import.show') }}" class="btn btn-success">
                                                            <i class="fas fa-file-excel"></i> Import from Excel
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Summary Statistics -->
                        @if($productionOrders->count() > 0)
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="alert alert-light border">
                                        <div class="row text-center">
                                            <div class="col-md-3">
                                                <h5 class="text-primary mb-1">{{ $productionOrders->count() }}</h5>
                                                <small class="text-muted">Total Orders</small>
                                            </div>
                                            <div class="col-md-3">
                                                <h5 class="text-success mb-1">{{ $productionOrders->sum('quantity') }}</h5>
                                                <small class="text-muted">Total Quantity</small>
                                            </div>
                                            <div class="col-md-3">
                                                <h5 class="text-warning mb-1">{{ $productionOrders->sum('not_finished') }}</h5>
                                                <small class="text-muted">Not Finished</small>
                                            </div>
                                            <div class="col-md-3">
                                                <h5 class="text-info mb-1">{{ $productionOrders->sum('quantity') - $productionOrders->sum('not_finished') }}</h5>
                                                <small class="text-muted">Completed</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Pagination -->
                        @if($productionOrders->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $productionOrders->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add smooth transitions for hover effects
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });
    </script>
</body>
</html>