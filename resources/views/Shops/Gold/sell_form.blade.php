<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بيع قطعة</title>
    <link href="{{ url('css/style.css') }}" rel="stylesheet">
    <link href="{{ url('css/sell_form.css') }}" rel="stylesheet">
</head>

<body>
    <div class="container">
        <!-- Customer Details Section -->
        <div class="customer-details-container">
            <h2 class="section-heading" style="color: rgb(171, 245, 0)">بيانات الزبون</h2>
            <form class="item-details-form" action="{{ route('shop-items.bulkSell') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="first_name">الاسم الاول</label>
                    <input class="form-control" type="text" name="first_name" id="first_name" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="last_name">الاسم الاخير</label>
                    <input class="form-control" type="text" name="last_name" id="last_name" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="phone_number">رقم التليفون</label>
                    <input class="form-control" type="number" name="phone_number" id="phone_number">
                </div>
                <div class="form-group">
                    <label class="form-label" for="address">العنوان</label>
                    <input class="form-control" type="text" name="address" id="address">
                </div>
                <div class="form-group">
                    <label class="form-label" for="email">Email:</label>
                    <input class="form-control" type="email" name="email" id="email">
                </div>
                <div class="form-group">
                    <label class="form-label" for="payment_method">طريقة الدفع</label>
                    <select class="form-control" name="payment_method" id="payment_method">
                        <option value="cash">Cash</option>
                        <option value="visa">Visa</option>
                        <option value="value">Value</option>
                        <option value="mogo">Mogo</option>
                        <option value="instapay">Contact</option>
                    </select>
                </div>

                <!-- Item Details Section -->
                <div class="item-details-container">
                    <h2 class="section-heading">تفاصيل القطع</h2>
                    @foreach ($goldItems as $item)
                        <div class="item-details-card">
                            <h3 class="item-title">Item {{ $item->serial_number }}</h3>
                            <input type="hidden" name="ids[]" value="{{ $item->id }}">
                            <div class="item-detail">
                                <span class="label">Shop Name:</span>
                                <span class="value">{{ $item->shop_name }}</span>
                            </div>
                            <div class="item-detail">
                                <span class="label">Shop ID:</span>
                                <span class="value">{{ $item->shop_id }}</span>
                            </div>
                            <div class="item-detail">
                                <span class="label">Kind:</span>
                                <span class="value">{{ $item->kind }}</span>
                            </div>
                            <div class="item-detail">
                                <span class="label">Model:</span>
                                <span class="value">{{ $item->model }}</span>
                            </div>
                            <div class="item-detail">
                                <span class="label">Gold Color:</span>
                                <span class="value">{{ $item->gold_color }}</span>
                            </div>
                            <div class="item-detail">
                                <span class="label">Weight:</span>
                                <span class="value">{{ $item->weight }}</span>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="price_{{ $item->id }}">
                                    السعر للقطعة ({{ $item->serial_number }})
                                </label>
                                <input class="form-control" type="number" name="prices[{{ $item->id }}]"
                                    step="0.01" id="price_{{ $item->id }}" required>
                            </div>
                        </div>
                    @endforeach
                    <button class="form-button"  type="submit">Complete Sale</button>

                </div>

            </form>
        </div>
    </div>
</body>
<script>
    document.getElementById('price').addEventListener('input', function() {
        var weight = parseFloat(document.getElementById('weight').value);
        var price = parseFloat(this.value);
        var totalPrice = weight * price;
        document.getElementById('total_price').value = totalPrice.toFixed(2);
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sellForm = document.querySelector('.item-details-form');

        sellForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent the default form submission

            // Submit the form using fetch
            fetch(sellForm.action, {
                method: 'POST',
                body: new FormData(sellForm),
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // Add CSRF token for Laravel
                }
            })
            .then(response => {
                if (response.ok) {
                    // Clear local storage after successful submission
                    localStorage.removeItem('selectedItems');
                    // Redirect or show a success message
                    window.location.href = '{{ route("gold-items.shop") }}'; // Redirect to the shop page
                } else {
                    // Handle errors
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to submit the form. Please try again.',
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while submitting the form.',
                });
            });
        });
    });
</script>

</html>
