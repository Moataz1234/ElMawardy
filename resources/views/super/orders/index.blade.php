<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Management - Super User</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- BoxIcons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .order-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
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
        .badge-count {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            margin-left: 0.5rem;
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
                <h2 class="fw-bold text-dark">Orders Management</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('super.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item active">Orders</li>
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

        <!-- Tabs Navigation -->
        <div class="row">
            <div class="col-12">
                <div class="card order-card">
                    <div class="card-header bg-white">
                        <ul class="nav nav-tabs card-header-tabs" id="orderTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="all-orders-tab" data-bs-toggle="tab" data-bs-target="#all-orders" type="button" role="tab">
                                    <i class="bx bx-list-ul me-2"></i>All Orders
                                    <span class="badge bg-primary badge-count">{{ $orders->total() }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending-orders" type="button" role="tab">
                                    <i class="bx bx-time me-2"></i>Pending
                                    <span class="badge bg-warning badge-count text-dark">{{ $pendingOrdersCount }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="progress-tab" data-bs-toggle="tab" data-bs-target="#progress-orders" type="button" role="tab">
                                    <i class="bx bx-loader me-2"></i>In Progress
                                    <span class="badge bg-info badge-count">{{ $inProgressOrdersCount }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed-orders" type="button" role="tab">
                                    <i class="bx bx-check-circle me-2"></i>Completed
                                    <span class="badge bg-success badge-count">{{ $completedOrdersCount }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="order-items-tab" data-bs-toggle="tab" data-bs-target="#order-items" type="button" role="tab">
                                    <i class="bx bx-package me-2"></i>Order Items
                                    <span class="badge bg-secondary badge-count">{{ $orders->sum(function($order) { return $order->items->count(); }) }}</span>
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="orderTabsContent">
                            <!-- All Orders Tab -->
                            <div class="tab-pane fade show active" id="all-orders" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Order ID</th>
                                                <th>Order Number</th>
                                                <th>Customer</th>
                                                <th>Shop</th>
                                                <th>Items Count</th>
                                                <th>Deposit</th>
                                                <th>Rest Cost</th>
                                                <th>Status</th>
                                                <th>Order Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($orders as $order)
                                            <tr>
                                                <td><strong>#{{ $order->id }}</strong></td>
                                                <td>{{ $order->order_number }}</td>
                                                <td>
                                                    <div>
                                                        <strong>{{ $order->customer_name }}</strong><br>
                                                        @if($order->customer_phone)
                                                            <small class="text-muted">{{ $order->customer_phone }}</small>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($order->shop)
                                                        <span class="badge bg-light text-dark">{{ $order->shop->name }}</span>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ $order->items->count() }}</span>
                                                </td>
                                                <td>
                                                    @if($order->deposit)
                                                        <strong class="text-success">${{ number_format($order->deposit, 2) }}</strong>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($order->rest_of_cost)
                                                        <strong class="text-warning">${{ number_format($order->rest_of_cost, 2) }}</strong>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge {{ 
                                                        $order->status == 'pending' ? 'bg-warning text-dark' : 
                                                        ($order->status == 'in_progress' ? 'bg-info' : 
                                                        ($order->status == 'completed' ? 'bg-success' : 'bg-danger'))
                                                    }}">
                                                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($order->order_date)
                                                        {{ \Carbon\Carbon::parse($order->order_date)->format('M d, Y') }}
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('super.orders.show', $order->id) }}" 
                                                           class="btn btn-sm btn-outline-info" 
                                                           title="View Order">
                                                            <i class="bx bx-show"></i>
                                                        </a>
                                                        <a href="{{ route('super.orders.edit', $order->id) }}" 
                                                           class="btn btn-sm btn-outline-primary ms-1" 
                                                           title="Edit Order">
                                                            <i class="bx bx-edit"></i>
                                                        </a>
                                                        <form method="POST" 
                                                              action="{{ route('super.orders.delete', $order->id) }}" 
                                                              style="display: inline;" 
                                                              onsubmit="return confirm('Are you sure you want to delete this order?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" 
                                                                    class="btn btn-sm btn-outline-danger ms-1" 
                                                                    title="Delete Order">
                                                                <i class="bx bx-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="d-flex justify-content-center">
                                        <nav aria-label="Orders pagination">
                                            {{ $orders->appends(request()->query())->links('vendor.pagination.custom-super') }}
                                        </nav>
                                    </div>
                                </div>
                            </div>

                            <!-- Pending Orders Tab -->
                            <div class="tab-pane fade" id="pending-orders" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Order ID</th>
                                                <th>Order Number</th>
                                                <th>Customer</th>
                                                <th>Shop</th>
                                                <th>Deposit</th>
                                                <th>Order Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($pendingOrders as $order)
                                            <tr>
                                                <td><strong>#{{ $order->id }}</strong></td>
                                                <td>{{ $order->order_number }}</td>
                                                <td>{{ $order->customer_name }}</td>
                                                <td>
                                                    @if($order->shop)
                                                        {{ $order->shop->name }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($order->deposit)
                                                        ${{ number_format($order->deposit, 2) }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($order->order_date)
                                                        {{ \Carbon\Carbon::parse($order->order_date)->format('M d, Y') }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('super.orders.show', $order->id) }}" class="btn btn-sm btn-outline-info">
                                                            <i class="bx bx-show"></i>
                                                        </a>
                                                        <a href="{{ route('super.orders.edit', $order->id) }}" class="btn btn-sm btn-outline-primary ms-1">
                                                            <i class="bx bx-edit"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- In Progress Orders Tab -->
                            <div class="tab-pane fade" id="progress-orders" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Order ID</th>
                                                <th>Order Number</th>
                                                <th>Customer</th>
                                                <th>Shop</th>
                                                <th>Deposit</th>
                                                <th>Deliver Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($inProgressOrders as $order)
                                            <tr>
                                                <td><strong>#{{ $order->id }}</strong></td>
                                                <td>{{ $order->order_number }}</td>
                                                <td>{{ $order->customer_name }}</td>
                                                <td>
                                                    @if($order->shop)
                                                        {{ $order->shop->name }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($order->deposit)
                                                        ${{ number_format($order->deposit, 2) }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($order->deliver_date)
                                                        {{ \Carbon\Carbon::parse($order->deliver_date)->format('M d, Y') }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('super.orders.show', $order->id) }}" class="btn btn-sm btn-outline-info">
                                                            <i class="bx bx-show"></i>
                                                        </a>
                                                        <a href="{{ route('super.orders.edit', $order->id) }}" class="btn btn-sm btn-outline-primary ms-1">
                                                            <i class="bx bx-edit"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Completed Orders Tab -->
                            <div class="tab-pane fade" id="completed-orders" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Order ID</th>
                                                <th>Order Number</th>
                                                <th>Customer</th>
                                                <th>Shop</th>
                                                <th>Total Amount</th>
                                                <th>Payment Method</th>
                                                <th>Completed Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($completedOrders as $order)
                                            <tr>
                                                <td><strong>#{{ $order->id }}</strong></td>
                                                <td>{{ $order->order_number }}</td>
                                                <td>{{ $order->customer_name }}</td>
                                                <td>
                                                    @if($order->shop)
                                                        {{ $order->shop->name }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($order->deposit && $order->rest_of_cost)
                                                        <strong class="text-success">${{ number_format($order->deposit + $order->rest_of_cost, 2) }}</strong>
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($order->payment_method)
                                                        <span class="badge bg-success">{{ $order->payment_method }}</span>
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($order->deliver_date)
                                                        {{ \Carbon\Carbon::parse($order->deliver_date)->format('M d, Y') }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('super.orders.show', $order->id) }}" class="btn btn-sm btn-outline-info">
                                                        <i class="bx bx-show"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Order Items Tab -->
                            <div class="tab-pane fade" id="order-items" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Item ID</th>
                                                <th>Order</th>
                                                <th>Kind</th>
                                                <th>Type</th>
                                                <th>Model</th>
                                                <th>Serial Number</th>
                                                <th>Weight</th>
                                                <th>Cost</th>
                                                <th>Details</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($orders as $order)
                                                @foreach($order->items as $item)
                                                <tr>
                                                    <td><strong>#{{ $item->id }}</strong></td>
                                                    <td>
                                                        <div>
                                                            <strong>{{ $order->order_number }}</strong><br>
                                                            <small class="text-muted">{{ $order->customer_name }}</small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if($item->order_kind)
                                                            <span class="badge bg-primary">{{ $item->order_kind }}</span>
                                                        @else
                                                            <span class="text-muted">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($item->item_type)
                                                            <span class="badge bg-info">{{ $item->item_type }}</span>
                                                        @else
                                                            <span class="text-muted">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $item->model ?? 'N/A' }}</td>
                                                    <td>{{ $item->serial_number ?? 'N/A' }}</td>
                                                    <td>
                                                        @if($item->weight)
                                                            {{ $item->weight }}g
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($item->cost)
                                                            <strong class="text-success">${{ number_format($item->cost, 2) }}</strong>
                                                        @else
                                                            <span class="text-muted">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($item->order_details)
                                                            <span class="text-muted">{{ Str::limit($item->order_details, 30) }}</span>
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 