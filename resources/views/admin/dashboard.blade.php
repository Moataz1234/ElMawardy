
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
    <form method="GET" action="{{ route('gold-items.index') }}">
        <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}">
        <button type="submit">Search</button>

    </form>
    <nav>   
    <ul>
     <li class="dropdown">
         <a href="#" class="dropbtn">Invetory</a>
         <div class="dropdown-content">
             <a href="{{ route('gold-items.create') }}">Gold Inventory</a>
             <a href="{{ route('gold-items.create') }}">Diamond Inventory</a>
             <a href="{{ route('gold-pounds.index') }}">Coins</a>
             <a href="{{ route('gold-items.create') }}">Bars</a>
             <a href="{{ route('gold-items.create') }}">Chains</a>
             <a href="{{ route('gold-items.index') }}">All Items</a>
         </div>
     </li>
        <li><a href="{{ route('gold-items.sold') }}">Sold Items</a></li>
        <li><a href="{{ route('transfer.requests.history') }}">transfer requests history</a></li>
        <li><a href="{{ route('gold-items.create') }}">create new item</a></li>
    </ul>
</nav>
</body>
