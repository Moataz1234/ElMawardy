
    <style>
    .btn-custom {
    padding: 10px 20px;
    border-radius: 5px;
    border: none;
    color: rgb(0, 0, 0);
    background-color: #49dc35;
}
        </style>
<body>
    <form action="{{ route('orders.accept') }}" method="POST">
        @csrf
<div class="container">
    {{-- <h2>Order Requests</h2> --}}

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>check</th>
                @php
                // Array of columns with their display names
                $columns = [
                    'shop_id    ' =>'shop_id',
                    'order_number' => 'Order Number',
                    'order_details' => 'Order Details',
                    'customer_name' => 'Customer Name',
                    'customer_phone' => 'Customer Phone',
                    'seller_name' => 'Seller Name',
                    'order_date' => 'Order Date',
                    'status' => 'Status',
                    'action' => 'Action',
                ];
            @endphp

            @foreach ($columns as $field => $label) 
                <th>
                        {{ $label }}
                        
                    </div>
                </th>
                @endforeach
            </tr>
        </thead>
        <tbody id="table-body">
            @foreach ($orders as $order)
                <tr>
                   
                    <td>
                        <input type="checkbox" name="order_ids[]" value="{{ $order->id }}" class="order-checkbox">
                    </td>                    
                    <td>{{ $order->shop_id }}</td>
                    <td>{{ $order->shop_id }}-{{ $order->order_number }}</td>
                    <td>{{ $order->order_details }}</td>
                    <td>{{ $order->customer_name }}</td>
                    <td>{{ $order->customer_phone }}</td>
                    <td>{{ $order->seller_name }}</td>
                    <td>{{ $order->order_date }}</td>
                    <td>{{ $order->status }}</td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('orders.show', $order->id) }}" class="action_button">View</a>
                        </div>
                    </td>
                    {{-- <td>
                        <form action="{{ route('orders.accept', $order->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn-custom">Review</button>
                        </form>
                    </td> --}}
                </tr>
            @endforeach

        </tbody>
    </table>

    </div>
    <button type="submit" class="btn-custom">Accept  </button>

</form>
<script src="{{ asset('js/order_details.js') }}"></script>

</body>
</html>
