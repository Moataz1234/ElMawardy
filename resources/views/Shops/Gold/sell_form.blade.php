<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بيع قطعة</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ url('css/style.css') }}" rel="stylesheet">
    <style>
        /* Base Styles */
body {
  background-color: #003A70;
  color: white;
  font-family: Arial, sans-serif;
  margin: 0;
  padding: 0;
}

/* Container */
.container {
  max-width: 900px;
  margin: 0 auto;
  padding: 40px 20px;
}

/* Item Details Styles */
.item-details-container {
  background-color: #0D2544;
  border-radius: 8px;
  padding: 30px;
  margin-bottom: 30px;
}

.section-heading {
  font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
  font-weight: bold;
  margin-top: 0;
  margin-bottom: 20px;
}

.item-details-form {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  grid-gap: 20px;
}

.item-details-card {
  background-color: #0D2544;
  border-radius: 8px;
  padding: 20px;
}

.item-title {
  font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
  font-weight: bold;
  margin-top: 0;
  margin-bottom: 15px;
}

.item-detail {
  display: flex;
  justify-content: space-between;
  margin-bottom: 10px;
}

.label {
  font-weight: bold;
}

/* Customer Details Styles */
.customer-details-container {
  background-color: #0D2544;
  border-radius: 8px;
  padding: 30px;
}

.customer-details-form {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  grid-gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-label {
  font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
  font-weight: bold;
  margin-bottom: 5px;
}

.form-control {
  background-color: #F2F2F2;
  border: none;
  padding: 10px;
  border-radius: 4px;
  font-size: 16px;
  color: #333;
}

.form-control:focus {
  outline: none;
  box-shadow: 0 0 5px rgba(255, 255, 255, 0.5);
}

.form-button {
  background-color: #5CB85C;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 4px;
  font-size: 16px;
  cursor: pointer;
  justify-self: end;
}
    </style>
</head>
<body>
    <div class="container">
        <div class="item-details-container">
            <h2 class="section-heading">تفاصيل القطعة</h2>
            <form class="item-details-form" action="{{ route('shop-items.bulkSell') }}" method="POST">
                @csrf
                @foreach($goldItems as $item)
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
                </div>
                @endforeach
        </div>

        <div class="customer-details-container">
            <h2 class="section-heading" style="color: rgb(171, 245, 0)">بيانات الزبون</h2>
      
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
                    <input class="form-control" type="number" name="phone_number" id="phone_number" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="address">العنوان</label>
                    <input class="form-control" type="text" name="address" id="address" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="email">Email:</label>
                    <input class="form-control" type="email" name="email" id="email" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="payment_method">طريقة الدفع</label>
                    <select class="form-control" name="payment_method" id="payment_method">
                        <option value="visa">Visa</option>
                        <option value="value">Value</option>
                        <option value="cash">Cash</option>
                        <option value="instapay">Instapay</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="total_price">السعر</label>
                    <input class="form-control" type="number" name="total_price" step="0.01" id="total_price">
                </div>
                <button class="form-button" type="submit">Complete Sale</button>
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
</html>  