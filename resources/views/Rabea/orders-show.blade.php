<!DOCTYPE html>
<html lang="en">

<head>
    @include('components.navbar')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/order-details.css') }}" rel="stylesheet">
    <!-- Add Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Add Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        /* Container for order details and items, displayed side by side */
        .order-wrapper {
            display: flex;
            gap: 20px;
            justify-content: space-between;
            align-items: flex-start;
        }

        /* Styling for the order details */
        .order-details,
        .order-items {
            flex: 1;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        /* Order items table styling */
        .order-items-table {
            width: 100%;
            border-collapse: collapse;
        }

        .order-items-table th,
        .order-items-table td {
            padding: 8px 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .order-items-table th {
            background-color: #f1f1f1;
        }

        .item-image {
            max-width: 80px;
            height: auto;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container-fluid py-4">
        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @elseif (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Main Content -->
        <div class="card shadow-lg border-0 rounded-3">
            <!-- Order Header -->
            <div class="card-header bg-primary bg-gradient text-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-shopping-cart me-2"></i>
                        Order #{{ $order->shop->name }} - {{ $order->order_number }}
                    </h4>
                    <span class="badge bg-{{ $order->status === 'خلص' ? 'success' : 'warning' }} fs-6">
                        {{ $order->status }}
                    </span>
                </div>
            </div>

            <div class="card-body p-4">
                <div class="row g-4">
                    <!-- Order Information -->
                    <div class="col-lg-5">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Order Information
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <strong><i class="fas fa-shopping-cart me-2"></i>Order Number:</strong>
                                        <span>{{ $order->order_number }}</span>
                                    </div>
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <strong><i class="fas fa-store me-2"></i>Shop:</strong>
                                        <span>{{ $order->shop->name }}</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <strong><i class="fas fa-user me-2"></i>Customer:</strong>
                                        <span>{{ $order->customer_name }}</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <strong><i class="fas fa-phone me-2"></i>Phone:</strong>
                                        <span>{{ $order->customer_phone }}</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <strong><i class="fas fa-user-tie me-2"></i>Seller:</strong>
                                        <span>{{ $order->seller_name }}</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <strong><i class="fas fa-calendar me-2"></i>Order Date:</strong>
                                        <span>{{ $order->order_date }}</span>
                                    </div>
                                    {{-- <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <strong><i class="fas fa-truck me-2"></i>Deliver Date:</strong>
                                        <span>{{ $order->deliver_date }}</span>
                                    </div> --}}
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <strong><i class="fas fa-credit-card me-2"></i>Payment Method:</strong>
                                        <span>{{ $order->payment_method }}</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <strong><i class="fas fa-money-bill me-2"></i>Deposit:</strong>
                                        <span class="text-success">{{ $order->deposit }}</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <strong><i class="fas fa-money-bill-wave me-2"></i>Rest of Cost:</strong>
                                        <span class="text-danger">{{ $order->rest_of_cost }}</span>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <strong><i class="fas fa-notes-medical me-2"></i>Order Details:</strong>
                                    <p class="mt-2 mb-0">{{ $order->order_details }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="col-lg-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">
                                    <i class="fas fa-list me-2"></i>
                                    Order Items
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped">
                                        <thead class="table-primary">
                                            <tr>
                                                <th scope="col">Item Kind</th>
                                                <th scope="col">Weight</th>
                                                <th scope="col">Gold Color</th>
                                                <th scope="col">Fix Type</th>
                                                <th scope="col">Order Type</th>
                                                <th>Item Image</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($order->items as $item)
                                                <tr>
                                                    <td>{{ $item->order_kind }}</td>
                                                    <td>{{ $item->weight }}</td>
                                                    <td>{{ $item->gold_color }}</td>
                                                    <td>{{ $item->order_fix_type }}</td>
                                                    <td>
                                                        @if($item->order_type === 'by_customer')
                                                            طلب العميل
                                                        @elseif($item->order_type === 'by_shop')
                                                            طلب المحل
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($item->image_link)
                                                            <img src="{{ asset('storage/' . $item->image_link) }}" alt="Order Item Image">
                                                        @else
                                                            <p>No image available for this item.</p>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card-footer bg-light py-3">
                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ route('orders.rabea.edit', $order->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit Order
                    </a>
                    <a href="{{ route('orders.rabea.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Orders
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
