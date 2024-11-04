
<nav>
    <div class="w3-bar">
        <div class="dropdown" href="#" class="w3-bar-item w3-button w3-hover-none w3-border-white w3-bottombar w3-hover-border-black">
<button class="w3-bar-item w3-button w3-hover-none w3-border-white w3-bottombar w3-hover-border-black">Inventory</button>
<div class="dropdown-content">
    <a href="{{ route('gold-items.index') }}">Gold Inventory</a>
    <a href="{{ route('gold-items.create') }}">Diamond Inventory</a>
    <a href="{{ route('gold-pounds.index') }}">Coins</a>
    <a href="{{ route('gold-items.create') }}">Bars</a>
    <a href="{{ route('gold-items.create') }}">Chains</a>
    <a href="{{ route('gold-items.index') }}">All Items</a>
</div>

        </div>
        <a  href="{{ route('gold-items.sold') }}" class="w3-bar-item w3-button w3-hover-none w3-border-white w3-bottombar w3-hover-border-black">Sold Items</a>
        <a href="{{ route('orders.create') }}" class="w3-bar-item w3-button w3-hover-none w3-border-white w3-bottombar w3-hover-border-black">Custom Order</a>
        <div class="dropdown" href="#" class="w3-bar-item w3-button w3-hover-none w3-border-white w3-bottombar w3-hover-border-black">
                
    
    <button class="w3-bar-item w3-button w3-hover-none w3-border-white w3-bottombar w3-hover-border-black">Orders</button>
    <div class="dropdown-content">
        <a href="{{ route('orders.index') }}">Orders List</a>
        <a href="{{ route('orders.history') }}">Orders History</a>

    </div>
    
      </div>
    <a href="{{ route('gold-catalog') }}" class="w3-bar-item w3-button w3-hover-none w3-border-white w3-bottombar w3-hover-border-black">Catalog</a>

        </div>
        
        </div>
        
</nav>