<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('components.navbar')
    <title>إضافة شراء كسر جديد</title>
    <!-- Bootstrap RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.rtl.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .form-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-top: 20px;
            direction: rtl;
        }
        .form-header {
            background-color: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            border-right: 5px solid #0d6efd;
        }
        .btn-submit {
            background-color: #0d6efd;
            border: none;
            padding: 10px 20px;
        }
        .btn-cancel {
            background-color: #6c757d;
            border: none;
            padding: 10px 20px;
        }
        .form-label {
            font-weight: 600;
        }
        
        /* Enhanced checkbox styling */
        .custom-checkbox-container {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
        }
        
        .custom-checkbox-container:hover {
            border-color: #0d6efd;
            box-shadow: 0 0 5px rgba(13, 110, 253, 0.25);
        }
        
        .form-check {
            padding-right: 2.5em;
            padding-left: 0;
            margin-bottom: 0;
        }
        
        .form-check .form-check-input {
            float: right;
            margin-right: -2.5em;
            width: 1.5em;
            height: 1.5em;
            margin-top: 0.15em;
            cursor: pointer;
        }
        
        .form-check-label {
            font-weight: 600;
            margin-right: 10px;
            font-size: 1.1em;
            cursor: pointer;
        }
        
        .checkbox-description {
            display: block;
            margin-top: 5px;
            color: #6c757d;
            font-size: 0.9em;
            padding-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="form-container">
                    <div class="form-header">
                        <h3 class="mb-0">إضافة شراء كسر جديد</h3>
                    </div>

                    <form method="POST" action="{{ route('kasr-sales.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label for="customer_name" class="form-label">اسم العميل</label>
                                <input id="customer_name" type="text" class="form-control @error('customer_name') is-invalid @enderror" name="customer_name" value="{{ old('customer_name') }}" required autofocus>
                                @error('customer_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="weight" class="form-label">الوزن (جرام)</label>
                                <input id="weight" type="number" step="0.01" class="form-control @error('weight') is-invalid @enderror" name="weight" value="{{ old('weight') }}" required>
                                @error('weight')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label for="metal_purity" class="form-label">عيار الذهب</label>
                                <select id="metal_purity" class="form-select @error('metal_purity') is-invalid @enderror" name="metal_purity" required>
                                    <option value="">اختر العيار</option>
                                    <option value="24K" {{ old('metal_purity') == '24K' ? 'selected' : '' }}>عيار 24</option>
                                    <option value="22K" {{ old('metal_purity') == '22K' ? 'selected' : '' }}>عيار 22</option>
                                    <option value="21K" {{ old('metal_purity') == '21K' ? 'selected' : '' }}>عيار 21</option>
                                    <option value="18K" {{ old('metal_purity') == '18K' ? 'selected' : '' }}>عيار 18</option>
                                    <option value="18K" {{ old('metal_purity') == '14K' ? 'selected' : '' }}>عيار 14</option>
                                    <option value="18K" {{ old('metal_purity') == '12K' ? 'selected' : '' }}>عيار 12</option>
                                    <option value="18K" {{ old('metal_purity') == '9K' ? 'selected' : '' }}>عيار 9</option>
                                </select>
                                @error('metal_purity')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="kind" class="form-label">نوع القطعة</label>
                                <select id="kind" class="form-select @error('kind') is-invalid @enderror" name="kind">
                                    <option value="">اختر النوع</option>
                                    <option value="تعليفة" {{ old('kind') == 'تعليفة' ? 'selected' : '' }}>تعليفة</option>
                                    <option value="اسورة" {{ old('kind') == 'اسورة' ? 'selected' : '' }}>اسورة</option>
                                    <option value="حلق" {{ old('kind') == 'حلق' ? 'selected' : '' }}>حلق</option>
                                    <option value="كوليه" {{ old('kind') == 'كوليه' ? 'selected' : '' }}>كوليه</option>
                                    <option value="خاتم" {{ old('kind') == 'خاتم' ? 'selected' : '' }}>خاتم</option>
                                    <option value="بروش" {{ old('kind') == 'بروش' ? 'selected' : '' }}>بروش</option>
                                    <option value="ميدالية" {{ old('kind') == 'ميدالية' ? 'selected' : '' }}>ميدالية</option>    
                                    <option value="زرار " {{ old('kind') == 'زرار' ? 'selected' : '' }}>زرار</option>
                                    <option value="جنيه" {{ old('kind') == 'جنيه' ? 'selected' : '' }}>جنيه</option>
                                    <option value="تول " {{ old('kind') == 'تول' ? 'selected' : '' }}>تول</option>

                                </select>
                                @error('kind')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label for="offered_price" class="form-label">السعر </label>
                                <input id="offered_price" type="number" step="0.01" class="form-control @error('offered_price') is-invalid @enderror" name="offered_price" value="{{ old('offered_price') }}">
                                @error('offered_price')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="order_date" class="form-label">تاريخ الطلب</label>
                                <input id="order_date" type="date" class="form-control @error('order_date') is-invalid @enderror" name="order_date" value="{{ old('order_date', date('Y-m-d')) }}">
                                @error('order_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="image" class="form-label">صورة</label>
                                <input id="image" type="file" class="form-control @error('image') is-invalid @enderror" name="image">
                                @error('image')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="custom-checkbox-container">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="item_type" id="item_type" value="shop" {{ old('item_type') == 'shop' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="item_type">
                                            القطعة من صنعنا
                                        </label>
                                        {{-- <span class="checkbox-description">
                                            حدد هذا الخيار إذا كانت القطعة الذهبية من محلنا، وإلا فهي من العميل
                                        </span> --}}
                                        @error('item_type')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary btn-submit">
                                    حفظ
                                </button>
                                <a href="{{ route('kasr-sales.index') }}" class="btn btn-secondary btn-cancel me-2">
                                    إلغاء
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 