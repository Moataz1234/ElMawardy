<style>
    .details-icon {
        cursor: pointer;
        color: #007bff;
        font-size: 1.2rem;
    }
    
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.4);
    }
    
    .modal-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        border-radius: 8px;
        direction: rtl;
    }
    
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 1rem;
        margin-bottom: 1rem;
    }
    
    .close {
        color: #aaa;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }
    
    .item-details {
        padding: 10px;
        margin-bottom: 20px;
        border-bottom: 1px solid #eee;
        background-color: #f8f9fa;
        border-radius: 5px;
    }

    .item-number {
        font-weight: bold;
        color: #007bff;
        margin-bottom: 15px;
        font-size: 16px;
        background-color: #e9ecef;
        padding: 8px;
        border-radius: 5px;
    }
    
    .item-row {
        display: flex;
        flex-wrap: wrap;
        margin-bottom: 10px;
        background-color: #fff;
        border-radius: 5px;
        padding: 5px;
    }
    
    .item-cell {
        flex: 0 0 33.33%;
        padding: 8px;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .item-details-section {
        margin: 10px 0;
        padding: 10px;
        background-color: #f8f9fa;
        border-radius: 5px;
        border-left: 3px solid #007bff;
    }
    
    .new-fields-section {
        display: flex;
        flex-wrap: wrap;
        margin-top: 10px;
        background-color: #f0f8ff;
        padding: 10px;
        border-radius: 5px;
        border-left: 3px solid #28a745;
    }
    
    .new-field-cell {
        flex: 0 0 25%;
        padding: 8px;
    }
</style>
<table class="table">
    <thead>
        <tr>
            <th>رقم الفرع</th>
            <th>رقم الأوردر</th>
            <th>اسم العميل</th>
            <th>رقم العميل</th>
            <th>اسم البائع</th>
            <th>تفاصيل القطع</th>
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
                <td>
                    <i class="fas fa-info-circle details-icon" onclick="showOrderDetails({{ $order->id }})"></i>
                </td>
                <td>
                    <div class="status-box status-cell" class="status-cell" data-status="{{ $order->status }}"
                        style="background-color: {{ $order->status == 'في انتظار الموافقة'
                            ? 'rgb(200, 50, 50)'
                            : ($order->status == 'في الدمغة'
                                ? 'rgba(64, 152, 199, 0.862)'
                                : ($order->status == 'في الورشة'
                                    ? 'rgb(200, 151, 5)'
                                    : ($order->status == 'تم الإستلام'
                                        ? 'rgba(104, 180, 22, 0.971)'
                                        : ''))) }}">
                        {{ $order->status }}
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<!-- Modal -->
<div id="detailsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h4>تفاصيل القطع</h4>
            <span class="close">&times;</span>
        </div>
        <div id="modalBody">
            <!-- Details will be inserted here -->
        </div>
    </div>
</div>

<script>
    // Prepare the orders data in a simpler format
    const orders = {
        @foreach ($orders as $order)
            {{ $order->id }}: {
                id: {{ $order->id }},
                items: [
                    @foreach ($order->items as $item)
                        {
                            item_type: "{{ $item->item_type }}",
                            order_kind: "{{ $item->order_kind }}",
                            weight: "{{ $item->weight }}",
                            // ring_size: "{{ $item->ring_size }}",
                            model: "{{ $item->model }}",
                            serial_number: "{{ $item->serial_number }}",
                            order_details: "{{ $item->order_details }}",
                            order_type: "{{ $item->order_type }}",
                            cost: "{{ $item->cost }}",
                            gold_weight: "{{ $item->gold_weight }}",
                            new_barcode: "{{ $item->new_barcode }}",
                            new_diamond_number: "{{ $item->new_diamond_number }}"
                        },
                    @endforeach
                ]
            },
        @endforeach
    };

    function showOrderDetails(orderId) {
        const modal = document.getElementById('detailsModal');
        const modalBody = document.getElementById('modalBody');

        // Get the order from our prepared data
        const order = orders[orderId];

        if (order) {
            let detailsHtml = '';
            order.items.forEach((item, index) => {
                detailsHtml += `
                    <div class="item-details">
                        <div class="item-number">القطعة ${index + 1}</div>
                        
                        <div class="item-row">
                            <!-- First row - 3 items -->
                            <div class="item-cell"><strong>النوع:</strong> ${item.item_type}</div>
                            <div class="item-cell"><strong>النوع:</strong> ${item.order_kind}</div>
                            <div class="item-cell"><strong>الوزن:</strong> ${item.weight || ''}</div>
                            
                            <!-- Second row - 3 items -->
                            <div class="item-cell"><strong>الموديل:</strong> ${item.model || ''}</div>
                            <div class="item-cell"><strong>رقم القطعة:</strong> ${item.serial_number || ''}</div>
                            <div class="item-cell"><strong>نوع الطلب:</strong> ${item.order_type === 'by_customer' ? 'طلب العميل' : 'طلب المحل'}</div>
                        </div>
                        
                        <!-- Order details in its own row -->
                        <div class="item-details-section">
                            <strong>التفاصيل:</strong> ${item.order_details || ''}
                        </div>
                        
                        <!-- New fields in the last row -->
                        <div class="new-fields-section">
                            <div class="new-field-cell"><strong>التكلفة:</strong> ${item.cost || ''}</div>
                            <div class="new-field-cell"><strong>وزن الذهب:</strong> ${item.gold_weight || ''}</div>
                            <div class="new-field-cell"><strong>الباركود الجديد:</strong> ${item.new_barcode || ''}</div>
                            <div class="new-field-cell"><strong>رقم القطعة الجديد:</strong> ${item.new_diamond_number || ''}</div>
                        </div>
                    </div>
                `;
            });

            modalBody.innerHTML = detailsHtml;
            modal.style.display = "block";
        }
    }

    // Close modal when clicking the X
    document.querySelector('.close').onclick = function() {
        document.getElementById('detailsModal').style.display = "none";
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('detailsModal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

<!-- Add Font Awesome for the icon -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
