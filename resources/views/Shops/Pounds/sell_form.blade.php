<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بيع جنيه</title>
    @include('components.navbar')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <!-- Customer Details Section -->
                        <h2 class="card-title text-center mb-4" style="color: #28a745">بيانات الزبون</h2>
                        <form class="item-details-form" action="{{ route('gold-pounds.create-sale-request') }}"
                            method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class=" mb-3">
                                        <label for="first_name">الاسم الاول</label>
                                        <input type="text" class="form-control" id="first_name" name="first_name"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class=" mb-3">
                                        <label for="last_name">الاسم الاخير</label>
                                        <input type="text" class="form-control" id="last_name" name="last_name"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class=" mb-3">
                                        <label for="phone_number">رقم التليفون</label>
                                        <input type="number" class="form-control" id="phone_number"
                                            name="phone_number">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class=" mb-3">
                                        <label for="address">العنوان</label>
                                        <input type="text" class="form-control" id="address" name="address">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class=" mb-3">
                                        <label for="email">البريد الإلكتروني</label>
                                        <input type="email" class="form-control" id="email" name="email">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class=" mb-3">
                                        <label for="payment_method">طريقة الدفع</label>

                                        <select class="form-select" id="payment_method" name="payment_method">
                                            <option value="cash">كاش</option>
                                            <option value="visa">فيزا</option>
                                            <option value="value">فاليو</option>
                                            <option value="mogo">موجو</option>
                                            <option value="instapay">انستا باي</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                    </div>


                    <!-- Pounds Details Section -->
                    <h3 class="text-center mt-5 mb-4">تفاصيل الجنيهات</h3>
                    @foreach ($pounds as $pound)
                        <div class="card mb-4 border-primary">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">جنيه رقم {{ $pound->serial_number }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="hidden" name="serial_numbers[]"
                                            value="{{ $pound->serial_number }}">
                                        <div class="mb-2 d-flex justify-content-between">
                                            <strong>الرقم التسلسلي: </strong> 
                                            <span class="text-start">{{ $pound->serial_number }}</span>
                                        </div>
                                        <div class="mb-2 d-flex justify-content-between">
                                            <strong>النوع: </strong>
                                            <span class="text-start">
                                                {{ $pound->goldPound ? ucfirst(str_replace('_', ' ', $pound->goldPound->kind)) : 'غير متوفر' }}
                                            </span>
                                        </div>
                                        <div class="mb-2 d-flex justify-content-between">
                                            <strong>الوزن: </strong>
                                            <span class="text-start">
                                                {{ $pound->goldPound ? $pound->goldPound->weight : 'غير متوفر' }} جرام
                                            </span>
                                        </div>
                                        <div class="mb-2 d-flex justify-content-between">
                                            <strong>العيار: </strong>
                                            <span class="text-start">
                                                {{ $pound->goldPound ? $pound->goldPound->purity : 'غير متوفر' }} قيراط
                                            </span>
                                        </div>
                                        <div class="form-floating mt-3">
                                            <input type="number" class="form-control"
                                                id="price_{{ $pound->serial_number }}"
                                                name="prices[{{ $pound->serial_number }}]" step="0.01" required>
                                            <label for="price_{{ $pound->serial_number }}">
                                                السعر للجنيه ({{ $pound->serial_number }})
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-success btn-lg px-5">
                            <i class="fas fa-check-circle me-2"></i> إرسال طلب البيع
                        </button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sellForm = document.querySelector('.item-details-form');

            sellForm.addEventListener('submit', function(event) {
                event.preventDefault();

                fetch(sellForm.action, {
                        method: 'POST',
                        body: new FormData(sellForm),
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم بنجاح!',
                                text: 'تم إرسال طلب البيع بنجاح.',
                            }).then(() => {
                                window.location.href = '{{ route('gold-pounds.index') }}';
                            });
                        } else {
                            throw new Error(data.message || 'فشل في إرسال النموذج');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: error.message ||
                                'فشل في إرسال النموذج. برجاء المحاولة مرة أخرى.',
                        });
                    });
            });
        });
    </script>
</body>

</html>
