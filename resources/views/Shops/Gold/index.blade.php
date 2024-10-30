
<!DOCTYPE html>
<html lang="en">
<head>
    @include('dashboard')

   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Your Shop's Items</title>
   <link href="{{ asset('css/app.css') }}" rel="stylesheet">
   <link href="{{ asset('css/style.css') }}" rel="stylesheet">
   <link href="{{ asset('css/pagination.css') }}" rel="stylesheet">
   <link href="{{ asset('css/navbar.css') }}" rel="stylesheet">

</head>
<body>
      <!-- Button to toggle the price table -->
      <button id="togglePriceTable" class="prices" >Show Updates</button>

      <!-- Hidden price table section -->
      <div id="priceTable" style="display: none;">
          <table>
              <tbody>
                  @foreach ($latestPrices as $price)
                      <tr>
                          <td>{{ $price->gold_buy }}/{{ $price->gold_sell }}</td>
                          <td>{{ $price->gold_with_work }}</td>
                          <td>{{ $price->dollar_price }}</td>
                          <td>{{ $price->percent }}</td>
                          <td>{{ $price->gold_in_diamond }}</td>
                          <td>{{ $price->shoghl_agnaby }}</td>
                      </tr>
                  @endforeach
              </tbody>
          </table>
      </div>
  
       <table>
           <thead>
               <tr>
                   <th>Image</th>
                   <th>
                       <div class="sort-container">
                           Serial Number
                           <form method="GET" action="{{ route('gold-items.shop', ['shop' => Auth::user()->name]) }}" style="display:inline;">
                            <input type="hidden" name="sort" value="serial_number">
                               <input type="hidden" name="direction" value="{{ request('direction') === 'asc' ? 'desc' : 'asc' }}">
                               <input type="hidden" name="search" value="{{ request('search') }}">
                               <button type="submit">&#8597;</button>
                           </form>
                       </div>
                   </th>
                   <th>
                       <div class="sort-container">
                           Shop Name
                           <form method="GET" action="{{ route('gold-items.shop', ['shop' => Auth::user()->name]) }}" style="display:inline;">
                            <input type="hidden" name="sort" value="shop_name">
                               <input type="hidden" name="direction" value="{{ request('direction') === 'asc' ? 'desc' : 'asc' }}">
                               <input type="hidden" name="search" value="{{ request('search') }}">
                               <button type="submit">&#8597;</button>
                           </form>
                       </div>
                   </th>
                   <th>
                       <div class="sort-container">
                           Kind
                           <form method="GET" action="{{ route('gold-items.shop', ['shop' => Auth::user()->name]) }}" style="display:inline;">
                            <input type="hidden" name="sort" value="kind">
                               <input type="hidden" name="direction" value="{{ request('direction') === 'asc' ? 'desc' : 'asc' }}">
                               <input type="hidden" name="search" value="{{ request('search') }}">
                               <button type="submit">&#8597;</button>
                           </form>
                       </div>
                   </th>
                   <th>
                       <div class="sort-container">
                           Model
                           <form method="GET" action="{{ route('gold-items.shop', ['shop' => Auth::user()->name]) }}" style="display:inline;">
                            <input type="hidden" name="sort" value="model">
                               <input type="hidden" name="direction" value="{{ request('direction') === 'asc' ? 'desc' : 'asc' }}">
                               <input type="hidden" name="search" value="{{ request('search') }}">
                               <button type="submit">&#8597;</button>
                           </form>
                       </div>
                   </th>
                   <th>
                       <div class="sort-container">
                           Gold Color
                           <form method="GET" action="{{ route('gold-items.shop', ['shop' => Auth::user()->name]) }}" style="display:inline;">
                            <input type="hidden" name="sort" value="gold_color">
                               <input type="hidden" name="direction" value="{{ request('direction') === 'asc' ? 'desc' : 'asc' }}">
                               <input type="hidden" name="search" value="{{ request('search') }}">
                               <button type="submit">&#8597;</button>
                           </form>
                       </div>
                   </th>
                   <th>
                       <div class="sort-container">
                           Stones
                           <form method="GET" action="{{ route('gold-items.shop', ['shop' => Auth::user()->name]) }}" style="display:inline;">
                               <input type="hidden" name="sort" value="stones">
                               <input type="hidden" name="direction" value="{{ request('direction') === 'asc' ? 'desc' : 'asc' }}">
                               <input type="hidden" name="search" value="{{ request('search') }}">
                               <button type="submit">&#8597;</button>
                           </form>
                       </div>
                   </th>
                   <th>
                       <div class="sort-container">
                           Metal Type
                           <form method="GET" action="{{ route('gold-items.shop', ['shop' => Auth::user()->name]) }}" style="display:inline;">
                            <input type="hidden" name="sort" value="metal_type">
                               <input type="hidden" name="direction" value="{{ request('direction') === 'asc' ? 'desc' : 'asc' }}">
                               <input type="hidden" name="search" value="{{ request('search') }}">
                               <button type="submit">&#8597;</button>
                           </form>
                       </div>
                   </th>
                   <th>
                       <div class="sort-container">
                           Metal Purity
                           <form method="GET" action="{{ route('gold-items.shop', ['shop' => Auth::user()->name]) }}" style="display:inline;">
                            <input type="hidden" name="sort" value="metal_purity">
                               <input type="hidden" name="direction" value="{{ request('direction') === 'asc' ? 'desc' : 'asc' }}">
                               <input type="hidden" name="search" value="{{ request('search') }}">
                               <button type="submit">&#8597;</button>
                           </form>
                       </div>
                   </th>
                   <th>
                       <div class="sort-container">
                           Quantity
                           <form method="GET" action="{{ route('gold-items.shop', ['shop' => Auth::user()->name]) }}" style="display:inline;">
                            <input type="hidden" name="sort" value="quantity">
                               <input type="hidden" name="direction" value="{{ request('direction') === 'asc' ? 'desc' : 'asc' }}">
                               <input type="hidden" name="search" value="{{ request('search') }}">
                               <button type="submit">&#8597;</button>
                           </form>
                       </div>
                   </th>
                   <th>
                       <div class="sort-container">
                           Weight
                           <form method="GET" action="{{ route('gold-items.shop', ['shop' => Auth::user()->name]) }}" style="display:inline;">
                            <input type="hidden" name="sort" value="weight">
                               <input type="hidden" name="direction" value="{{ request('direction') === 'asc' ? 'desc' : 'asc' }}">
                               <input type="hidden" name="search" value="{{ request('search') }}">
                               <button type="submit">&#8597;</button>
                           </form>
                       </div>
                   </th>
                   <th>
                       <div class="sort-container">
                           Source
                           <form method="GET" action="{{ route('gold-items.shop', ['shop' => Auth::user()->name]) }}" style="display:inline;">
                            <input type="hidden" name="sort" value="source">
                               <input type="hidden" name="direction" value="{{ request('direction') === 'asc' ? 'desc' : 'asc' }}">
                               <input type="hidden" name="search" value="{{ request('search') }}">
                               <button type="submit">&#8597;</button>
                           </form>
                       </div>
                   </th>
                   <th>
                       <div class="sort-container">
                           Average of Stones
                           <form method="GET" action="{{ route('gold-items.shop', ['shop' => Auth::user()->name]) }}" style="display:inline;">
                            <input type="hidden" name="sort" value="average_of_stones">
                               <input type="hidden" name="direction" value="{{ request('direction') === 'asc' ? 'desc' : 'asc' }}">
                               <input type="hidden" name="search" value="{{ request('search') }}">
                               <button type="submit">&#8597;</button>
                           </form>
                       </div>
                   </th>
                   <th>
                       <div class="sort-container">
                           Net Weight
                           <form method="GET" action="{{ route('gold-items.shop', ['shop' => Auth::user()->name]) }}" style="display:inline;">
                            <input type="hidden" name="sort" value="net_weight">
                               <input type="hidden" name="direction" value="{{ request('direction') === 'asc' ? 'desc' : 'asc' }}">
                               <input type="hidden" name="search" value="{{ request('search') }}">
                               <button type="submit">&#8597;</button>
                           </form>
                       </div>
                   </th>
                   <th>Actions</th>
               </tr>
           </thead>
       <tbody>
           @foreach ($goldItems as $item)
           @php
           $isOuter = \App\Models\Outer::where('gold_serial_number', $item->serial_number)
                                       ->where('is_returned', false)
                                       ->exists();
            @endphp
        <tr style="{{ $isOuter ? 'background-color: yellow;' : '' }}">

                <td>
                       @if($item->link)
                           <img src="{{ asset('storage/' . $item->link) }}" alt="Image" width="50">
                       @else
                           No Image
                       @endif
                   </td>
                   <td>{{ $item->serial_number }}</td>
                   <td>{{ $item->shop->name }}</td>
                   <td>{{ $item->kind }}</td>
                   <td>{{ $item->model }}</td>
                   <td>{{ $item->gold_color }}</td>
                   <td>{{ $item->stones }}</td>
                   <td>{{ $item->metal_type }}</td>
                   <td>{{ $item->metal_purity }}</td>
                   <td>{{ $item->quantity }}</td>
                   <td>{{ $item->weight }}</td>
                   <td>{{ $item->source }}</td>
                   <td>{{ $item->average_of_stones }}</td>
                   <td>{{ $item->net_weight }}</td>
                   <td>
                    <a class="action_button" href="{{ route('shop-items.edit', $item->id) }}" 
                        {{ $isOuter ? 'style=pointer-events:none;opacity:0.5;' : '' }}>
                        Sell
                     </a>
                     <a class="action_button" href="{{ route('gold-items.transferForm', $item->id) }}" 
                        {{ $isOuter ? 'style=pointer-events:none;opacity:0.5;' : '' }}>
                        Transfer
                     </a>
                     @include('Shops.Gold.outerForm') <!-- Include the form view -->

                    </div>
                    @if ($isOuter)
                    <form action="{{ route('gold-items.returnOuter', $item->serial_number) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="returned-btn">Returned</button>
                    </form>
                @else
                <form action="{{ route('gold-items.toggleReturn', $item->serial_number) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="outer-btn">
                        Outer
                    </button>
                </form>
                @endif
            
                   </td>
               </tr>
           @endforeach
       </tbody>
   </table>

   {{ $goldItems->links('pagination::bootstrap-4') }}

   <script>
    function openOuterForm(serialNumber) {
    document.getElementById('gold_serial_number').value = serialNumber;
    document.getElementById('outerFormModal').style.display = 'block';
}
function closeOuterForm() {
    document.getElementById('outerFormModal').style.display = 'none';
}
function confirmReturn() {
    // Optional: Add confirmation dialog if you want to confirm the return action
    if (confirm('Are you sure you want to mark this item as returned?')) {
        document.querySelector('#returnOuterForm').submit(); // Submit the return form
    }
}
    // JavaScript to toggle the visibility of the price table
    document.getElementById('togglePriceTable').addEventListener('click', function() {
        const priceTable = document.getElementById('priceTable');
        if (priceTable.style.display === 'none') {
            priceTable.style.display = 'block';
            this.innerText = 'Hide Prices';
        } else {
            priceTable.style.display = 'none';
            this.innerText = 'Show Prices';
        }
    });
</script>
</body>
</html>
