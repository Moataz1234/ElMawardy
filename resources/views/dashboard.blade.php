<head>
    <link href="{{ asset('css/navbar.css') }}" rel="stylesheet">
</head>
<body>
    <form method="GET" action="{{ route('gold-items.index') }}">
        <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}">
        <button type="submit">Search</button>

    </form>
    @include('components.navbar')

</body>
