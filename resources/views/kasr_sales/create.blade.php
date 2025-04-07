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
                            <div class="col-md-2 mb-3 mb-md-0">
                                <label for="customer_name" class="form-label">اسم العميل</label>
                                <input id="customer_name" type="text" class="form-control @error('customer_name') is-invalid @enderror" name="customer_name" value="{{ old('customer_name') }}" required autofocus>
                            </div>
                            
                            <div class="col-md-2">
                                <label for="customer_phone" class="form-label">رقم الهاتف</label>
                                <input id="customer_phone" type="text" class="form-control @error('customer_phone') is-invalid @enderror" name="customer_phone" value="{{ old('customer_phone') }}">
                                @error('customer_phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        {{-- </div> --}}

                        {{-- <div class="row mb-3"> --}}
                            <div class="col-md-2 mb-3 mb-md-0">
                                <label for="offered_price" class="form-label">السعر </label>
                                <input id="offered_price" type="number" step="0.01" class="form-control @error('offered_price') is-invalid @enderror" name="offered_price" value="{{ old('offered_price') }}">
                                @error('offered_price')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            
                            <div class="col-md-3">
                                <label for="order_date" class="form-label">تاريخ الطلب</label>
                                <input id="order_date" type="date" class="form-control @error('order_date') is-invalid @enderror" name="order_date" value="{{ old('order_date', date('Y-m-d')) }}">
                                @error('order_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="image" class="form-label">صورة</label>
                                <input id="image" type="file" class="form-control @error('image') is-invalid @enderror" name="image">
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">تفاصيل القطع</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="5%">#</th>
                                                <th width="20%">نوع القطعة</th>
                                                <th width="20%">عيار الذهب</th>
                                                <th width="12%">الوزن القائم</th>
                                                <th width="12%">الوزن الصافي</th>
                                                <th width="21%">القطعة من صنعنا</th>
                                                <th width="5%"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="items-container">
                                            <tr class="item-row">
                                                <td class="align-middle"><span class="item-number">1</span></td>
                                                <td>
                                                    <select class="form-select" name="items[0][kind]" required>
                                                        <option value="">اختر النوع</option>
                                                        <option value="تعليقة">تعليقة</option>
                                                        <option value="اسورة">اسورة</option>
                                                        <option value="حلق">حلق</option>
                                                        <option value="كوليه">كوليه</option>
                                                        <option value="خاتم">خاتم</option>
                                                        <option value="بروش">بروش</option>
                                                        <option value="ميدالية">ميدالية</option>    
                                                        <option value="زرار">زرار</option>
                                                        <option value="سلاسل">سلاسل</option>
                                                        <option value="جنيه">جنيه</option>
                                                        <option value="تول">تول</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-select" name="items[0][metal_purity]" required>
                                                        <option value="">اختر العيار</option>
                                                        <option value="24K">عيار 24</option>
                                                        <option value="22K">عيار 22</option>
                                                        <option value="21K">عيار 21</option>
                                                        <option value="18K">عيار 18</option>
                                                        <option value="14K">عيار 14</option>
                                                        <option value="12K">عيار 12</option>
                                                        <option value="9K">عيار 9</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" class="form-control" name="items[0][weight]" required>
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" class="form-control" name="items[0][net_weight]">
                                                </td>
                                                <td class="text-center">
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" name="items[0][item_type]" value="shop">
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-danger btn-sm remove-item-btn" style="display: none;">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="text-center mt-3">
                                    <button type="button" class="btn btn-success" id="add-item-btn">
                                        <i class="fas fa-plus-circle me-1"></i> إضافة قطعة
                                    </button>
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

    <!-- Add this script at the end of your file, before the closing </body> tag -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let itemCount = 1;
            
            // Add new item
            document.getElementById('add-item-btn').addEventListener('click', function() {
                itemCount++;
                
                const itemsContainer = document.getElementById('items-container');
                const newRow = document.createElement('tr');
                newRow.className = 'item-row';
                
                newRow.innerHTML = `
                    <td class="align-middle"><span class="item-number">${itemCount}</span></td>
                    <td>
                        <select class="form-select" name="items[${itemCount-1}][kind]" required>
                            <option value="">اختر النوع</option>
                            <option value="تعليقة">تعليقة</option>
                            <option value="اسورة">اسورة</option>
                            <option value="حلق">حلق</option>
                            <option value="كوليه">كوليه</option>
                            <option value="خاتم">خاتم</option>
                            <option value="بروش">بروش</option>
                            <option value="ميدالية">ميدالية</option>    
                            <option value="زرار">زرار</option>
                            <option value="سلاسل">سلاسل</option>
                            <option value="جنيه">جنيه</option>
                            <option value="تول">تول</option>
                        </select>
                    </td>
                    <td>
                        <select class="form-select" name="items[${itemCount-1}][metal_purity]" required>
                            <option value="">اختر العيار</option>
                            <option value="24K">عيار 24</option>
                            <option value="22K">عيار 22</option>
                            <option value="21K">عيار 21</option>
                            <option value="18K">عيار 18</option>
                            <option value="14K">عيار 14</option>
                            <option value="12K">عيار 12</option>
                            <option value="9K">عيار 9</option>
                        </select>
                    </td>
                    <td>
                        <input type="number" step="0.01" class="form-control" name="items[${itemCount-1}][weight]" required>
                    </td>
                    <td>
                        <input type="number" step="0.01" class="form-control" name="items[${itemCount-1}][net_weight]">
                    </td>
                    <td class="text-center">
                        <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" name="items[${itemCount-1}][item_type]" value="shop">
                        </div>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm remove-item-btn">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                
                itemsContainer.appendChild(newRow);
                
                // Show remove button for first item if we have more than one item
                if (itemCount > 1) {
                    document.querySelector('.remove-item-btn[style="display: none;"]')?.removeAttribute('style');
                }
                
                // Add event listener to the new remove button
                newRow.querySelector('.remove-item-btn').addEventListener('click', function() {
                    newRow.remove();
                    itemCount--;
                    
                    // Update item numbers
                    updateItemNumbers();
                    
                    // Hide remove button for first item if only one item remains
                    if (itemCount === 1) {
                        document.querySelector('.remove-item-btn').style.display = 'none';
                    }
                });
            });
            
            // Function to update item numbers after removal
            function updateItemNumbers() {
                const itemNumbers = document.querySelectorAll('.item-number');
                itemNumbers.forEach((span, index) => {
                    span.textContent = index + 1;
                });
                
                // Update input names to maintain sequential indices
                const itemRows = document.querySelectorAll('.item-row');
                itemRows.forEach((row, index) => {
                    const inputs = row.querySelectorAll('input, select');
                    inputs.forEach(input => {
                        const name = input.getAttribute('name');
                        if (name) {
                            const newName = name.replace(/items\[\d+\]/, `items[${index}]`);
                            input.setAttribute('name', newName);
                        }
                    });
                });
            }
            
            // Add event listener to the first row's remove button
            document.querySelector('.remove-item-btn').addEventListener('click', function() {
                if (itemCount > 1) {
                    this.closest('tr').remove();
                    itemCount--;
                    updateItemNumbers();
                    
                    // Hide remove button for first item if only one item remains
                    if (itemCount === 1) {
                        document.querySelector('.remove-item-btn').style.display = 'none';
                    }
                }
            });
        });
    </script>
</body>