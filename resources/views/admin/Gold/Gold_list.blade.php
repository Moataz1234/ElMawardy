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

            {{-- <link href="{{ asset('css/Gold/three_view.css') }}" rel="stylesheet"> --}}
            </head>
            <body>
                <form method="GET" action="{{ route('gold-items.index') }}">
                    <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}">
                    <button type="submit">Search</button>
                </form>
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th><a href="{{ route('gold-items.index', ['sort' => 'serial_number', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">Serial Number</a></th>
                            <th><a href="{{ route('gold-items.index', ['sort' => 'shop_name', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">Shop Name</a></th>
                            <th><a href="{{ route('gold-items.index', ['sort' => 'kind', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">Kind</a></th>
                            <th><a href="{{ route('gold-items.index', ['sort' => 'model', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">Model</a></th>
                            <th><a href="{{ route('gold-items.index', ['sort' => 'gold_color', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">Gold Color</a></th>
                            <th><a href="{{ route('gold-items.index', ['sort' => 'stones', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">Stones</a></th>
                            <th><a href="{{ route('gold-items.index', ['sort' => 'metal_type', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">Metal Type</a></th>
                            <th><a href="{{ route('gold-items.index', ['sort' => 'metal_purity', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">Metal Purity</a></th>
                            <th><a href="{{ route('gold-items.index', ['sort' => 'quantity', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">Quantity</a></th>
                            <th><a href="{{ route('gold-items.index', ['sort' => 'weight', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">Weight</a></th>
                            <th><a href="{{ route('gold-items.index', ['sort' => 'source', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">Source</a></th>
                            <th><a href="{{ route('gold-items.index', ['sort' => 'average_of_stones', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">Average of Stones</a></th>
                            <th><a href="{{ route('gold-items.index', ['sort' => 'net_weight', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">Net Weight</a></th>
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
                                <td>
                                    <a href="{{ route('gold-items.edit', $item->id) }}" class="btn btn-primary">Edit</a>
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
