<x-app-layout>
    @if(session('error'))
   <div class="alert alert-danger">
       {{ session('error') }}
   </div>
@endif
</x-app-layout>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Your Shop's Items</title>
   <link href="{{ asset('css/app.css') }}" rel="stylesheet">
   <link href="{{ asset('css/style.css') }}" rel="stylesheet">
   <link href="{{ asset('css/pagination.css') }}" rel="stylesheet">
   <link href="{{ asset('css/navbar.css') }}" rel="stylesheet">
   <link href="{{ asset('css/style.css') }}" rel="stylesheet">

</head>
<body>
   <h1>Items for {{ Auth::user()->name }}</h1>
   <form method="GET" action="{{ route('gold-items.index') }}">
       <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}">
       <button type="submit">Search</button>
   </form>
   <nav>
       <ul>
           <li><a href="{{ route('gold-items.shop', ['shop' => Auth::user()->name]) }}">Shop Items</a></li>
           <li><a href="{{ route('transfer.requests') }}">Transfer Requests</a></li>
           <li><a href="{{ route('transfer.requests.history') }}">Transfer Request History</a></li>
           <li><a href="{{ route('gold-items.index') }}">Available Items</a></li>
           <li><a href="{{ route('gold-pounds.index') }}">Gold Pounds</a></li>
       </ul>
   </nav>
   <table>
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
               <tr>
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
                       <a class="action_button" href="{{ route('gold-items.edit', $item->id) }}" >Edit</a>
                       <a class="action_button" href="{{ route('gold-items.transferForm', $item->id) }}" >Transfer</a>

                   </td>
               </tr>
           @endforeach
       </tbody>
   </table>


   @php
       $paginationLinks =  $goldItems->links('pagination::bootstrap-4');
   @endphp
   {{$paginationLinks}}
</body>
</html>
