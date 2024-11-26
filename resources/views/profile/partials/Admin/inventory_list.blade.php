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
    
    <button class="delete_btn"  type="submit" name="action" value="delete">Delete </button>
    <button class="request_btn" type="submit" name="action" value="request">Request Item</button>
</form>
</body>
</html>
