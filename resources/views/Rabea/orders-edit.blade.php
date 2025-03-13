<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    @include('components.navbar')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل طلب العميل</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/order-details.css') }}" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f5f5f5;
        }
        .form-section {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            padding: 25px;
            margin-bottom: 20px;
        }
        .section-header {
            background-color: #f8f9fa;
            padding: 10px 15px;
            margin: -25px -25px 20px -25px;
            border-radius: 10px 10px 0 0;
            border-bottom: 2px solid #e9ecef;
        }
        .order-item {
            background-color: #f8d7da; /* Light red background */
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            position: relative;
            max-height: 300px;
            overflow-y: auto;
        }
        .form-floating > label {
            right: 0;
            left: auto;
            padding-right: 1rem;
        }
        .form-floating > .form-control {
            padding-right: 1rem;
        }
        .form-select {
            background-position: left 0.75rem center;
            padding-right: 1rem;
        }
        .item-header {
            background: rgba(255, 255, 255, 0.9);
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .order-type-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .order-type-label {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .radio-group {
            display: flex;
            gap: 20px;
        }
        .custom-radio {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        .custom-radio input {
            width: 18px;
            height: 18px;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <form action="{{ route('orders.update', $order->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <!-- Customer Information Section -->
            <div class="form-section">
                <div class="section-header">
                    <h4 class="mb-0"><i class="fas fa-user ms-2"></i>بيانات العميل</h4>
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="text" class="form-control" name="customer_name" id="customer_name" value="{{ $order->customer_name }}" required>
                            <label for="customer_name">اسم العميل</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="tel" class="form-control" name="customer_phone" id="customer_phone" value="{{ $order->customer_phone }}" >
                            <label for="customer_phone">تليفون العميل</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="text" class="form-control" name="seller_name" id="seller_name" value="{{ $order->seller_name }}" required>
                            <label for="seller_name">البائع</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Details Section -->
            <div class="form-section">
                <div class="section-header">
                    <h4 class="mb-0"><i class="fas fa-file-invoice ms-2"></i>تفاصيل الطلب</h4>
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <div class="form-floating">
                            <textarea class="form-control" name="order_details" id="order_details" style="height: 100px">{{ $order->order_details }}</textarea>
                            <label for="order_details">موضوع الطلب</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating">
                            <input type="number" class="form-control" name="deposit" id="deposit" value="{{ $order->deposit }}" step="0.01">
                            <label for="deposit">المدفوع</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating">
                            <input type="number" class="form-control" name="rest_of_cost" id="rest_of_cost" value="{{ $order->rest_of_cost }}" step="0.01">
                            <label for="rest_of_cost">الباقي</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating">
                            <select class="form-select" name="payment_method" id="payment_method">
                                <option value="visa" {{ $order->payment_method == 'visa' ? 'selected' : '' }}>فيزا</option>
                                <option value="value" {{ $order->payment_method == 'value' ? 'selected' : '' }}>فاليو</option>
                                <option value="cash" {{ $order->payment_method == 'cash' ? 'selected' : '' }}>كاش</option>
                                <option value="instapay" {{ $order->payment_method == 'instapay' ? 'selected' : '' }}>انستا باي</option>
                            </select>
                            <label for="payment_method">طريقة الدفع</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating">
                            <input type="date" class="form-control" name="order_date" id="order_date" value="{{ $order->order_date }}">
                            <label for="order_date">تاريخ الاستلام</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items Section -->
            <div class="form-section">
                <div class="section-header">
                    <h4 class="mb-0"><i class="fas fa-list ms-2"></i>المنتجات</h4>
                </div>
                
                <div id="order-items">
                    @foreach ($order->items as $index => $item)
                        <div class="order-item">
                            <input type="hidden" name="order_item_id[]" value="{{ $item->id }}">
                            
                            <div class="item-header">
                                <h5 class="mb-0">القطعة {{ $index + 1 }}</h5>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>النوع</label>
                                        <select name="item_type[]" class="form-select">
                                            <option value="Gold" {{ $item->item_type == 'Gold' ? 'selected' : '' }}>Gold</option>
                                            <option value="Diamond" {{ $item->item_type == 'Diamond' ? 'selected' : '' }}>Diamond</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>الصنف</label>
                                        <select name="order_kind[]" class="form-select">
                                            @foreach ($kinds as $kind)
                                                <option value="{{ $kind }}">{{ $kind }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>الوزن</label>
                                        <input type="number" class="form-control" name="weight[]" value="{{ $item->weight }}" step="0.01">
                                    </div>
                                </div>
{{-- 
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>مقاس الخاتم</label>
                                        <input type="number" class="form-control" name="ring_size[]" value="{{ $item->ring_size }}">
                                    </div>
                                </div> --}}

                                <div class="col-12">
                                    <div class="form-group">
                                        <label>تفاصيل القطعة</label>
                                        <textarea class="form-control" name="order_details[]" rows="2">{{ $item->order_details }}</textarea>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="order-type-container">
                                        <div class="order-type-label">نوع الطلب</div>
                                        <div class="radio-group">
                                            <label class="custom-radio">
                                                <input type="radio" name="order_type[{{ $index }}]" 
                                                       value="by_customer" 
                                                       {{ $item->order_type === 'by_customer' ? 'checked' : '' }} required>
                                                طلب العميل
                                            </label>
                                            <label class="custom-radio">
                                                <input type="radio" name="order_type[{{ $index }}]" 
                                                       value="by_shop" 
                                                       {{ $item->order_type === 'by_shop' ? 'checked' : '' }}>
                                                طلب المحل
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-lg px-5">
                    <i class="fas fa-save ms-2"></i>تحديث الطلب
                </button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/order_details.js') }}"></script>
</body>
</html>
