<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders: All Locations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>

        /* Add padding to rows and cells */
        th, td {
            padding: 15px;
            vertical-align: middle; /* Center content vertically */
        }

        /* Ensure that text in the table is left aligned */
        th, td {
            text-align: left;
        }

        /* Optional: Ensure image size is appropriate */
        td img {
            max-width: 100px; /* Keep images to a reasonable size */
            height: auto;
        }

        /* Add scrolling to the table body */
        tbody {
            display: block;
            max-height: 500px; /* Set a maximum height for the table body */
            overflow-y: auto; /* Enable vertical scrolling */
        }

        /* Ensure the table header and body align */
        thead, tbody tr {
            display: table;
            width: 100%;
            table-layout: fixed; /* Ensure the table layout is fixed */
        }
    </style>
</head>

<body>
    {{-- class="container" --}}
<div >
    <h2 class="text-center mb-4">Orders: All Locations</h2>
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link {{ $currentTab == 'unfulfilled' ? 'active' : '' }}" 
               href="{{ route('orders_shopify', ['tab' => 'unfulfilled']) }}">
               Unfulfilled Orders
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $currentTab == 'archived' ? 'active' : '' }}" 
               href="{{ route('orders_shopify', ['tab' => 'archived']) }}">
               Past Orders
            </a>
        </li>
    </ul>
    <table class="table table-hover table-striped">
        <thead class="table-dark">
            <tr>
                <th>
                    <a href="{{ route('orders_shopify', ['tab' => $currentTab, 'sort_by_'.$currentTab => 'name', 'sort_direction_'.$currentTab => $sortDirection == 'asc' ? 'desc' : 'asc']) }}" class="text-white">
                        Order #
                        @if ($sortBy == 'name')
                            @if ($sortDirection == 'asc') &#9650; @else &#9660; @endif
                        @endif
                    </a>
                </th>

                <th>
                    <a href="{{ route('orders_shopify', ['tab' => $currentTab, 'sort_by_'.$currentTab => 'created_at', 'sort_direction_'.$currentTab => $sortDirection == 'asc' ? 'desc' : 'asc']) }}" class="text-white">
                        Date
                        @if ($sortBy == 'created_at')
                            @if ($sortDirection == 'asc') &#9650; @else &#9660; @endif
                        @endif
                    </a>
                </th>
                <th>Customer</th>
                
                <th>Order Image</th>
                <th>Order Details</th>
                <th>Contact Info</th> <!-- New column for contact info -->
                <th>Shipping Address</th> <!-- New column for shipping address -->
                <th>Billing Address</th> <!-- New column for shipping address -->
              
                {{-- <th>Channel</th> --}}
                <th>
                    <a href="{{ route('orders_shopify', ['sort_by' => 'total_price', 'sort_direction' => $sortBy == 'total_price' && $sortDirection == 'asc' ? 'desc' : 'asc']) }}" class="text-white">
                        Total
                        @if ($sortBy == 'total_price')
                            @if ($sortDirection == 'asc') &#9650; @else &#9660; @endif
                        @endif
                    </a>
                </th>
                <th>Payment Status</th>
                <th>Fulfillment Status</th>
                {{-- <th>Items</th> --}}
                <th>Action</th> 

            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ $order['name'] }}</td>
                <td>{{ \Carbon\Carbon::parse($order['created_at'])->format('M d, Y h:i A') }}</td>
                <td>{{ $order['customer']['first_name'] ?? 'Guest' }} {{ $order['customer']['last_name'] ?? '' }}</td>
                <td>
                @foreach($order['line_items'] as $item)
                    @if(isset($item['image_url']))
                         <img src="{{ $item['image_url'] }}" alt="Product Image" height="100" style="padding-top:10px">
                    @else
                         No image available
                    @endif
                @endforeach
                </td>
            
                <!-- Order Details Column (related items) -->
                <td>
                    <ul>
                        @foreach($order['line_items'] as $item)
                            <li>{{ $item['name'] }} <br>(Qty: {{ $item['quantity'] }}) <br> SKU: {{ $item['sku'] }}</li>
                        @endforeach
                    </ul>
                </td>
                <td>
                    <div><span>Email:</span> {{ $order['customer']['email'] ?? 'N/A' }}</div>
                    {{-- <div>Phone: {{ $order['customer']['phone'] ?? 'N/A' }}</div> --}}
                    <div><span> Phone Number:</span>{{ $order['shipping_address']['phone'] ?? 'N/A' }}</div>

                </td>

                <!-- Shipping Address -->
                <td style="width: 200px">
                    @if (!empty($order['shipping_address']))
                        <div>{{ $order['shipping_address']['address1'] ?? 'N/A' }}</div>
                        <div>{{ $order['shipping_address']['city'] ?? 'N/A' }}</div>
                        <div>{{ $order['shipping_address']['zip'] ?? 'N/A' }}</div>
                        <div>{{ $order['shipping_address']['country'] ?? 'N/A' }}</div>
                    @else
                        <div>N/A</div>
                    @endif
                </td>
                <td style="width: 200px">
                    @if (!empty($order['billing_address']))
                    <div>{{ $order['customer']['first_name'] ?? 'Guest' }} {{ $order['customer']['last_name'] ?? '' }}</div>
                    <div>{{ $order['billing_address']['address1'] ?? 'N/A' }}</div>
                        <div>{{ $order['billing_address']['city'] ?? 'N/A' }}</div>
                        <div>{{ $order['billing_address']['zip'] ?? 'N/A' }}</div>
                        <div>{{ $order['billing_address']['country'] ?? 'N/A' }}</div>
                    @else
                        <div>N/A</div>
                    @endif
                </td>
                {{-- <td>{{ $order['source_name'] }}</td> --}}
                <td>£{{ number_format($order['total_price'], 2) }}</td>
                <td>
                    @if ($order['financial_status'] == 'paid')
                        <span class="badge bg-success">Paid</span>
                    @elseif ($order['financial_status'] == 'pending')
                        <span class="badge bg-warning">Pending</span>
                    @else
                        <span class="badge bg-danger">{{ ucfirst($order['financial_status']) }}</span>
                    @endif
                </td>
                <td>
                    @if ($order['fulfillment_status'] == 'fulfilled')
                        <span class="badge bg-primary">Fulfilled</span>
                    @else
                        <span class="badge bg-secondary">Unfulfilled</span>
                    @endif
                </td>
                {{-- <td>{{ count($order['line_items']) }} items</td> --}}
                      <!-- Action Column with Mark as Paid and Mark as Fulfilled -->
                      <td>
                        @if ($order['financial_status'] != 'paid')
                            <form method="POST" action="{{ route('order.markPaid', $order['id']) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">Mark as Paid</button>
                            </form>
                        @endif

                        @if ($order['fulfillment_status'] != 'fulfilled')
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#fulfillModal_{{ $order['id'] }}">
                            Fulfill
                        </button>
                
                        <!-- Fulfillment Modal -->
                        <div class="modal fade" id="fulfillModal_{{ $order['id'] }}" tabindex="-1" aria-labelledby="fulfillModalLabel_{{ $order['id'] }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="fulfillModalLabel_{{ $order['id'] }}">Fulfill Order: {{ $order['name'] }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST" action="{{ route('order.fulfill', $order['id']) }}">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="tracking_number" class="form-label">Tracking Number</label>
                                                <input type="text" class="form-control" name="tracking_number" id="tracking_number_{{ $order['id'] }}" required>
                                            </div>
                                            <label for="tracking_url">Tracking URL:</label>
                                            <input type="text" id="tracking_url" name="tracking_url" placeholder="https://www.example.com/track/123456789">
                                        
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Fulfill Order</button>
                                            </div>
                                        </form>
                                        <form action="{{ route('fulfillWithoutShipping') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="order_id" value="{{ $order['id'] }}">
                                            <input type="hidden" name="line_item_id" value="{{ $lineItem->id }}">
                                            <button type="submit" class="btn btn-primary">Fulfill Without Shipping</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <span class="badge bg-primary">Fulfilled</span>
                    @endif
                          </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $orders->links() }}
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
<script>
    // document.addEventListener('DOMContentLoaded', function() {
    //     var fulfillModals = document.querySelectorAll('[id^="fulfillModal-"]');
    //     fulfillModals.forEach(function(modal) {
    //         var orderId = modal.id.split('-')[1];
    //         var trackingInfo = document.getElementById('trackingInfo-' + orderId);
    //         var withTracking = document.getElementById('withTracking-' + orderId);
    //         var withoutTracking = document.getElementById('withoutTracking-' + orderId);
            
    //         withTracking.addEventListener('change', function() {
    //             if (withTracking.checked) {
    //                 trackingInfo.style.display = 'block';
    //             }
    //         });
    //         withoutTracking.addEventListener('change', function() {
    //             if (withoutTracking.checked) {
    //                 trackingInfo.style.display = 'none';
    //             }
    //         });
    //     });
    // });
    let currentOrderId = null;

    function openFulfillmentDialog(orderId) {
        currentOrderId = orderId;
        document.getElementById('fulfillmentModal').style.display = 'block';
    }

    function fulfillWithoutTracking() {
    fetch(`/mark-as-fulfilled/${currentOrderId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}', // Include the CSRF token
        },
        body: JSON.stringify({}),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Order fulfilled successfully without tracking');
            document.getElementById('fulfillmentModal').style.display = 'none';
            location.reload(); // Optional: reload the page to reflect changes
        } else {
            alert('Error fulfilling order without tracking');
        }
    })
    .catch(err => alert('Error fulfilling order'));
}


    function showTrackingForm() {
        document.getElementById('trackingForm').style.display = 'block';
    }
    function fulfillWithTracking() {
    const trackingNumber = document.getElementById('trackingNumber').value;
    const shippingCarrier = document.getElementById('shippingCarrier').value;

    fetch(`/mark-as-fulfilled-with-tracking/${currentOrderId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}', // Include CSRF token
        },
        body: JSON.stringify({
            trackingNumber,
            shippingCarrier
        }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Order fulfilled with tracking successfully');
            document.getElementById('fulfillmentModal').style.display = 'none';
            location.reload(); // Optional: reload the page to reflect changes
        } else {
            alert('Error fulfilling order with tracking');
        }
    })
    .catch(err => alert('Error fulfilling order with tracking'));
}

</script>
</html>
