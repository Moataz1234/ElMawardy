<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بيع قطعة</title>
    {{-- <link href="{{ url('css/style.css') }}" rel="stylesheet">
    <link href="{{ url('css/sell_form.css') }}" rel="stylesheet"> --}}
    @include('components.navbar')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .badge {
            padding: 0.5em 1em;
            font-size: 0.85em;
            font-weight: 500;
            border-radius: 4px;
            color: white;
        }
        .modal-body table td {
            vertical-align: middle;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center" dir="rtl">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <!-- Returning Customer Alert -->
                        <div id="returningCustomerAlert" class="alert alert-info d-none mb-4" role="alert">
                            <strong>عميل سابق!</strong> هذا العميل قام بالشراء من قبل. 
                            <button type="button" class="btn btn-link" data-bs-toggle="modal" data-bs-target="#customerHistoryModal">
                                عرض التفاصيل
                            </button>
                        </div>

                        <!-- Customer History Modal -->
                        <div class="modal fade" id="customerHistoryModal" tabindex="-1" aria-labelledby="customerHistoryModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="customerHistoryModalLabel">سجل مشتريات العميل</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>تاريخ الطلب</th>
                                                        <th>الحالة</th>
                                                        <th>نوع القطعة</th>
                                                        <th>الرقم التسلسلي</th>
                                                        <th>النوع</th>
                                                        <th>الوزن</th>
                                                        <th>السعر</th>
                                                        <th>طريقة الدفع</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="purchaseHistoryTableBody">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Details Section -->
                        <h2 class="card-title text-center mb-4" style="color: #28a745">بيانات الزبون</h2>
                        <form class="item-details-form" action="{{ route('shop-items.bulkSell') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class=" mb-3">
                                        <label for="first_name">الاسم الاول</label>
                                        <input type="text" class="form-control" id="first_name" name="first_name"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class=" mb-3">
                                        <label for="last_name">الاسم الاخير</label>
                                        <input type="text" class="form-control" id="last_name" name="last_name"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class=" mb-3">
                                        <label for="phone_number">رقم التليفون</label>
                                        <input type="number" class="form-control" id="phone_number"
                                            name="phone_number">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class=" mb-3">
                                        <label for="address">العنوان</label>
                                        <input type="text" class="form-control" id="address" name="address">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class=" mb-3">
                                        <label for="email">البريد الإلكتروني</label>
                                        <input type="email" class="form-control" id="email" name="email">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class=" mb-3">
                                        <label for="payment_method">طريقة الدفع</label>

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
                                            <option value="visa">تحويل بنكي ( بوليون فاروز)</option>
                                            <option value="visa">تحويل بنكي ( المواردي بنك مصر  )</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="sold_date">تاريخ البيع</label>
                                        <input type="date" class="form-control" id="sold_date" name="sold_date" value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                            </div>
                    </div>

                    <!-- Item Details Section -->
                    <h3 class="text-center mt-5 mb-4">تفاصيل القطع</h3>
                    @foreach ($goldItems as $item)
                        <div class="card mb-4 border-primary">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">قطعة رقم {{ $item->serial_number }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Item Details Column -->
                                    <div class="col-md-6">
                                        <input type="hidden" name="ids[]" value="{{ $item->id }}">
                                        <div class="mb-2 d-flex justify-content-between">
                                            <strong>اسم المحل: </strong>
                                            <span class="text-start">{{ $item->shop_name }}</span>
                                        </div>
                                        <div class="mb-2 d-flex justify-content-between">
                                            <strong>رقم المحل: </strong>
                                            <span class="text-start">{{ $item->shop_id }}</span>
                                        </div>
                                        <div class="mb-2 d-flex justify-content-between">
                                            <strong>النوع: </strong>
                                            <span class="text-start">{{ $item->kind }}</span>
                                        </div>
                                        <div class="mb-2 d-flex justify-content-between">
                                            <strong>الموديل: </strong>
                                            <span class="text-start">{{ $item->model }}</span>
                                        </div>
                                        <div class="mb-2 d-flex justify-content-between">
                                            <strong>اللون: </strong>
                                            <span class="text-start">{{ $item->gold_color }}</span>
                                        </div>
                                        <div class="mb-2 d-flex justify-content-between">
                                            <strong>الوزن: </strong>
                                            <span class="text-start">{{ $item->weight }}</span>
                                        </div>
                                        <div class="form-floating mt-3">
                                            <input type="number" class="form-control" id="price_{{ $item->id }}"
                                                name="prices[{{ $item->id }}]" step="0.01" required>
                                            <label for="price_{{ $item->id }}">السعر للقطعة
                                                ({{ $item->serial_number }})</label>
                                        </div>
                                    </div>

                                    @if ($pound = $associatedPounds->get($item->id))
                                        <div class="col-md-6">
                                            <div class="card h-100 border-warning">
                                                <div class="card-header bg-warning text-dark">
                                                    <h5 class="mb-0">الجنيه المرتبط</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="mb-2 d-flex justify-content-between">
                                                        <strong>الرقم التسلسلي: </strong>
                                                        <span>{{ $pound->serial_number }}</span>
                                                    </div>
                                                    <div class="mb-2 d-flex justify-content-between">
                                                        <strong>النوع: </strong>
                                                        <span>{{ ucfirst(str_replace('_', ' ', $pound->goldPound->kind)) }}</span>
                                                    </div>
                                                    <div class="mb-2 d-flex justify-content-between">
                                                        <strong>الوزن: </strong>
                                                        <span>{{ $pound->goldPound->weight }}g</span>
                                                    </div>
                                                    <div class="mb-2 d-flex justify-content-between">
                                                        <strong>العيار: </strong>
                                                        <span>{{ $pound->goldPound->purity }}K</span>
                                                    </div>
                                                    <div class="form-floating mt-3">
                                                        <input type="number" class="form-control"
                                                            id="pound_price_{{ $pound->serial_number }}"
                                                            name="pound_prices[{{ $pound->serial_number }}]"
                                                            step="0.01" required>
                                                        <label for="pound_price_{{ $pound->serial_number }}">سعر
                                                            الجنيه</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-success btn-lg px-5" id="submitButton">
                            <i class="fas fa-check-circle me-2"></i> إرسال طلب البيع
                        </button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Add this modal at the bottom of your form, before closing body tag -->
    {{-- <div class="modal fade" id="poundPriceModal" tabindex="-1" aria-labelledby="poundPriceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="poundPriceModalLabel">تحديد سعر الجنيه</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="poundDetails" class="mb-3"></div>
                    <form id="poundPriceForm">
                        @csrf
                        <input type="hidden" id="poundSerialNumber" name="serial_number">
                        <div class="form-group">
                            <label for="poundPrice">سعر الجنيه</label>
                            <input type="number" class="form-control" id="poundPrice" name="price" required step="0.01">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    <button type="button" class="btn btn-primary" id="submitPoundPrice">حفظ</button>
                </div>
            </div>
        </div>
    </div> --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sellForm = document.querySelector('.item-details-form');
            const submitButton = document.getElementById('submitButton');

            sellForm.addEventListener('submit', function(event) {
                event.preventDefault();

                // Get form values
                const firstName = document.getElementById('first_name').value.trim();
                const lastName = document.getElementById('last_name').value.trim();
                const phoneNumber = document.getElementById('phone_number').value.trim();
                const email = document.getElementById('email').value.trim();

                // Validation checks
                if (!firstName || !lastName) {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'يرجى ملء الاسم الأول والأخير'
                    });
                    return;
                }

                if (!phoneNumber && !email) {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'يرجى إدخال رقم الهاتف أو البريد الإلكتروني'
                    });
                    return;
                }

                // Disable button immediately
                submitButton.disabled = true;
                const originalText = submitButton.innerHTML;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> جاري المعالجة...';

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
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                localStorage.removeItem('selectedItems');
                                window.location.href = '{{ route('gold-items.shop') }}';
                            });
                        } else {
                            throw new Error(data.message || 'فشل في إرسال الطلب');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: error.message ||
                                'حدث خطأ أثناء إرسال الطلب. يرجى المحاولة مرة أخرى.',
                        });
                        // Re-enable button on error
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalText;
                    });
            });

            const phoneNumberInput = document.getElementById('phone_number');

            phoneNumberInput.addEventListener('blur', function() {
                const phoneNumber = phoneNumberInput.value.trim();
                if (phoneNumber) {
                    fetch(`/get-customer-data?phone_number=${phoneNumber}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Fill in customer details
                                document.getElementById('first_name').value = data.customer.first_name;
                                document.getElementById('last_name').value = data.customer.last_name;
                                document.getElementById('address').value = data.customer.address;
                                document.getElementById('email').value = data.customer.email;
                                document.querySelector('select[name="payment_method"]').value = data.customer.payment_method;

                                // Show returning customer alert if customer has purchase history
                                const returningCustomerAlert = document.getElementById('returningCustomerAlert');
                                if (data.isReturningCustomer) {
                                    returningCustomerAlert.classList.remove('d-none');
                                    
                                    // Populate purchase history table
                                    const tableBody = document.getElementById('purchaseHistoryTableBody');
                                    tableBody.innerHTML = ''; // Clear existing content
                                    
                                    data.purchaseHistory.forEach(sale => {
                                        const row = document.createElement('tr');
                                        const statusText = {
                                            'approved': 'تم البيع',
                                            'pending': 'قيد الانتظار',
                                            'rejected': 'مرفوض'
                                        }[sale.status] || sale.status;
                                        
                                        const itemType = sale.item_type === 'pound' ? 'جنيه ذهب' : 'قطعة ذهب';
                                        
                                        row.innerHTML = `
                                            <td>${new Date(sale.created_at).toLocaleDateString('ar-EG')}</td>
                                            <td><span class="badge ${sale.status_badge}">${statusText}</span></td>
                                            <td>${itemType}</td>
                                            <td>${sale.item_serial_number}</td>
                                            <td>${sale.kind}</td>
                                            <td>${sale.weight}</td>
                                            <td>${sale.price}</td>
                                            <td>${sale.payment_method}</td>
                                        `;
                                        tableBody.appendChild(row);
                                    });
                                } else {
                                    returningCustomerAlert.classList.add('d-none');
                                }
                            }
                        })
                        .catch(error => console.error('Error fetching customer data:', error));
                }
            });
        });
    </script>
    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sellForm = document.querySelector('.item-details-form');

            sellForm.addEventListener('submit', async function(event) {
                event.preventDefault();
                console.log('Form submitted');

                // Get the selected item ID from the hidden input
                const itemId = document.querySelector('input[name="ids[]"]').value;
                console.log('Item ID:', itemId);

                try {
                    console.log('Checking for associated pounds...');
                    const response = await fetch('/check-associated-pounds', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ 
                            ids: [itemId]
                        })
                    });

                    const data = await response.json();
                    console.log('Response from check-associated-pounds:', data);
                    
                    if (data.hasPound) {
                        console.log('Pound found, showing modal');
                        document.getElementById('poundSerialNumber').value = data.poundDetails.serial_number;
                        
                        // Populate pound details
                        const detailsHtml = `
                            <div class="mb-3">
                                <h6>تفاصيل الجنيه:</h6>
                                <p>الرقم التسلسلي: ${data.poundDetails.serial_number}</p>
                                <p>النوع: ${data.poundDetails.kind}</p>
                                <p>الوزن: ${data.poundDetails.weight}g</p>
                                <p>العيار: ${data.poundDetails.purity}K</p>
                            </div>
                        `;
                        document.getElementById('poundDetails').innerHTML = detailsHtml;
                        
                        // Show the modal
                        const poundModal = new bootstrap.Modal(document.getElementById('poundPriceModal'));
                        poundModal.show();

                        // Handle pound price submission
                        document.getElementById('submitPoundPrice').onclick = async function() {
                            const poundPrice = document.getElementById('poundPrice').value;
                            if (!poundPrice) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Please enter the pound price',
                                });
                                return;
                            }

                            try {
                                // First submit the pound sale request
                                const poundResponse = await fetch('{{ route("gold-pounds.create-sale-request") }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        serial_numbers: [data.poundDetails.serial_number],
                                        prices: {
                                            [data.poundDetails.serial_number]: poundPrice
                                        },
                                        payment_method: document.querySelector('select[name="payment_method"]').value,
                                        first_name: document.querySelector('input[name="first_name"]').value,
                                        last_name: document.querySelector('input[name="last_name"]').value,
                                        phone_number: document.querySelector('input[name="phone_number"]').value,
                                        address: document.querySelector('input[name="address"]').value,
                                        email: document.querySelector('input[name="email"]').value
                                    })
                                });

                                const poundResult = await poundResponse.json();
                                if (!poundResult.success) {
                                    throw new Error(poundResult.message || 'Failed to submit pound sale request');
                                }

                                // Then submit the item sale request
                                poundModal.hide();
                                submitFormWithData(new FormData(sellForm));

                            } catch (error) {
                                console.error('Error submitting pound sale:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: error.message || 'Failed to submit pound sale request',
                                });
                            }
                        };
                    } else {
                        console.log('No pound found, submitting form normally');
                        submitFormWithData(new FormData(sellForm));
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to check for associated pounds. Please try again.',
                    });
                }
            });

            function submitFormWithData(formData) {
                console.log('Submitting form with data:', Object.fromEntries(formData));
                fetch(sellForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Form submission response:', data);
                    if (data.success) {
                        showSuccessAndRedirect();
                    } else {
                        throw new Error(data.message || 'Failed to submit the form');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'Failed to submit the form. Please try again.',
                    });
                });
            }

            function showSuccessAndRedirect() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Sale request submitted successfully.',
                }).then(() => {
                    localStorage.removeItem('selectedItems');
                    window.location.href = '{{ route('gold-items.shop') }}';
                });
            }
        });
    </script> --}}
</body>

</html>
