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
            width: 95%; /* Increased width */
            max-width: 1800px; /* Increased max-width */
            direction: rtl;
        }
        .form-control, .form-select {
            height: 40px; /* Smaller input fields */
            font-size: 0.9rem;
        }
        .order-sections {
            display: flex;
            gap: 2rem;
        }
        .customer-section {
            flex: 1;
            padding-left: 2rem;
            border-left: 1px solid #dee2e6;
        }
        .items-section {
            flex: 1;
        }
        .section-header {
            background-color: #6c757d;
            color: white;
            padding: 0.75rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 1.1rem;
        }
        .order-item {
            background-color: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            border: 1px solid #dee2e6;
        }
        .form-label {
            font-size: 0.9rem;
            margin-bottom: 0.3rem;
        }
        .custom-control-group {
            gap: 1rem; /* Reduced gap */
            padding: 0.3rem;
        }
        .custom-control {
            min-width: 100px; /* Smaller minimum width */
            padding: 0.3rem 0.8rem;
        }
        textarea.form-control {
            height: auto;
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

        .shop-specific-fields {
            transition: all 0.3s ease;
        }
        
        .model-label, .model-input {
            transition: all 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <form class="custom-form" action="{{ route('orders.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="order-sections">
                <!-- Customer Information - Right Side -->
                <div class="customer-section">
                    <div class="section-header">
                        <i class="fas fa-user me-2"></i>معلومات العميل
                    </div>
                    
                    <!-- First Row: Customer Name and Seller Name -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="fas fa-user me-2"></i>اسم العميل
                            </label>
                            <input type="text" class="form-control" name="customer_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="fas fa-user-tie me-2"></i>اسم البائع
                            </label>
                            <input type="text" class="form-control" name="seller_name" required>
                        </div>
                    </div>
                    
                    <!-- Second Row: Phone and Order Date -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="fas fa-phone me-2"></i>تليفون العميل
                            </label>
                            <input type="number" class="form-control" name="customer_phone" maxlength="11" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="fas fa-calendar me-2"></i>تاريخ الطلب
                            </label>
                            <input type="date" class="form-control" name="order_date" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    
                    <!-- Third Row: Payment Method, Deposit, and Rest -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">
                                <i class="fas fa-credit-card me-2"></i>طريقة الدفع
                            </label>
                            <select class="form-select" name="payment_method">
                                <option value="">لا يوجد</option>
                                
                                <option value="value">فاليو</option>
                                <option value="cash">كاش</option>
                                <option value="instapay">انستاباي</option>
                                <option value="visa">فيزا (بنك مصر مواردي)</option>
                                <option value="visa">فيزا (بنك مصر بوليون فاروز)</option>
                                <option value="visa">فيزا (العربي الافريقي ماوردي)</option>
                                <option value="visa">فيزا (العربي الافريقي بوليون فاروز)</option>
                                <option value="visa">فيزا (CIB)</option>
                                <option value="visa">فيزا (جيديا)</option>

                                </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">
                                <i class="fas fa-money-bill-wave me-2"></i>المدفوع
                            </label>
                            <input type="number" class="form-control" name="deposit" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">
                                <i class="fas fa-money-bill-alt me-2"></i>الباقي
                            </label>
                            <input type="number" class="form-control" name="rest_of_cost" step="0.01">
                        </div>
                    </div>
                </div>

                <!-- Order Items - Left Side -->
                <div class="items-section">
                    <div class="section-header">
                        <i class="fas fa-list me-2"></i>تفاصيل القطع
                    </div>
                    
                    <div id="order-items">
                        <!-- Order items will be added here dynamically -->
                    </div>

                    <button type="button" id="add-item" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>إضافة قطعة
                    </button>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save me-2"></i>حفظ الطلب
                </button>
            </div>
        </form>
    </div>

    <!-- Move the template outside the form -->
    <div class="order-item" id="order-item-template" style="display: none">
        <div class="row mb-3">
            <div class="col-md-6">
                {{-- <label class="form-label">نوع القطعة</label> --}}
                <div class="d-flex gap-3">
                    <div class="custom-radio">
                        <input type="radio" class="item-type-radio" 
                               name="item_type[]" 
                               value="دهب" 
                               id="gold_template">
                        <span class="radio-circle"></span>
                        <label for="gold_template">دهب</label>
                    </div>
                    <div class="custom-radio">
                        <input type="radio" class="item-type-radio" 
                               name="item_type[]" 
                               value="الماظ" 
                               id="diamond_template">
                        <span class="radio-circle"></span>
                        <label for="diamond_template">الماظ</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="hidden-fields" style="display: none">
            <!-- First Row: Order Kind and Order Type -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">النوع</label>
                    <select name="order_kind[]" class="form-select">
                        <option value="">لا يوجد</option>
                        <option value="تعليفة">تعليفة</option>
                        <option value="اسورة">اسورة</option>
                        <option value="حلق">حلق</option>
                        <option value="كوليه">كوليه</option>
                        <option value="خاتم">خاتم</option>
                        <option value="بروش">بروش</option>
                        <option value="ميدالية">ميدالية</option>
                        <option value="زرار ">زرار</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">الوزن</label>
                    <input type="number" class="form-control" name="weight[]" step="0.001">
                </div>
                <div class="col-md-4">
                    <div class="custom-control-group flex-column">
                        <div class="d-flex gap-3 flex-column">
                            <div class="custom-radio by-shop">
                                <input type="radio" class="order-type-radio" 
                                       name="order_type[]" 
                                       value="by_shop" 
                                       id="by_shop_template">
                                <span class="radio-circle"></span>
                                <label for="by_shop_template"> المحل</label>
                            </div>
                            
                            <div class="custom-radio by-customer">
                                <input type="radio" class="order-type-radio" 
                                       name="order_type[]" 
                                       value="by_customer" 
                                       id="by_customer_template" >
                                <span class="radio-circle"></span>
                                <label for="by_customer_template"> العميل</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Second Row: Weight -->
            <div class="row mb-3">
              
            </div>

            <!-- Shop-specific fields (hidden by default) -->
            <div class="shop-specific-fields">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label model-label">الموديل</label>
                        <input type="text" class="form-control model-input" name="model[]">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label"> barcode</label>
                        <input type="text" class="form-control" name="serial_number[]">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">صورة المنتج</label>
                        <input type="file" class="form-control" name="image_link[]">
                    </div>
                </div>
            </div>

        </div>

        <div class="mb-3">
            <label class="form-label">موضوع الطلب</label>
            <textarea class="form-control" name="order_details[]" rows="3" required></textarea>
        </div>
        <button type="button" class="btn btn-danger remove-item mt-3">
            <i class="fas fa-trash me-2"></i>حذف القطعة
        </button>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/order_details.js') }}"></script>
</body>
</html>
