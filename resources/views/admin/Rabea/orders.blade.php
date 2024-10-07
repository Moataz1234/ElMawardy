<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.rabea.dashboard')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Orders</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/pagination.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/order-details.css') }}" rel="stylesheet">

    <style>
        @media print {
            /* Hide everything except the table */
            body * {
                visibility: hidden;
            }

            /* Only show the table */
            .table, .table * {
                visibility: visible;
            }

            /* Ensure the table takes up the full width */
            .table {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
            }
        }
   /* custom.css */
.nav-item {
    display: inline-block; /* Ensure nav items are inline */
    margin-right: 15px; /* Space between items */
}

.nav-link {
    text-decoration: none; /* Remove underline from links */
    padding: 10px 15px; /* Add padding */
    border: 1px solid #007bff; /* Border color */
    border-radius: 4px; /* Rounded corners */
    background-color: #f8f9fa; /* Light background */
    color: #333; /* Text color */
    transition: background-color 0.3s; /* Smooth transition */
}

.nav-link:hover {
    background-color: #e2e6ea; /* Darker background on hover */
}

.badge {
    background-color: red; /* Badge background color */
    color: white; /* Badge text color */
    border-radius: 10px; /* Rounded badge */
    padding: 2px 8px; /* Padding for badge */
}

.light-up {
    animation: pulse 1s infinite; /* Animation for light-up effect */
    color: red; /* Change text color when lit */
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}
    /* Align the buttons in the Actions column neatly */
    .action-buttons {
        display: flex;
        flex-direction: column; /* Stack buttons vertically */
        gap: 5px; /* Add space between buttons */
    }

    .action-buttons button,
    .action-buttons a {
        width: 100%; /* Make buttons full width */
        padding: 5px 10px;
        text-align: center;
        border-radius: 5px;
        border: 1px solid #ddd; /* Optional: border for buttons */
        transition: background-color 0.3s ease;
        cursor: pointer;
    }

    /* Button color and hover effects */
    .info_button2 {
        background-color: #17a2b8; /* Light blue color */
        color: white;
    }

    .info_button2:hover {
        background-color: #138496;
    }

    .info_button {
        background-color: #ffc107; /* Yellow color */
        color: white;
    }

    .info_button:hover {
        background-color: #e0a800;
    }

    .success_button {
        background-color: #28a745; /* Green color */
        color: white;
    }

    .success_button:hover {
        background-color: #218838;
    }

    .action_button {
        background-color: #007bff; /* Blue color */
        color: white;
    }

    .action_button:hover {
        background-color: #0056b3;
    }

    </style>
</head>
<body>
<div class="container">
    <h2>Customer Orders</h2>

    <!-- Display success or error messages -->
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif (session('error'))
        <div class="light-up">{{ session('error') }}</div>
    @endif
   
   
   @php
       $pendingOrdersCount = \App\Models\Order::where('status', 'pending')->count(); // Count all pending orders
@endphp

<div class="nav-item">
    <a href="{{ route('orders.requests') }}" class="nav-link {{ $pendingOrdersCount > 0 ? 'light-up' : '' }}">
        Order Requests 
        @if ($pendingOrdersCount > 0)
            <span class="badge">{{ $pendingOrdersCount }}</span>
        @endif
    </a>
</div>
    <!-- Orders Table -->
    <table class="table">
        <thead>
            <tr>
                <th>Image</th>
                @php
                    // Array of columns with their display names
                    $columns = [
                        'shop_name' => ' فرع',
                        'order_number' => ' رقم الطلب',
                        'order_kind' => 'النوع',
                        'order_details' => 'موضوع الطلب',
                        // 'ring_size' => 'مقاس الخاتم',
                        'weight' => 'الوزن',
                        'gold_color' => 'اللون',
                        'order_fix_type' => 'المشكلة',
                        'customer_name' => 'اسم العميل ',
                        'customer_phone' => ' تليفون العميل',
                        'seller_name' => 'البائع',
                        'deposit' => 'المدفوع',
                        'rest_of_cost' => 'الباقي',
                        'order_date' => 'تاريخ الاستلام',
                        'deliver_date' => 'تاريخ التسليم',
                        '' => 'طريقة الدفع',

                        'status' => 'Status',
                    ];
                @endphp

                @foreach ($columns as $field => $label)
                    <th>
                        <div class="sort-container">
                            {{ $label }}
                            <form method="GET" action="{{ route('orders.rabea.index') }}" style="display:inline;">
                                <input type="hidden" name="sort" value="{{ $field }}">
                                <input type="hidden" name="direction" value="{{ request('direction') === 'asc' ? 'desc' : 'asc' }}">
                                <input type="hidden" name="search" value="{{ request('search') }}">
                                <button type="submit">&#8597;</button>
                            </form>
                        </div>
                    </th>
                @endforeach
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
                <tr>
                    <td><img src="{{ asset('storage/' . $order->image_link) }}" alt="Order Image" style="max-width: 100%; height: auto;">
                    </td>
                    <td>{{ $order->shop->name }}</td>
                    <td>{{ $order->order_number }}</td>
                    <td>{{ $order->order_kind }}</td>
                    <td>{{ $order->order_details }}</td>
                    {{-- <td>{{ $order->ring_size }}</td> --}}
                    <td>{{ $order->weight }}</td>
                    <td>{{ $order->gold_color }}</td>
                    <td>{{ $order->order_fix_type }}</td>
                    <td>{{ $order->customer_name }}</td>
                    <td>{{ $order->customer_phone }}</td>
                    <td>{{ $order->seller_name }}</td>
                    <td>{{ $order->deposit }}</td>
                    <td>{{ $order->rest_of_cost }}</td>
                    <td>{{ $order->order_date }}</td>
                    <td>{{ $order->deliver_date }}</td>
                    <td>{{ $order->payment_method }}</td>
                    <td>{{ $order->status }}</td>
                    <td >
                        <div class="action-buttons">
                        <a href="{{ route('orders.show', $order->id) }}" class="action_button">View</a>
                        
                        <!-- Button to change status to "في الورشة" -->
                        <form action="{{ route('orders.updateStatus', ['id' => $order->id, 'status' => 'في الورشة']) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="info_button2">في الورشة</button>
                        </form>
                        
                        <!-- Button to change status to "في الدمغة" -->
                        <form action="{{ route('orders.updateStatus', ['id' => $order->id, 'status' => 'في الدمغة']) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="info_button">في الدمغة</button>
                        </form>
                    
                        <!-- Button to change status to "خلص" -->
                        <form action="{{ route('orders.updateStatus', ['id' => $order->id, 'status' => 'خلص']) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="success_button">خلص</button>
                        </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <button onclick="window.print()">Print this page</button>
    
    {{ $orders->appends(request()->query())->links() }} <!-- Pagination with search and sort parameters -->
</div>
</body>
</html>
