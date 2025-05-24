<div class="table-responsive">
    <table class="table table-hover align-middle">
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
            @forelse($orders as $order)
            <tr>
                <td><strong>#{{ $order->id }}</strong></td>
                <td>{{ $order->order_number }}</td>
                <td>
                    <div>
                        <strong>{{ $order->customer_name }}</strong>
                        @if($order->customer_phone)
                            <br><small class="text-muted">{{ $order->customer_phone }}</small>
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
                        <small class="text-muted">{{ \Carbon\Carbon::parse($order->order_date)->format('M d, Y H:i') }}</small>
                    @else
                        <span class="text-muted">N/A</span>
                    @endif
                </td>
                <td>
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-outline-info" 
                                onclick="viewOrderDetails('{{ $order->id }}')" 
                                title="View Order">
                            <i class="bx bx-show"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-primary ms-1" 
                                onclick="editOrder('{{ $order->id }}')" 
                                title="Edit Order">
                            <i class="bx bx-edit"></i>
                        </button>
                        @if($order->status == 'pending')
                        <div class="btn-group ms-1" role="group">
                            <button type="button" class="btn btn-sm btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" title="Update Status">
                                <i class="bx bx-check"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="updateOrderStatus('{{ $order->id }}', 'in_progress')">Mark In Progress</a></li>
                                <li><a class="dropdown-item" href="#" onclick="updateOrderStatus('{{ $order->id }}', 'completed')">Mark Completed</a></li>
                                <li><a class="dropdown-item" href="#" onclick="updateOrderStatus('{{ $order->id }}', 'cancelled')">Cancel Order</a></li>
                            </ul>
                        </div>
                        @endif
                        <button class="btn btn-sm btn-outline-danger ms-1" 
                                onclick="deleteOrder('{{ $order->id }}')" 
                                title="Delete Order">
                            <i class="bx bx-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center py-4">
                    <div class="text-muted">
                        <i class="bx bx-package fs-1 d-block mb-2"></i>
                        No orders found
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <!-- Pagination -->
    @if(isset($is_collection) && $is_collection)
        <!-- For collections, we'll show a simple count -->
        @if($orders->count() > 0)
        <div class="d-flex justify-content-center">
            <small class="text-muted">Showing {{ $orders->count() }} orders</small>
        </div>
        @endif
    @else
        <!-- For paginated results -->
        @if($orders->hasPages())
        <div class="d-flex justify-content-center">
            {{ $orders->appends(request()->query())->links('vendor.pagination.custom-super') }}
        </div>
        @endif
    @endif
</div> 