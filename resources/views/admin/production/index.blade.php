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
        .filter-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 1px solid #dee2e6;
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
        .view-toggle {
            border-radius: 0.5rem;
            overflow: hidden;
        }
        .model-group-card {
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }
        .model-group-card:hover {
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
        }
        .model-header {
            background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
            border-bottom: 1px solid #dee2e6;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .model-header:hover {
            background: linear-gradient(135deg, #bbdefb 0%, #e1bee7 100%);
        }
        .model-details {
            display: none;
            background: #f8f9fa;
        }
        .details-table {
            margin: 0;
        }
        .details-table th {
            background: #e9ecef;
            font-size: 0.8rem;
        }
        .loading-spinner {
            display: none;
        }
        .pagination {
            border-radius: 0.5rem;
        }
        .pagination .page-link {
            border-radius: 0.375rem;
            margin: 0 0.125rem;
            border: 1px solid #dee2e6;
        }
        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea;
        }
        .sort-link {
            color: inherit;
            text-decoration: none;
        }
        .sort-link:hover {
            color: #667eea;
        }
        .sort-active {
            color: #667eea;
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

                        <!-- Search and Filter Section -->
                        <div class="card filter-card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0 text-dark">
                                    <i class="fas fa-filter"></i> Search & Filters
                                </h6>
                            </div>
                            <div class="card-body">
                                <form method="GET" action="{{ route('production.index') }}" id="filterForm">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-label">Search Model</label>
                                            <input type="text" class="form-control" name="search" 
                                                   value="{{ request('search') }}" 
                                                   placeholder="Enter model name...">
                                        </div>
                                        
                                        <div class="col-md-2">
                                            <label class="form-label">Gold Color</label>
                                            <select class="form-select" name="gold_color">
                                                <option value="">All Colors</option>
                                                @foreach($goldColors as $color)
                                                    <option value="{{ $color }}" 
                                                            {{ request('gold_color') == $color ? 'selected' : '' }}>
                                                        {{ $color }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-2">
                                            <label class="form-label">Progress Status</label>
                                            <select class="form-select" name="progress_status">
                                                <option value="">All Status</option>
                                                <option value="completed" {{ request('progress_status') == 'completed' ? 'selected' : '' }}>
                                                    Completed
                                                </option>
                                                <option value="in_progress" {{ request('progress_status') == 'in_progress' ? 'selected' : '' }}>
                                                    In Progress
                                                </option>
                                                <option value="not_started" {{ request('progress_status') == 'not_started' ? 'selected' : '' }}>
                                                    Not Started
                                                </option>
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-2">
                                            <label class="form-label">Date From</label>
                                            <input type="date" class="form-control" name="date_from" 
                                                   value="{{ request('date_from') }}">
                                        </div>
                                        
                                        <div class="col-md-2">
                                            <label class="form-label">Date To</label>
                                            <input type="date" class="form-control" name="date_to" 
                                                   value="{{ request('date_to') }}">
                                        </div>
                                        
                                        <div class="col-md-1 d-flex align-items-end">
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    @if(request()->hasAny(['search', 'gold_color', 'progress_status', 'date_from', 'date_to']))
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <a href="{{ route('production.index') }}" class="btn btn-outline-secondary btn-sm">
                                                    <i class="fas fa-times"></i> Clear Filters
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                </form>
                            </div>
                        </div>

                        <!-- View Toggle -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="btn-group view-toggle" role="group">
                                <input type="radio" class="btn-check" name="viewType" id="detailedView" checked>
                                <label class="btn btn-outline-primary" for="detailedView">
                                    <i class="fas fa-list"></i> Detailed View
                                </label>
                                
                                <input type="radio" class="btn-check" name="viewType" id="groupedView">
                                <label class="btn btn-outline-primary" for="groupedView">
                                    <i class="fas fa-layer-group"></i> Grouped by Model
                                </label>
                            </div>
                            
                            <div class="d-flex align-items-center">
                                <span class="text-muted me-3">
                                    Showing {{ $productionOrders->firstItem() ?? 0 }} to {{ $productionOrders->lastItem() ?? 0 }} 
                                    of {{ $productionOrders->total() }} results
                                </span>
                            </div>
                        </div>

                        <!-- Detailed View -->
                        <div id="detailed-view">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>
                                                <a href="{{ route('production.index', array_merge(request()->query(), ['sort_by' => 'id', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" 
                                                   class="sort-link {{ request('sort_by') == 'id' ? 'sort-active' : '' }}">
                                                    <i class="fas fa-hashtag"></i> ID
                                                    @if(request('sort_by') == 'id')
                                                        <i class="fas fa-sort-{{ request('sort_order') == 'asc' ? 'up' : 'down' }}"></i>
                                                    @endif
                                                </a>
                                            </th>
                                            <th>
                                                <a href="{{ route('production.index', array_merge(request()->query(), ['sort_by' => 'model', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" 
                                                   class="sort-link {{ request('sort_by') == 'model' ? 'sort-active' : '' }}">
                                                    <i class="fas fa-cube"></i> Model
                                                    @if(request('sort_by') == 'model')
                                                        <i class="fas fa-sort-{{ request('sort_order') == 'asc' ? 'up' : 'down' }}"></i>
                                                    @endif
                                                </a>
                                            </th>
                                            <th><i class="fas fa-palette"></i> Gold Color</th>
                                            <th>
                                                <a href="{{ route('production.index', array_merge(request()->query(), ['sort_by' => 'quantity', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" 
                                                   class="sort-link {{ request('sort_by') == 'quantity' ? 'sort-active' : '' }}">
                                                    <i class="fas fa-calculator"></i> Total Quantity
                                                    @if(request('sort_by') == 'quantity')
                                                        <i class="fas fa-sort-{{ request('sort_order') == 'asc' ? 'up' : 'down' }}"></i>
                                                    @endif
                                                </a>
                                            </th>
                                            <th><i class="fas fa-clock"></i> Not Finished</th>
                                            <th><i class="fas fa-chart-line"></i> Progress</th>
                                            <th>
                                                <a href="{{ route('production.index', array_merge(request()->query(), ['sort_by' => 'order_date', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" 
                                                   class="sort-link {{ request('sort_by') == 'order_date' ? 'sort-active' : '' }}">
                                                    <i class="fas fa-calendar"></i> Order Date
                                                    @if(request('sort_by') == 'order_date')
                                                        <i class="fas fa-sort-{{ request('sort_order') == 'asc' ? 'up' : 'down' }}"></i>
                                                    @endif
                                                </a>
                                            </th>
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
                                                            No production orders match your current filters. 
                                                            Try adjusting your search criteria or create a new order.
                                                        </p>
                                                        <div>
                                                            <a href="{{ route('production.create') }}" class="btn btn-primary me-2">
                                                                <i class="fas fa-plus"></i> Create New Order
                                                            </a>
                                                            <a href="{{ route('production.index') }}" class="btn btn-outline-secondary">
                                                                <i class="fas fa-times"></i> Clear Filters
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Grouped View -->
                        <div id="grouped-view" style="display: none;">
                            @forelse($groupedOrders as $group)
                                <div class="model-group-card">
                                    <div class="model-header p-3" data-model="{{ $group->model }}">
                                        <div class="row align-items-center">
                                            <div class="col-md-3">
                                                <h6 class="mb-0 text-primary">
                                                    <i class="fas fa-cube me-2"></i>
                                                    <strong>{{ $group->model }}</strong>
                                                </h6>
                                            </div>
                                            <div class="col-md-2">
                                                <span class="badge bg-info">
                                                    <i class="fas fa-boxes"></i> {{ $group->total_quantity }} Total
                                                </span>
                                            </div>
                                            <div class="col-md-2">
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-hourglass-half"></i> {{ $group->total_not_finished }} Pending
                                                </span>
                                            </div>
                                            <div class="col-md-2">
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check"></i> {{ $group->total_quantity - $group->total_not_finished }} Done
                                                </span>
                                            </div>
                                            <div class="col-md-2">
                                                <small class="text-muted">
                                                    <i class="fas fa-palette me-1"></i>
                                                    {{ str_replace(',', ', ', $group->gold_colors) }}
                                                </small>
                                            </div>
                                            <div class="col-md-1 text-end">
                                                <button class="btn btn-sm btn-outline-primary toggle-details">
                                                    <i class="fas fa-chevron-down"></i> Details
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="model-details p-3" data-model="{{ $group->model }}">
                                        <div class="d-flex justify-content-center">
                                            <div class="loading-spinner">
                                                <i class="fas fa-spinner fa-spin"></i> Loading details...
                                            </div>
                                        </div>
                                        <div class="details-content"></div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5">
                                    <i class="fas fa-inbox fa-4x text-muted mb-4"></i>
                                    <h5 class="text-muted">No Models Found</h5>
                                    <p class="text-muted">No production orders match your current filters.</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- Summary Statistics -->
                        @if($productionOrders->count() > 0)
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="alert alert-light border">
                                        <div class="row text-center">
                                            <div class="col-md-3">
                                                <h5 class="text-primary mb-1">{{ $productionOrders->total() }}</h5>
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

                        <!-- Enhanced Pagination -->
                        @if($productionOrders->hasPages())
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <div class="text-muted">
                                    Showing {{ $productionOrders->firstItem() }} to {{ $productionOrders->lastItem() }} 
                                    of {{ $productionOrders->total() }} results
                                </div>
                                <nav aria-label="Production orders pagination">
                                    {{ $productionOrders->links('pagination::bootstrap-4') }}
                                </nav>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });

            // View toggle functionality
            const detailedViewRadio = document.getElementById('detailedView');
            const groupedViewRadio = document.getElementById('groupedView');
            const detailedViewDiv = document.getElementById('detailed-view');
            const groupedViewDiv = document.getElementById('grouped-view');

            function toggleView() {
                if (groupedViewRadio.checked) {
                    detailedViewDiv.style.display = 'none';
                    groupedViewDiv.style.display = 'block';
                } else {
                    detailedViewDiv.style.display = 'block';
                    groupedViewDiv.style.display = 'none';
                }
            }

            detailedViewRadio.addEventListener('change', toggleView);
            groupedViewRadio.addEventListener('change', toggleView);

            // Model details toggle functionality
            document.querySelectorAll('.toggle-details').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const modelHeader = this.closest('.model-header');
                    const model = modelHeader.dataset.model;
                    const detailsDiv = modelHeader.nextElementSibling;
                    const icon = this.querySelector('i');
                    
                    if (detailsDiv.style.display === 'none' || !detailsDiv.style.display) {
                        // Show details
                        detailsDiv.style.display = 'block';
                        icon.className = 'fas fa-chevron-up';
                        this.innerHTML = '<i class="fas fa-chevron-up"></i> Hide';
                        
                        // Load details if not already loaded
                        if (!detailsDiv.dataset.loaded) {
                            loadModelDetails(model, detailsDiv);
                        }
                    } else {
                        // Hide details
                        detailsDiv.style.display = 'none';
                        icon.className = 'fas fa-chevron-down';
                        this.innerHTML = '<i class="fas fa-chevron-down"></i> Details';
                    }
                });
            });

            // Also make the entire header clickable
            document.querySelectorAll('.model-header').forEach(header => {
                header.addEventListener('click', function() {
                    const button = this.querySelector('.toggle-details');
                    if (button) {
                        button.click();
                    }
                });
            });

            function loadModelDetails(model, detailsDiv) {
                const loadingSpinner = detailsDiv.querySelector('.loading-spinner');
                const detailsContent = detailsDiv.querySelector('.details-content');
                
                loadingSpinner.style.display = 'block';
                detailsContent.innerHTML = '';
                
                fetch(`{{ route('production.model-details') }}?model=${encodeURIComponent(model)}`)
                    .then(response => response.json())
                    .then(data => {
                        loadingSpinner.style.display = 'none';
                        
                        if (data.orders && data.orders.length > 0) {
                            let tableHtml = `
                                <div class="table-responsive">
                                    <table class="table table-sm details-table">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Gold Color</th>
                                                <th>Quantity</th>
                                                <th>Not Finished</th>
                                                <th>Progress</th>
                                                <th>Order Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                            `;
                            
                            data.orders.forEach(order => {
                                const progressClass = order.progress_percentage >= 100 ? 'bg-success' : 
                                                    order.progress_percentage >= 50 ? 'bg-warning' : 'bg-danger';
                                
                                const colorClass = order.gold_color === 'Yellow' ? 'bg-warning text-dark' :
                                                 order.gold_color === 'White' ? 'bg-light text-dark' :
                                                 order.gold_color === 'Rose' ? 'bg-danger text-white' : 'bg-secondary text-white';
                                
                                tableHtml += `
                                    <tr>
                                        <td><span class="fw-bold text-primary">#${order.id}</span></td>
                                        <td><span class="badge ${colorClass}">${order.gold_color}</span></td>
                                        <td><span class="badge bg-info">${order.quantity}</span></td>
                                        <td><span class="badge bg-warning">${order.not_finished}</span></td>
                                        <td>
                                            <div class="progress" style="height: 15px; min-width: 100px;">
                                                <div class="progress-bar ${progressClass}" 
                                                     style="width: ${order.progress_percentage}%">
                                                    ${order.progress_percentage}%
                                                </div>
                                            </div>
                                        </td>
                                        <td><small class="text-muted">${order.order_date}</small></td>
                                        <td>
                                            <a href="/admin/production/${order.id}/edit" class="btn btn-xs btn-outline-primary me-1">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                `;
                            });
                            
                            tableHtml += `
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <div class="alert alert-info alert-sm">
                                            <div class="row text-center">
                                                <div class="col-md-3">
                                                    <strong>${data.total_orders}</strong><br>
                                                    <small>Total Orders</small>
                                                </div>
                                                <div class="col-md-3">
                                                    <strong>${data.total_quantity}</strong><br>
                                                    <small>Total Quantity</small>
                                                </div>
                                                <div class="col-md-3">
                                                    <strong>${data.total_not_finished}</strong><br>
                                                    <small>Pending</small>
                                                </div>
                                                <div class="col-md-3">
                                                    <strong>${data.total_completed}</strong><br>
                                                    <small>Completed</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                            
                            detailsContent.innerHTML = tableHtml;
                        } else {
                            detailsContent.innerHTML = '<p class="text-muted text-center">No orders found for this model.</p>';
                        }
                        
                        detailsDiv.dataset.loaded = 'true';
                    })
                    .catch(error => {
                        loadingSpinner.style.display = 'none';
                        detailsContent.innerHTML = '<p class="text-danger text-center">Error loading details. Please try again.</p>';
                        console.error('Error:', error);
                    });
            }
        });
    </script>
</body>
</html>