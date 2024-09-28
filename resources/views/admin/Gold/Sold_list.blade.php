<!-- resources/views/images/index.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    {{-- @include("GoldCatalog.Shared.adminNavBar")
    @include("GoldCatalog.Shared.sideBar") --}}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalog Items</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/pagination.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    </head>
<body>
    <form method="GET" action="{{ route('gold-items.sold') }}">
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
            <li><a href="{{ route('gold-items.shop', ['shop' => Auth::user()->name]) }}">Shop Items</a></li>
            <li><a href="{{ route('transfer.requests') }}">Transfer Requests</a></li>
            <li><a href="{{ route('gold-items.sold') }}">Sold Items</a></li>
 
         </ul>
    </nav>
    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th>
                    <div class="sort-container">
                        Serial Number
                        <form method="GET" action="{{ route('gold-items.sold') }}" style="display:inline;">
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
                        <form method="GET" action="{{ route('gold-items.sold') }}" style="display:inline;">
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
                        <form method="GET" action="{{ route('gold-items.sold') }}" style="display:inline;">
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
                        <form method="GET" action="{{ route('gold-items.sold') }}" style="display:inline;">
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
                        <form method="GET" action="{{ route('gold-items.sold') }}" style="display:inline;">
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
                        <form method="GET" action="{{ route('gold-items.sold') }}" style="display:inline;">
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
                        <form method="GET" action="{{ route('gold-items.sold') }}" style="display:inline;">
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
                        <form method="GET" action="{{ route('gold-items.sold') }}" style="display:inline;">
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
                        <form method="GET" action="{{ route('gold-items.sold') }}" style="display:inline;">
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
                        <form method="GET" action="{{ route('gold-items.sold') }}" style="display:inline;">
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
                        <form method="GET" action="{{ route('gold-items.sold') }}" style="display:inline;">
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
                        <form method="GET" action="{{ route('gold-items.sold') }}" style="display:inline;">
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
                        <form method="GET" action="{{ route('gold-items.sold') }}" style="display:inline;">
                            <input type="hidden" name="sort" value="net_weight">
                            <input type="hidden" name="direction" value="{{ request('direction') === 'asc' ? 'desc' : 'asc' }}">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <button type="submit">&#8597;</button>
                        </form>
                    </div>
                </th>
                
                <th>
                    <div class="sort-container">
                        Sold Date
                        <form method="GET" action="{{ route('gold-items.sold') }}" style="display:inline;">
                            <input type="hidden" name="sort" value="sold_date">
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
                    <td><img src="{{ asset($item->link) }}" alt="Image" width="50"></td>
                    <td>{{ $item->serial_number }}</td>
                    <td>{{ $item->shop_name }}</td>
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
                    <td>{{ $item->sold_date }}</td>

                    <td>
                        <a class="edit_button" href="{{ route('gold-items-sold.edit', $item->id) }}" >Edit</a>
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
