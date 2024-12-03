{{-- <div class="spreadsheet"> --}}
    <table class="table">
        <thead>
            <tr>
                <th>رقم الفرع</th>
                <th>رقم الأوردر</th>
                <th>اسم العميل</th>
                <th>رقم العميل</th>
                <th>اسم البائع</th>
                <th>موضوع الطلب</th>
                <th>حالة الطلب</th>
            </tr>
        </thead>
        <tbody id="table-body">
            @foreach ($orders as $order)
                <tr data-order-id="{{ $order->id }}">
                    <td>{{ $order->shop_id }}</td>
                    <td>{{ $order->order_number }}</td>
                    <td>{{ $order->customer_name }}</td>
                    <td>{{ $order->customer_phone }}</td>
                    <td>{{ $order->seller_name }}</td>
                    <td>{{ $order->order_details }}</td>
                    <td>
                        <div class="status-box status-cell" 
                             data-status="{{ $order->status }}"
                             style="background-color: {{ 
                                $order->status == 'خلص' ? 'rgb(179, 5, 121)' : 
                                ($order->status == 'في الدمغة' ? 'rgba(64, 152, 199, 0.862)' : 
                                ($order->status == 'في الورشة' ? 'rgb(200, 151, 5)' : 
                                ($order->status == 'تم الإستلام' ? 'rgba(104, 180, 22, 0.971)' : ''))) 
                             }}">
                            {{ $order->status }}
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
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

}    
</script>