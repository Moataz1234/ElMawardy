<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Gold Item</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ url('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/navbar.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body>
    <form class="custom-form" action="{{ route('gold-items.transfer', $goldItem->id) }}" method="POST">
        {{-- <nav>
            <ul>
                <li><a href="{{ route('gold-items.shop', ['shop' => Auth::user()->name]) }}">Shop Items</a></li>
                <li><a href="{{ route('transfer.requests') }}">Transfer Requests</a></li>
                <li><a href="{{ route('transfer.requests.history') }}">Transfer Request History</a></li>
                <li><a href="{{ route('gold-items.index') }}">Available Items</a></li>
                <li><a href="{{ route('gold-pounds.index') }}">Gold Pounds</a></li>
                <li><a href="{{ route('gold-items.sold') }}">Sold Items</a></li>

            </ul>
        </nav> --}}
        @csrf
        <p><strong>Serial Number:</strong> {{ $goldItem->serial_number }}</p>
        <p><strong>Model :</strong> {{ $goldItem->model }}</p>
        <p><strong>Weight :</strong> {{ $goldItem->weight }}</p>

        <p><strong>Current Shop:</strong> {{ $goldItem->shop_name }}</p>
       
        <label for="shop_id">to Shop:</label>
        <select name="shop_id" id="shop_id" required>
            @foreach($shops as $shop)
                <option value="{{ $shop->id }}">{{ $shop->name }}</option>
            @endforeach
        </select><br>
        <button type="submit">Send Transfer Request</button>
    </form>
</body>
</html>
