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
                            <th>Serial Number</th>
                            <th>Shop Name</th>
                            <th>Kind</th>
                            <th>Model</th>
                            <th>Gold Color</th>
                            <th>Stones</th>
                            <th>Metal Type</th>
                            <th>Metal Purity</th>
                            <th>Quantity</th>
                            <th>Weight</th>
                            <th>Source</th>
                            <th>Price</th>
                            <th>Average of Stones</th>
                            <th>Net Weight</th>
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
                                <td>{{ $item->price }}</td>
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
