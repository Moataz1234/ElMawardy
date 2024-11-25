<!DOCTYPE html>
<html lang="en">
<head>
    {{-- @include("GoldCatalog.Shared.adminNavBar")
    @include("GoldCatalog.Shared.sideBar") --}}
    {{-- @include('dashboard') --}}
    @include('components.navbar')
    @include('Temp.sidebar')

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
    <form method="POST" action="{{ route('bulk-action') }}">
        @csrf
    <table class="table">
        <thead>
            <tr>
                <th><input type="checkbox" id="select-all" /></th>
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
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($goldItems as $item)
                <tr>
                    <td><input type="checkbox" name="selected_items[]" value="{{ $item->id }}" /></td>
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
                    <td>
                        <a class="action_button" href="{{ route('gold-items.edit', $item->id) }}">Edit</a>
                    </td>
                    {{-- <td>{{ $item->source }}</td> --}}
                    {{-- <td>{{ $item->average_of_stones }}</td> --}}
                    {{-- <td>{{ $item->net_weight }}</td> --}}
                    {{-- <td>
                        <a class="action_button" href="{{ route('gold-items.edit', $item->id) }}" >Edit</a> --}}
                </tr>
            @endforeach 
        </tbody>
    </table>
    
    <button type="submit" name="action" value="delete">Delete </button>
    <button type="submit" name="action" value="request">Request Item</button>
</form>
@push('modals')
    @include('profile.partials._image_modal')
@endpush
@include('components.pagination')

@push('scripts')

    <script>
        document.getElementById('select-all').onclick = function() {
            var checkboxes = document.getElementsByName('selected_items[]');
            for (var checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        }
    </script>
    
<script src="{{ asset('js/modal.js') }}"></script>
<script src="{{ asset('js/checkbox-selection.js') }}"></script>
@if(isset($cleared_items))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const clearedData = @json($cleared_items);
        const currentTime = Math.floor(Date.now() / 1000);
        
        // Only clear if the data is recent (within last 5 seconds)
        if (currentTime - clearedData.timestamp < 5) {
            clearedData.items.forEach(itemId => {
                const checkbox = document.querySelector(`.select-item[data-id="${itemId}"]`);
                if (checkbox) {
                    checkbox.checked = false;
                }
            });
        }
        
        localStorage.removeItem('selectedItems');
    });
    </script>
@endif
</body>
</html>
