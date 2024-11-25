<!DOCTYPE html>
<html lang="en">
<head>
    @include('rabea.dashboard')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Orders</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/pagination.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/order-details.css') }}" rel="stylesheet">
    <style>
        /* Custom CSS for the buttons and table layout */
        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .action-buttons button, .action-buttons a {
            width: 100%;
            padding: 5px 10px;
            text-align: center;
            border-radius: 5px;
            border: 1px solid #ddd;
            transition: background-color 0.3s ease;
            cursor: pointer;
        }
       
        .nav-item {
            display: inline-block;
            margin-right: 15px;
        }

        .nav-link {
            text-decoration: none;
            padding: 10px 15px;
            border: 1px solid #007bff;
            border-radius: 4px;
            background-color: #f8f9fa;
            color: #333;
            transition: background-color 0.3s;
        }

        .nav-link:hover {
            background-color: #e2e6ea;
        }

        .badge {
            background-color: red;
            color: white;
            border-radius: 10px;
            padding: 2px 8px;
        }

        .light-up {
            animation: pulse 1s infinite;
            color: red;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        .action_button { background-color: #007bff; color: white; }
        .action_button:hover { background-color: #0056b3; }
        @media print {
            body * {
                visibility: hidden; /* Hide everything */
            }
            .printable, .printable * {
                visibility: visible; /* Show only the date and table */
            }
            .printable {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>
<div class="container">

    <!-- Display success or error messages -->
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif (session('error'))
        <div class="light-up">{{ session('error') }}</div>
    @endif

    @php
        $pendingOrdersCount = \App\Models\Order::where('status', 'pending')->count();
    @endphp

    <div class="nav-item">
        <a href="{{ route('orders.requests') }}" class="nav-link {{ $pendingOrdersCount > 0 ? 'light-up' : '' }}">
            Order Requests 
            @if ($pendingOrdersCount > 0)
                <span class="badge">{{ $pendingOrdersCount }}</span>
            @endif
        </a>
    </div>
    <div class="printable">
        <p style="margin: 20px 500px"><strong>Date: </strong>{{ \Carbon\Carbon::now()->format('Y-m-d') }}</p>
    </div>

    <!-- Orders Table -->
    <div class="printable">
    <table class="table">
        <thead>
            <tr>
                <th>موضوع الطلب</th>
                <th>رقم الاوردر</th>
                <th>اسم البائع</th>
                <th>رقم المحل</th>
                <th></th>

            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
                <tr>
                    <td class="order_details">{{ $order->order_details }}</td>                    
                    <td>{{ $order->shop_id }}-{{ $order->order_number }}</td>
                    <td>{{ $order->seller_name }}</td>
                    <td>{{ $order->shop_id }}</td>
                </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
    <button onclick="window.print()">Print this page</button>

    {{ $orders->appends(request()->query())->links() }} <!-- Pagination with search and sort parameters -->
</div>
</body>
</html>
