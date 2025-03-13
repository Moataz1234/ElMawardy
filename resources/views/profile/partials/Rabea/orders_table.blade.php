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
        margin-bottom: 10px;
        border-bottom: 1px solid #eee;
        background-color: #f8f9fa;
        border-radius: 5px;
    }

    .item-number {
        font-weight: bold;
        color: #007bff;
        margin-bottom: 5px;
    }
</style>

@include('profile.partials.Rabea.navigation')

<table class="table">
    <thead>
        <tr>
            <th></th>
            <th>رقم الفرع</th>
            <th>رقم الأوردر</th>
            <th>اسم العميل</th>
            <th>رقم العميل</th>
            <th>اسم البائع</th>
            <th>تفاصيل القطع</th>
            <th>حالة الطلب</th>
            <th>آخر تحديث</th>
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
                <td>
                    <i class="fas fa-info-circle details-icon" 
                       onclick="showOrderDetails({{ $order->id }})"></i>
                </td>
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
                <td>{{ $order->updated_at->format('Y-m-d') }}</td>
                <td>
                    <div class="action-buttons">
                        <a href="{{ route('orders.show', $order->id) }}" class="action_button">عرض</a>
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

<!-- Status Buttons -->
<div class="order-status">
    <div style="text-align: center;">
        <form action="{{ route('orders.updateStatus.bulk') }}" method="POST" id="bulk-status-form" style="display: none;">
            @csrf
            <input type="hidden" name="status" id="bulk-status-input">
            <input type="hidden" name="order_ids" id="selected-orders-input">
        </form>
        
        <button class="status-button status-خلص" onclick="changeBulkStatus('خلص')">خلص</button>
        <button class="status-button status-في-الدمغة" onclick="changeBulkStatus('في الدمغة')">في الدمغة</button>
        <button class="status-button status-في-الورشة" onclick="changeBulkStatus('في الورشة')">في الورشة</button>
    </div>
</div>

<script>
// Prepare the orders data in a simpler format
const orders = {
    @foreach($orders as $order)
        {{ $order->id }}: {
            id: {{ $order->id }},
            items: [
                @foreach($order->items as $item)
                    {
                        item_type: "{{ $item->item_type }}",
                        order_kind: "{{ $item->order_kind }}",
                        weight: "{{ $item->weight }}",
                        ring_size: "{{ $item->ring_size }}",
                        order_details: "{{ $item->order_details }}",
                        order_type: "{{ $item->order_type }}"
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
                    <div><strong>النوع:</strong> ${item.item_type}</div>
                    <div><strong>الصنف:</strong> ${item.order_kind}</div>
                    ${item.weight ? `<div><strong>الوزن:</strong> ${item.weight}</div>` : ''}
                    ${item.ring_size ? `<div><strong>مقاس الخاتم:</strong> ${item.ring_size}</div>` : ''}
                    <div><strong>التفاصيل:</strong> ${item.order_details}</div>
                    <div><strong>نوع الطلب:</strong> ${item.order_type === 'by_customer' ? 'طلب العميل' : 'طلب المحل'}</div>
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

function changeBulkStatus(status) {
    const checkboxes = document.querySelectorAll('input[type="checkbox"]:checked');
    if (checkboxes.length === 0) {
        alert('Please select at least one order to change its status.');
        return;
    }

    const orderIds = Array.from(checkboxes).map(checkbox => {
        return checkbox.closest('tr').dataset.orderId;
    });

    document.getElementById('bulk-status-input').value = status;
    document.getElementById('selected-orders-input').value = JSON.stringify(orderIds);
    document.getElementById('bulk-status-form').submit();
}
</script>