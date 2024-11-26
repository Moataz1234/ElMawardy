<!DOCTYPE html>
<html lang="en">
<head>
    {{-- @include("GoldCatalog.Shared.adminNavBar")
    @include("GoldCatalog.Shared.sideBar") --}}
    {{-- @include('dashboard') --}}
@include('Temp.dashboard')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalog Items</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">   
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="{{ asset('css/first_page.css') }}" rel="stylesheet">
    {{-- <link href="{{ asset('css/pagination.css') }}" rel="stylesheet"> --}}
    <link href="{{ asset('css/modal.css') }}" rel="stylesheet">
</head>
<body>
    <table class="table">
        <thead>
            <tr>
                <th>Image</th>
                    @php
                    // Array of columns with their display names
                    $columns = [
                        'serial_number' => 'Serial Number',
                        'shop_name' => 'Shop Name',
                        'kind' => 'Kind',
                        'model' => 'Model',
                        'gold_color' => 'Gold Color',
                        // 'stones' => 'Stones',
                        // 'metal_type' => 'Metal Type',
                        'metal_purity' => 'Metal Purity',
                        // 'quantity' => 'Quantity',
                        'weight' => 'Weight',
                        'category' =>'Category',
                        // 'source' => 'Source',
                        // 'average_of_stones' => 'Average of Stones',
                        // 'net_weight' => 'Net Weight',
                    ];
                @endphp

                @foreach ($columns as $field => $label)
                    <th>
                        <div class="sort-container">
                            {{ $label }}
                            <form method="GET" action="{{ route('gold-items.index') }}" style="display:inline;">
                                <input type="hidden" name="sort" value="{{ $field }}">
                            </form>
                        </div>  
                        @endforeach
                </th>
                {{-- <th>Actions</th> --}}
            </tr>
        </thead>
        <tbody>
            @foreach ($goldItems as $item)
                <tr>
                    <td><img src="{{ asset($item->link) }}" alt="Image" width="50" class="img-thumbnail"></td>
                    <td>{{ $item->serial_number }}</td>
                    <td>{{ $item->shop->name }}</td>
                    <td>{{ $item->kind }}</td>
                    <td>{{ $item->model }}</td>
                    <td>{{ $item->gold_color }}</td>
                    {{-- <td>{{ $item->stones }}</td> --}}
                    {{-- <td>{{ $item->metal_type }}</td> --}}
                    <td>{{ $item->metal_purity }}</td>
                    {{-- <td>{{ $item->quantity }}</td> --}}
                    <td>{{ $item->weight }}</td>
                    <td>{{ $item->modelCategory->category ?? 'No Category' }}</td>

                    {{-- <td>{{ $item->source }}</td> --}}
                    {{-- <td>{{ $item->average_of_stones }}</td> --}}
                    {{-- <td>{{ $item->net_weight }}</td> --}}
                    {{-- <td>
                        <a class="action_button" href="{{ route('gold-items.edit', $item->id) }}" >Edit</a> --}}
                </tr>
            @endforeach 
        </tbody>
    </table>
</body>
</html>
