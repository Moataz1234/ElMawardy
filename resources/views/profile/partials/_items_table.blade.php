<style>
    .select-item:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    tr.pending-transfer {
        background-color: #f8f9fa;
    }
    
    .pending-badge {
        background-color: #ffc107;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 0.8em;
        color: #000;
        display: inline-block;
    }
</style>
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
                <th>Category</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody id="table-body">   
            @foreach ($goldItems as $item)
                @php
                $isOuter = \App\Models\Outer::where('gold_serial_number', $item->serial_number)
                                          ->where('is_returned', false)
                                          ->exists();
                @endphp
                <tr 
                style="{{ $isOuter ? 'background-color: yellow;' : '' }}">
                <td>
                    @php
                        $isPending = \App\Models\TransferRequest::where('gold_item_id', $item->id)
                            ->where('status', 'pending')
                            ->exists();
                    @endphp
                    @if(!$isPending)
                        <input type="checkbox" class="select-item" data-id="{{ $item->id }}">
                    @else
                        <span class="pending-badge">
                            Pending Transfer to {{ $item->transferRequests->where('status', 'pending')->first()->to_shop_name }}
                        </span>
                    @endif
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
                    <td>{{ $item->modelCategory->category ?? 'No Category' }}</td>
                    <td>{{ $item->calculated_price }}</td>
{{-- 
                    <td>
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
                    </td> --}}
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="button-container">
        <button id="sellItemsButton" class="request_btn">Sell</button> 
        <button id="transferItemsButton" class="delete_btn">Transfer</button>
        {{-- <button type="submit" class="outer-btn">Outer</button> --}}

    </div>
</div>

{{-- {{ $goldItems->links('pagination::bootstrap-4') }} --}}

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