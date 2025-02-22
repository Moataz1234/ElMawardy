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
        /* Custom Radio and Checkbox Styles */
        .custom-control-group {
            display: flex;
            gap: 20rem;
            margin-bottom: 1rem;
            padding: 0.5rem;
            background-color: #f8f9fa;
            border-radius: 8px;
        }

        .custom-control {
            position: relative;
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: all 0.3s ease;
            border: 1px solid #dee2e6;
            background-color: white;
            min-width: 120px;
            justify-content: center;
        }

        .custom-radio, .custom-checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            margin: 0 0.5rem;
            transition: all 0.3s ease;
        }

        .custom-radio:hover, .custom-checkbox:hover {
            background-color: #f8f9fa;
            transform: translateY(-2px);
        }

        .custom-radio input:checked + label {
            color: #0d6efd;
            font-weight: 600;
        }

        .by-customer input:checked ~ .radio-circle {
            border-color: #198754;
            background-color: #198754;
        }

        .by-shop input:checked ~ .radio-circle {
            border-color: #dc3545;
            background-color: #dc3545;
        }

        .custom-checkbox input:checked + label {
            color: #6610f2;
            font-weight: 600;
        }

        .radio-circle, .checkbox-square {
            position: relative;
            width: 24px;
            height: 24px;
            border: 2px solid #adb5bd;
            transition: all 0.3s ease;
        }

        .radio-circle {
            border-radius: 50%;
        }

        .checkbox-square {
            border-radius: 4px;
        }

        input[type="radio"], input[type="checkbox"] {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }

        /* Radio button checked state */
        input[type="radio"]:checked ~ .radio-circle {
            border-color: #0d6efd;
            background-color: #0d6efd;
        }

        input[type="radio"]:checked ~ .radio-circle::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: white;
            animation: radioPop 0.3s ease;
        }

        /* Checkbox checked state */
        input[type="checkbox"]:checked ~ .checkbox-square {
            background-color: #6610f2;
            border-color: #6610f2;
            animation: checkboxPop 0.3s ease;
        }

        input[type="checkbox"]:checked ~ .checkbox-square::after {
            content: '';
            position: absolute;
            top: 3px;
            left: 7px;
            width: 6px;
            height: 12px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
            animation: checkmark 0.3s ease;
        }

        /* Animations */
        @keyframes radioPop {
            0% { transform: scale(0); }
            80% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        @keyframes checkboxPop {
            0% { transform: scale(0.9); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        @keyframes checkmark {
            from { height: 0; }
            to { height: 12px; }
        }

        .custom-control label {
            margin: 0;
            font-size: 0.95rem;
            white-space: nowrap;
            transition: color 0.3s ease;
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

        <div class="custom-control-group">
            <div class="d-flex justify-content-between">
            <div class="custom-radio by-shop">
                <input type="radio" class="order-type-radio" 
                       name="order_type[]" 
                       value="by_shop" 
                       id="by_shop_template">
                <span class="radio-circle"></span>
                <label for="by_shop_template">خاص بالمحل</label>
            </div>
            
            <div class="custom-radio by-customer">
                <input type="radio" class="order-type-radio" 
                       name="order_type[]" 
                       value="by_customer" 
                       id="by_customer_template" required>
                <span class="radio-circle"></span>
                <label for="by_customer_template">خاص بالعميل</label>
            </div>
            </div>
            <div class="custom-checkbox">
                <input type="checkbox" class="toggleLabel" id="sample_template">
                <span class="checkbox-square"></span>
                <label for="sample_template">عينة  </label>
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
