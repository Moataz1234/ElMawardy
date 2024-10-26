    
<head>
    @include('dashboard')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Transfer Requests</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
   <link href="{{ asset('css/style.css') }}" rel="stylesheet">
   <link href="{{ asset('css/navbar.css') }}" rel="stylesheet">
   <link href="{{ asset('css/style.css') }}" rel="stylesheet">
   <link href="{{ asset('css/pagination.css') }}" rel="stylesheet">
</head>
<body>
    @foreach($transferRequests as $request)
        <p><strong>Gold Item:</strong> {{ $request->goldItem->serial_number }}</p>
        <p><strong>From Shop:</strong> {{ $request->fromShop->name }}</p>
        <p><strong>To Shop:</strong> {{ $request->toShop->name }}</p>
        <p><strong>Status:</strong> {{ $request->status }}</p>

        @if($request->status == 'pending')
            <a href="{{ route('transfer.handle', ['id' => $request->id, 'status' => 'accepted']) }}">Accept</a>
            <a href="{{ route('transfer.handle', ['id' => $request->id, 'status' => 'rejected']) }}">Reject</a>
        @endif

        <hr>
    @endforeach
</body>
</html>
