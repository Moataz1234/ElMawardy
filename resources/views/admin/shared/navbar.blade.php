
<link href="{{ asset('css/style.css') }}" rel="stylesheet">
<div class="topnav">
 {{-- <a class="active" href="#home">Home</a> --}}
 
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
</script>