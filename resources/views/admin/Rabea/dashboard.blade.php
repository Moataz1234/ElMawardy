
<x-app-layout>
    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

</x-app-layout>
<head>
    <link href="{{ asset('css/navbar.css') }}" rel="stylesheet">

</head>
<body>
    <form method="GET" action="{{ route('orders.rabea.index') }}">
        <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}">
        <button type="submit">Search</button>

    </form>
    
    <nav>   
        <ul>
        
    </ul>
</nav>
</body>
