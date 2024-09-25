<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Gold Item</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ url('css/style.css') }}" rel="stylesheet">
</head>
<body>
    <h2>Transfer to Another Branch</h2>
    <p><strong>Serial Number:</strong> {{ $goldItem->serial_number }}</p>
    <p><strong>Current Shop:</strong> {{ $goldItem->shop_name }}</p>
    <form class="custom-form" action="{{ route('gold-items.transfer', $goldItem->id) }}" method="POST">
        @csrf
        <label for="shop_id">Shop:</label>
        <select name="shop_id" id="shop_id" required>
            @foreach($shops as $shop)
                <option value="{{ $shop->id }}">{{ $shop->name }}</option>
            @endforeach
        </select><br>
        <button type="submit">Transfer</button>
    </form>
</body>
</html>
