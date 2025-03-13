<style>
    .btn-custom {
        padding: 10px 20px;
        border-radius: 5px;
        border: none;
        color: rgb(0, 0, 0);
        background-color: #49dc35;
    }
    .order-items {
        margin-top: 10px;
        padding: 5px;
        background-color: #f8f9fa;
        border-radius: 5px;
    }
    .order-item {
        padding: 5px;
        margin-bottom: 5px;
        border-bottom: 1px solid #dee2e6;
    }
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
    }
    
    .item-number {
        font-weight: bold;
        color: #007bff;
        margin-bottom: 5px;
    }
</style>

<div class="container">
    <form action="{{ route('orders.accept') }}" method="POST">
        @csrf

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @elseif (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <table class="table">
            <thead>
                <tr>
                    <th>تحديد</th>
                    <th>رقم المحل</th>
                    <th>رقم الطلب</th>
                    <th>اسم العميل</th>
                    <th>رقم الهاتف</th>
                    <th>اسم البائع</th>
                    <th>تاريخ الطلب</th>
                    <th>الحالة</th>
                    <th>تفاصيل القطع</th>
                    <th>الإجراءات</th>
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
                        <td>{{ $order->customer_name }}</td>
                        <td>{{ $order->customer_phone }}</td>
                        <td>{{ $order->seller_name }}</td>
                        <td>{{ $order->order_date }}</td>
                        <td>{{ $order->status }}</td>
                        <td>
                            <i class="fas fa-info-circle details-icon" 
                               onclick="showOrderDetails({{ $order->id }})"></i>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('orders.show', $order->id) }}" class="action_button">عرض</a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <button type="submit" class="btn-custom">استلام الطلبات </button>
    </form>
</div>

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
// Add this to your existing order_details.js or include it here
function showOrderDetails(orderId) {
    const modal = document.getElementById('detailsModal');
    const modalBody = document.getElementById('modalBody');
    const orders = @json($orders);
    
    // Find the order
    const order = orders.find(o => o.id === orderId);
    
    if (order) {
        let detailsHtml = '';
        order.items.forEach((item, index) => {
            detailsHtml += `
                <div class="item-details">
                    <div class="item-number">القطعة ${index + 1}</div>
                    <div><strong>النوع:</strong> ${item.item_type} - ${item.order_kind}</div>
                    <div><strong>موضوع الطلب:</strong> ${item.order_details}</div>
                    ${item.weight ? `<div><strong>الوزن:</strong> ${item.weight}</div>` : ''}
                    ${item.gold_color ? `<div><strong>لون الذهب:</strong> ${item.gold_color}</div>` : ''}
                    <div><strong>نوع الطلب:</strong> ${item.order_type}</div>
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

<script src="{{ asset('js/order_details.js') }}"></script>
