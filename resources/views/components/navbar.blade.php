<nav>
    <ul>
        <li><a href="{{ route('gold-items.shop', ['shop' => Auth::user()->name]) }}">Shop Items</a></li>
        <li><a href="{{ route('transfer.requests') }}">Transfer Requests</a></li>
        <li><a href="{{ route('transfer.requests.history') }}">Transfer Request History</a></li>
        <li><a href="{{ route('gold-items.index') }}">Available Items</a></li>
        <li><a href="{{ route('gold-pounds.index') }}">Gold Pounds</a></li>
        <li><a href="{{ route('gold-items.create') }}">Add Gold Item</a></li>
    </ul>
</nav>
