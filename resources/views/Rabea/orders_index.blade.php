<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Orders</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/pagination.css') }}" rel="stylesheet">
    {{-- <link href="{{ asset('css/style.css') }}" rel="stylesheet"> --}}
    <link href="{{ asset('css/order-details.css') }}" rel="stylesheet">
    <link href="{{ asset('css/first_page.css') }}" rel="stylesheet">
    <link href="{{ asset('css/order_index.css') }}" rel="stylesheet">
    @include('admin.Rabea.dashboard')

    <title>Document</title>
</head>

<body>
    @php
    $pendingOrdersCount = \App\Models\Order::where('status', 'pending')->count();
    @endphp
            <h1>Customer Orders</h1>

    <nav class="second-section">
        <div class="search-container">
            <!-- Dropdown for Model Name -->
            <select class="model-dropdown">
                <option value="">Select Search Type</option>
                <option value="model1">Model 1</option>
                <option value="model2">Model 2</option>
                <option value="model3">Model 3</option>
            </select>
            <!-- Search Input -->
            <input type="text" class="search-input" placeholder="Model Name">
        </div>
        <div class="notification-icon">
            <a href="{{ route('orders.requests') }}" class="nav-link {{ $pendingOrdersCount > 0 ? 'light-up' : '' }}">
            <i class="fas fa-bell" id="notification"></i>
            @if ($pendingOrdersCount > 0)
            <span class="badge">{{ $pendingOrdersCount }}</span>
        @endif
            </a>
        </div>
    </nav>

    {{-- <div class="page-2">
        <div class="container">
            <div class="filter-search">

                <form>
                    <div class="radio-group">
                        <h3>Sort By</h3>
                        <div class="page-2">
                            <label>
                                <input type="radio" name="sort" value="Serial Number">
                                <span class="custom-radio"></span> رقم الفرع
                            </label>
                            <label>
                                <input type="radio" name="sort" value="Shop Name">
                                <span class="custom-radio"></span> رقم الأوردر
                            </label>
                            <label>
                                <input type="radio" name="sort" value="Model">
                                <span class="custom-radio"></span> اسم العميل
                            </label>
                            <label>
                                <input type="radio" name="sort" value="Quantity">
                                <span class="custom-radio"></span> اسم البائع
                            </label>
                        </div>
                    </div>
                    <div class="horizontal-line"></div>
                    <div class="button-container">
                        <button type="button" class="reset-button">Set</button>
                    </div>
                </form>
            </div> --}}

            <div class="spreadsheet">
                <table>
                    <thead>
                        <tr>
                            <th></th>
                            <th>رقم الفرع</th>
                            <th>رقم الأوردر</th>
                            <th>اسم العميل</th>
                            <th>رقم العميل</th>
                            <th>اسم البائع</th>
                            <th>موضوع الطلب</th>
                            <th>تاريخ الاستلام</th>
                            <th>حالة الطلب</th>
                            <th>عرض الطلب</th>
                                
                        </tr>
                    </thead>
                    <tbody id="table-body">
                        @foreach ($orders as $order)
                        <tr data-order-id="{{ $order->id }}">
                            <td><input type="checkbox"></td>
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
                                <div class="status-box status-cell" 
                                     data-status="{{ $order->status }}"
                                     style="background-color: {{ 
                                        $order->status == 'خلص' ? 'rgb(179, 5, 121)' : 
                                        ($order->status == 'في الدمغة' ? 'rgba(64, 152, 199, 0.862)' : 
                                        ($order->status == 'في الورشة' ? 'rgb(200, 151, 5)' : 
                                        ($order->status == 'تم الإستلام' ? 'rgba(104, 180, 22, 0.971)' : ''))) 
                                     }}">
                                    {{ $order->status  }}
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('orders.show', $order->id) }}" class="action_button">View</a>
                                </div>
                            </td>
                                
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Keep your existing bottom buttons div -->
<div class="order-status">
    <div style="text-align: center;">
        <!-- Add a hidden form for bulk updates -->
        <form action="{{ route('orders.updateStatus.bulk') }}" method="POST" id="bulk-status-form" style="display: none;">
            @csrf
            <input type="hidden" name="status" id="bulk-status-input">
            <input type="hidden" name="order_ids" id="selected-orders-input">
        </form>
        
        <button class="status-button status-خلص" onclick="changeBulkStatus('خلص')">خلص</button>
        <button class="status-button status-في-الدمغة" onclick="changeBulkStatus('في الدمغة')">في الدمغة</button>
        <button class="status-button status-في-الورشة" onclick="changeBulkStatus('في الورشة')">في الورشة</button>
        {{-- <button class="status-button status-تم-الإستلام" onclick="changeBulkStatus('تم الإستلام')">تم الإستلام</button> --}}
    </div>
</div>

    <script src="Scripts/Scripts.js"></script>
    <script>
       function changeBulkStatus(status) {
    const checkboxes = document.querySelectorAll('input[type="checkbox"]:checked');
    if (checkboxes.length === 0) {
        alert('Please select at least one order to change its status.');
        return;
    }

    // Collect all selected order IDs
    const orderIds = Array.from(checkboxes).map(checkbox => {
        return checkbox.closest('tr').dataset.orderId;
    });

    // Update the hidden form inputs
    document.getElementById('bulk-status-input').value = status;
    document.getElementById('selected-orders-input').value = JSON.stringify(orderIds);

                // Change background color of the status cell based on selected status
                
    // Submit the form
    document.getElementById('bulk-status-form').submit();

}    </script>
</body>

</html>