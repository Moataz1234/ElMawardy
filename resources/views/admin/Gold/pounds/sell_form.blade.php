<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بيع سبيكة</title>
    <link href="{{ url('css/style.css') }}" rel="stylesheet">
    <link href="{{ url('css/sell_form.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @include('components.navbar')
</head>

<body>
    <div class="container text-white">
        <!-- Customer Details Section -->
        <div class="customer-details-container">
            <h2 class="section-heading" style="color: rgb(171, 245, 0)">بيانات الزبون</h2>
            <form class="item-details-form d-flex flex-column gap-3" action="{{ route('gold-pounds.create-sale-request') }}" method="POST">
                @csrf
                <div style="direction: rtl;">
                    <div class="d-flex gap-3">
                        <div class="form-group w-50">
                            <label class="form-label" for="first_name">الاسم الاول</label>
                            <input class="form-control" type="text" name="first_name" id="first_name" required>
                    </div>
                    <div class="form-group w-50">
                        <label class="form-label" for="last_name">الاسم الاخير</label>
                        <input class="form-control" type="text" name="last_name" id="last_name" required>
                    </div>
                    </div>
                    <div class="d-flex gap-3">
                    <div class="form-group w-50">
                        <label class="form-label" for="phone_number">رقم التليفون</label>
                        <input class="form-control" type="number" name="phone_number" id="phone_number">
                    </div>
                    <div class="form-group w-50">
                        <label class="form-label" for="address">العنوان</label>
                        <input class="form-control" type="text" name="address" id="address">
                    </div>
                    </div>
                    <div class="d-flex gap-3">
                    <div class="form-group w-50">
                        <label class="form-label" for="email">Email:</label>
                        <input class="form-control" type="email" name="email" id="email">
                    </div>
                    <div class="form-group w-50">
                        <label class="form-label" for="payment_method">طريقة الدفع</label>
                        <select class="form-control" name="payment_method" id="payment_method">
                            <option value="cash">Cash</option>
                            <option value="visa">Visa</option>
                            <option value="value">Value</option>
                            <option value="mogo">Mogo</option>
                            <option value="instapay">Contact</option>
                        </select>
                    </div>
                    </div>
                </div>
                <!-- Pounds Details Section -->
                <div class="item-details-container">
                    @foreach ($pounds as $pound)
                    <div class="item-details-card d-flex flex-row gap-5 w-100">
                        <div>
                            <h3 class="item-title">Pound {{ $pound->goldPound->kind }}</h3>
                            <input type="hidden" name="serial_numbers[]" value="{{ $pound->serial_number }}">

                            <div class="item-detail">
                                <span class="label">Serial Number:</span>
                                <span class="value">{{ $pound->serial_number }}</span>
                            </div>
                            <div class="item-detail">
                                <span class="label">Type:</span>
                                <span
                                    class="value">{{ $pound->goldPound ? ucfirst(str_replace('_', ' ', $pound->goldPound->kind)) : 'N/A' }}</span>
                            </div>
                            <div class="item-detail">
                                <span class="label">Weight:</span>
                                <span class="value">{{ $pound->goldPound ? $pound->goldPound->weight : 'N/A' }}g</span>
                            </div>
                            <div class="item-detail">
                                <span class="label">Purity:</span>
                                <span class="value">{{ $pound->goldPound ? $pound->goldPound->purity : 'N/A' }}K</span>
                            </div>
                            <div class="item-detail">
                                <span class="label">Linked Item:</span>
                                <span class="value">{{ $pound->goldItem ? 'Yes' : 'No' }}</span>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="price_{{ $pound->serial_number }}">
                                    السعر للقطعة ({{ $pound->serial_number }})
                                </label>
                                <input class="form-control" type="number" name="prices[{{ $pound->serial_number }}]"
                                    step="0.01" id="price_{{ $pound->serial_number }}" required>
                            </div>
                        </div>
                        <div>
                            <h2 class="section-heading" style="color: rgb(171, 245, 0)">تفاصيل القطعة</h2>
                            
                            <p>Serial Number: {{ $pound->serial_number }}</p>
                            <p>Weight: {{ $pound->goldItem->weight ?? 'N/A' }}</p>
                            <p>Model: {{ $pound->goldItem->model ?? 'N/A' }}</p>
                            <p>Gold Color: {{ $pound->goldItem->gold_color ?? 'N/A' }}</p>
                            <p>Gold Purity: {{ $pound->goldItem->metal_purity ?? 'N/A' }}</p>
                            <p>Stars: {{ $pound->goldItem->modelCategory->stars ?? 'N/A' }}</p>
                        </div>
                    </div>
                    @endforeach

                    <button class="form-button" type="submit">Complete Sale</button>

                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sellForm = document.querySelector('.item-details-form');

            sellForm.addEventListener('submit', function(event) {
                event.preventDefault();

                const formData = new FormData(sellForm);

                fetch(sellForm.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Sale request submitted successfully');
                            window.location.href = '{{ route('gold-pounds.index') }}';
                        } else {
                            alert(data.message || 'Failed to submit the form. Please try again.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while submitting the form.');
                    });
            });
        });
    </script>
</body>

</html>
