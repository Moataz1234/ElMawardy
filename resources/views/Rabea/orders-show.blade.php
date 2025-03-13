<!DOCTYPE html>
<html lang="en" dir="rtl">

<head>
    @include('components.navbar')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفاصيل الطلب</title>
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/order-details.css') }}" rel="stylesheet">
    <!-- Add Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Add Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        .order-container {
            display: flex;
            gap: 30px;
            margin: 20px;
        }

        .order-info {
            flex: 0 0 30%;
            position: sticky;
            top: 20px;
            height: fit-content;
        }

        .order-items {
            flex: 0 0 70%;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .item-card {
            background: #838080; /* Light red background */
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            padding: 15px;
            transition: transform 0.2s ease;
            /* Reduce the height by making content more compact */
            max-height: 300px; /* Adjust this value as needed */
            overflow-y: auto;
        }

        .item-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
        }

        .item-header {
            background: rgba(255, 255, 255, 0.9);
            padding: 8px 12px; /* Reduced padding */
            border-radius: 8px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .item-number {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c3e50;
        }

        .order-type-badge {
            font-size: 0.9rem;
            padding: 6px 12px;
            border-radius: 15px;
            font-weight: 600;
        }

        .by-customer {
            background-color: #3498db;
            color: white;
        }

        .by-shop {
            background-color: #2ecc71;
            color: white;
        }

        .item-details {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 10px 0;
        }

        .detail-row {
            flex: 1 1 calc(50% - 10px);
            min-width: 200px;
            background: rgba(255, 255, 255, 0.9);
            padding: 8px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .detail-row i {
            color: #721c24;
            width: 20px;
        }

        .item-description {
            background: rgba(255, 255, 255, 0.9);
            padding: 8px 12px;
            border-radius: 8px;
            margin-top: 10px;
        }

        .description-header {
            font-weight: 600;
            margin-bottom: 8px;
            color: #721c24;
        }

        .item-image {
            max-width: 100px; /* Reduced image size */
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            margin-top: 10px;
        }

        .info-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            padding: 20px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 1rem;
            margin-top: 10px;
        }

        .status-pending {
            background-color: #ffc107;
            color: #000;
        }

        .status-completed {
            background-color: #28a745;
            color: #fff;
        }

        .status-container {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .status-label {
            font-size: 1.1rem;
            font-weight: 600;
            margin-right: 10px;
        }

        .action-buttons {
            margin-top: 20px;
        }

        .action-buttons .btn {
            padding: 10px 20px;
            font-weight: 500;
            border-radius: 8px;
        }

        /* Add scrollbar styling for better appearance */
        .item-card::-webkit-scrollbar {
            width: 6px;
        }

        .item-card::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.4);
            border-radius: 3px;
        }

        .item-card::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 3px;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container-fluid">
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

        <div class="order-container">
            <!-- Order Information -->
            <div class="order-info">
                <div class="info-card">
                    <div class="status-container">
                        <span class="status-label">حالة الطلب:</span>
                        <span class="status-badge {{ $order->status === 'خلص' ? 'status-completed' : 'status-pending' }}">
                            {{ $order->status }}
                        </span>
                    </div>
                    
                    <h4 class="mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        معلومات الطلب
                    </h4>

                    <div class="info-row">
                        <strong><i class="fas fa-shopping-cart me-2"></i>رقم الطلب:</strong>
                        <span>{{ $order->shop->name }} - {{ $order->order_number }}</span>
                    </div>

                    <div class="info-row">
                        <strong><i class="fas fa-user me-2"></i>اسم العميل:</strong>
                        <span>{{ $order->customer_name }}</span>
                    </div>

                    <div class="info-row">
                        <strong><i class="fas fa-phone me-2"></i>رقم الهاتف:</strong>
                        <span>{{ $order->customer_phone }}</span>
                    </div>

                    <div class="info-row">
                        <strong><i class="fas fa-user-tie me-2"></i>البائع:</strong>
                        <span>{{ $order->seller_name }}</span>
                    </div>

                    <div class="info-row">
                        <strong><i class="fas fa-calendar me-2"></i>تاريخ الطلب:</strong>
                        <span>{{ $order->order_date }}</span>
                    </div>

                    <div class="info-row">
                        <strong><i class="fas fa-credit-card me-2"></i>طريقة الدفع:</strong>
                        <span>{{ $order->payment_method }}</span>
                    </div>

                    <div class="info-row">
                        <strong><i class="fas fa-money-bill me-2"></i>المدفوع:</strong>
                        <span class="text-success">{{ $order->deposit }}</span>
                    </div>

                    <div class="info-row">
                        <strong><i class="fas fa-money-bill-wave me-2"></i>المتبقي:</strong>
                        <span class="text-danger">{{ $order->rest_of_cost }}</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <div class="d-grid gap-2">
                        <a href="{{ route('orders.rabea.edit', $order->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>تعديل الطلب
                        </a>
                        <a href="{{ route('orders.rabea.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>عودة
                        </a>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="order-items">
                <h4 class="mb-4 grid-column-full"><i class="fas fa-list me-2"></i>تفاصيل القطع</h4>
                @foreach ($order->items as $index => $item)
                    <div class="item-card">
                        <div class="item-header">
                            <div class="item-number">القطعة {{ $index + 1 }}</div>
                            <span class="order-type-badge {{ $item->order_type === 'by_customer' ? 'by-customer' : 'by-shop' }}">
                                {{ $item->order_type === 'by_customer' ? 'طلب العميل' : 'طلب المحل' }}
                            </span>
                        </div>

                        <div class="item-details">
                            <div class="detail-row">
                                <i class="fas fa-tag"></i>
                                <div>
                                    <strong>النوع:</strong>
                                    <span>{{ $item->item_type }}</span>
                                </div>
                            </div>

                            <div class="detail-row">
                                <i class="fas fa-box"></i>
                                <div>
                                    <strong>الصنف:</strong>
                                    <span>{{ $item->order_kind }}</span>
                                </div>
                            </div>

                            @if($item->weight)
                            <div class="detail-row">
                                <i class="fas fa-weight-hanging"></i>
                                <div>
                                    <strong>الوزن:</strong>
                                    <span>{{ $item->weight }}</span>
                                </div>
                            </div>
                            @endif

                            @if($item->ring_size)
                            <div class="detail-row">
                                <i class="fas fa-ring"></i>
                                <div>
                                    <strong>مقاس الخاتم:</strong>
                                    <span>{{ $item->ring_size }}</span>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="item-description">
                            <div class="description-header">
                                <i class="fas fa-align-left"></i>
                                <span>موضوع الطلب</span>
                            </div>
                            <p class="description-text">{{ $item->order_details }}</p>
                        </div>

                        @if ($item->image_link)
                            <div class="text-center">
                                <img src="{{ asset('storage/' . $item->image_link) }}" 
                                     alt="صورة القطعة" 
                                     class="item-image">
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
