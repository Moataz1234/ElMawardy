<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Request History</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/pagination.css') }}" rel="stylesheet">
</head>
<body>
    <nav>
        <ul>
            <li><a href="{{ route('gold-items.shop', ['shop' => Auth::user()->name]) }}">Shop Items</a></li>
            <li><a href="{{ route('transfer.requests') }}">Transfer Requests</a></li>
            <li><a href="{{ route('transfer.requests.history') }}">Transfer Request History</a></li>
            <li><a href="{{ route('gold-items.index') }}">Available Items</a></li>
            <li><a href="{{ route('gold-pounds.index') }}">Gold Pounds</a></li>
        </ul>
    </nav>

    @foreach($transferRequests as $request)
        <p><strong>Gold Item:</strong> {{ $request->goldItem->serial_number }}</p>
        <p><strong>From Shop:</strong> {{ $request->fromShop->name }}</p>
        <p><strong>To Shop:</strong> {{ $request->toShop->name }}</p>
        <p><strong>Status:</strong> {{ $request->status }}</p>
        <p><strong>Requested At:</strong> {{ $request->created_at }}</p>
        <p><strong>Updated At:</strong> {{ $request->updated_at }}</p>
        <hr>
    @endforeach
</body>
</html>
