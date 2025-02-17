<!DOCTYPE html>
<html lang="en">
<head>
    @include('components.navbar')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Customer Orders</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
        }
        .custom-form {
            background-color: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            margin: 2rem auto;
            max-width: 1200px;
            direction: rtl;
        }
        .order-item {
            background-color: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            border: 1px solid #dee2e6;
        }
        .section-header {
            background-color: #6c757d;
            color: white;
            padding: 0.75rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .remove-item {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <form class="custom-form" action="{{ route('orders.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- Customer Information -->
            <div class="section-header mb-4">
                <i class="fas fa-user me-2"></i>معلومات العميل
            </div>
            
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        <i class="fas fa-user me-2"></i>اسم العميل
                    </label>
                    <input type="text" class="form-control" name="customer_name" >
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        <i class="fas fa-phone me-2"></i>تليفون العميل
                    </label>
                    <input type="number" class="form-control" name="customer_phone" maxlength="11" >
                </div>
            </div>

            <!-- Order Details -->
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        <i class="fas fa-user-tie me-2"></i>البائع
                    </label>
                    <input type="text" class="form-control" name="seller_name" >
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        <i class="fas fa-credit-card me-2"></i>طريقة الدفع
                    </label>
                    <select class="form-select" name="payment_method">
                        <option value="">لا يوجد</option>
                        <option value="visa">Visa</option>
                        <option value="value">Value</option>
                        <option value="cash">Cash</option>
                        <option value="instapay">Instapay</option>
                    </select>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        <i class="fas fa-money-bill-wave me-2"></i>المدفوع
                    </label>
                    <input type="number" class="form-control" name="deposit" step="0.01">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        <i class="fas fa-money-bill-alt me-2"></i>الباقي
                    </label>
                    <input type="number" class="form-control" name="rest_of_cost" step="0.01">
                </div>
            </div>

            <!-- Dates -->
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        <i class="fas fa-calendar me-2"></i>تاريخ الطلب
                    </label>
                    <input type="date" class="form-control" name="order_date" value="{{ date('Y-m-d') }}">
                </div>
            </div>

            <!-- Order Description -->
            <div class="mb-4">
                <label class="form-label">
                    <i class="fas fa-file-alt me-2"></i>موضوع الطلب
                </label>
                <textarea class="form-control" name="order_details" rows="4"></textarea>
            </div>

            <!-- Order Items -->
            <div class="section-header mb-4">
                <i class="fas fa-list me-2"></i>تفاصيل الطلب
            </div>

            <div id="order-items">
                
            </div>

            <!-- Buttons -->
            <div class="d-flex justify-content-between mt-4">
                <button type="button" id="add-item" class="btn btn-success">
                    <i class="fas fa-plus me-2"></i>إضافة عنصر
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>حفظ الطلب
                </button>
            </div>
        </form>
    </div>

    <!-- Move the template outside the form -->
    <div class="order-item" id="order-item-template" style="display: none">
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">النوع</label>
                <select name="order_kind[]" class="form-select">
                    @foreach ($kinds as $kind)
                        <option value="{{ $kind }}">{{ $kind }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">المشكلة</label>
                <select name="order_fix_type[]" class="form-select">
                    <option value="اوردر جديد">اوردر جديد</option>
                    <option value="تصليح">تصليح</option>
                    <option value="عمل مقاس">عمل مقاس</option>
                    <option value="تلميع">تلميع</option>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <div class="form-check">
                <input type="checkbox" class="form-check-input toggleLabel">
                <label class="form-check-label">عينة</label>
            </div>
        </div>

        <div class="weight_field" style="display: none">
            <label class="form-label">الوزن</label>
            <input type="number" class="form-control" name="weight[]" step="0.001">
        </div>

        <div class="image_field" style="display: none">
            <label class="form-label">صورة المنتج</label>
            <input type="file" class="form-control" name="image_link[]">
        </div>

        <button type="button" class="btn btn-danger remove-item mt-3">
            <i class="fas fa-trash me-2"></i>حذف العنصر
        </button>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/order_details.js') }}"></script>
</body>
</html>
