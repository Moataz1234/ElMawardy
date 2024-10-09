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

    <!-- Orders Table -->
    <table class="table">
        <thead>
            <tr>
                @php
                // Array of columns with their display names
                $columns = [
                    'shop_id ' =>'رقم الفرع',
                    'order_number' => 'رقم الاوردر',
                    'customer_name' => 'اسم العميل',
                    'customer_phone' => 'رقم العميل',
                    'seller_name' => 'اسم البائع',
                    'order_details' => 'موضوع الطلب',
                    'order_date' => 'تاريخ الاستلام',
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
                {{-- <th>Delivery Date</th> --}}
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
                <tr>
                    <td>{{ $order->shop_id }}</td>
                    <td>{{ $order->order_number }}</td>
                    <td>{{ $order->customer_name }}</td>
                    <td>{{ $order->customer_phone }}</td>
                    <td>{{ $order->seller_name }}</td>
                    <td>{{ $order->order_details }}</td>

                    {{-- <td>{{ $order->deposit }}</td>
                    <td>{{ $order->rest_of_cost }}</td> --}}
                    <td>{{ $order->order_date }}</td>
                    {{-- <td>{{ $order->deliver_date }}</td> --}}
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('orders.show', $order->id) }}" class="action_button">View</a>
                            <form action="{{ route('orders.updateStatus', ['id' => $order->id]) }}" method="POST" id="status-form-{{ $order->id }}">
                                @csrf
                                <select name="status" onchange="document.getElementById('status-form-{{ $order->id }}').submit();">
                                    <option value="تم الاستلام" {{ $order->status == 'تم الاستلام' ? 'selected' : '' }}>تم الاستلام</option>
                                    <option value="في الورشة" {{ $order->status == 'في الورشة' ? 'selected' : '' }}>في الورشة</option>
                                    <option value="في الدمغة" {{ $order->status == 'في الدمغة' ? 'selected' : '' }}>في الدمغة</option>
                                    <option value="خلص" {{ $order->status == 'خلص' ? 'selected' : '' }}>خلص</option>
                                </select>
                            </form>
                        </div>
                    </td>
                        </div>
                    </td>
                </tr>

                {{-- <!-- Order Items Table (Displayed under each order) -->
                <tr>
                    <td colspan="10">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Item Kind</th>
                                    <th>Quantity</th>
                                    <th>Ring Size</th>
                                    <th>Weight</th>
                                    <th>Gold Color</th>
                                    <th>Fix Type</th>
                                    <th>Item Image</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->items as $item)
                                    <tr>
                                        <td>{{ $item->order_kind }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ $item->ring_size }}</td>
                                        <td>{{ $item->weight }}</td>
                                        <td>{{ $item->gold_color }}</td>
                                        <td>{{ $item->order_fix_type }}</td>
                                        <td>
                                            @if ($item->image_link)
                                                <img src="{{ asset('storage/' . $item->image_link) }}" alt="Item Image" style="max-width: 100px; height: auto;">
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr> --}}
            @endforeach
        </tbody>
    </table>

    {{-- <button onclick="window.print()">Print this page</button> --}}

    {{ $orders->appends(request()->query())->links() }} <!-- Pagination with search and sort parameters -->
</div>
</body>
</html>
