<div class="spreadsheet">
    <table>
        <thead>
            <tr>
                <th>Select</th>
                <th>Image</th>
                <th>Serial Number</th>
                <th>Shop Name</th>
                <th>Kind</th>
                <th>Model</th>
                <th>Gold Color</th>
                <th>Weight</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="table-body">   
            @foreach ($goldItems as $item)
                @php
                $isOuter = \App\Models\Outer::where('gold_serial_number', $item->serial_number)
                                          ->where('is_returned', false)
                                          ->exists();
                @endphp
                <tr style="{{ $isOuter ? 'background-color: yellow;' : '' }}">
                    <td>
                        <input type="checkbox" class="select-item" data-id="{{ $item->id }}">
                    </td>
                    <td>
                        @if($item->link)
                            <img
                             src="{{ asset($item->link) }}" alt="Image" width="50">
                        @else
                            No Image
                        @endif
                    </td>
                    <td>{{ $item->serial_number }}</td>
                    <td>{{ $item->shop->name }}</td>
                    <td>{{ $item->kind }}</td>
                    <td>{{ $item->model }}</td>
                    <td>{{ $item->gold_color }}</td>
                    <td>{{ $item->weight }}</td>
                    <td>
                        <a class="action_button" href="{{ route('gold-items.transferForm', $item->id) }}" 
                           {{ $isOuter ? 'style=pointer-events:none;opacity:0.5;' : '' }}>
                            Transfer
                        </a>
                        @include('Shops.Gold.outerForm')
                        
                        @if ($isOuter)
                            <form action="{{ route('gold-items.returnOuter', $item->serial_number) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="returned-btn">Returned</button>
                            </form>
                        @else
                            <form action="{{ route('gold-items.toggleReturn', $item->serial_number) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="outer-btn">Outer</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="button-container">
        <button id="sellItemsButton" class="image-button">Sell</button> 
        <button id="transferItemsButton" class="image-button">Transfer</button>
    </div>
</div>

{{ $goldItems->links('pagination::bootstrap-4') }}

@if(session('clear_selections'))
<script>
    localStorage.removeItem('selectedItems');
</script>
@endif