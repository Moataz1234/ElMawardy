<!-- resources/views/images/index.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    {{-- @include("GoldCatalog.Shared.adminNavBar")
    @include("GoldCatalog.Shared.sideBar") --}}
    @include('dashboard')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalog Items</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/pagination.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    </head>
<body>
    <table>
        <thead>
            <tr>
                {{-- <form method="GET" action="{{ route('gold-items-sold.index') }}">
                    <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}">
                    <button type="submit">Search</button>
            
                </form> --}}
                <th>Image</th>
                @php
                // Array of columns with their display names
                $columns = [
                    'serial_number' => 'Serial Number',
                    'shop_name' => 'Shop Name',
                    'kind' => 'Kind',
                    'model' => 'Model',
                    'gold_color' => 'Gold Color',
                    'stones' => 'Stones',
                    'metal_type' => 'Metal Type',
                    'metal_purity' => 'Metal Purity',
                    'quantity' => 'Quantity',
                    'weight' => 'Weight',
                    'source' => 'Source',
                    'average_of_stones' => 'Average of Stones',
                    'net_weight' => 'Net Weight',
                    'sold_date' => 'sold_date',
                ];
            @endphp

            @foreach ($columns as $field => $label)
                <th>
                    <div class="sort-container">
                        {{ $label }}
                        <form method="GET" action="{{ route('gold-items.sold') }}" style="display:inline;">
                            <input type="hidden" name="sort" value="{{ $field }}">
                            <input type="hidden" name="direction" value="{{ request('direction') === 'asc' ? 'desc' : 'asc' }}">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <button type="submit">&#8597;</button>
                        </form>
                    </div>  
                    @endforeach
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
