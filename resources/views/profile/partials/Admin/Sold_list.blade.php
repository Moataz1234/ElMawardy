{{-- @include('Temp.sidebar') --}}
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

    <table class="table">
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
                <th>Stars</th>
                {{-- <th>Sold Price</th> --}}
                <th>Sold Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="table-body">
            @foreach ($goldItems as $item)
                <tr>
                    <td>
                        <input type="checkbox" class="select-item" data-id="{{ $item->id }}">
                    </td>
                    <td>
                        @if ($item->modelCategory)
                            @if ($item->modelCategory->scanned_image)
                                <img src="{{ asset('storage/' . $item->modelCategory->scanned_image) }}"
                                    alt="Scanned Image" width="50">
                            @endif
                        @else
                            No matching model found
                        @endif
                    </td>
                    <td>{{ $item->serial_number }}</td>
                    <td>{{ $item->shop_name }}</td>
                    <td>{{ $item->kind }}</td>
                    <td>{{ $item->model }}</td>
                    <td>{{ $item->gold_color }}</td>
                    <td>{{ $item->weight }}</td>
                    <td>{{ $item->modelCategory->stars ?? 'No Stars' }}</td>
                    {{-- <td>{{ $item->price }}</td> --}}
                    <td>{{ $item->sold_date }}</td>
                    <td>
                        <form action="{{ route('gold-items-sold.markAsRest', $item->id) }}" method="POST"
                            style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-warning">Return to Stock</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>
