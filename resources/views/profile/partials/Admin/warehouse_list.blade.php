<div class="container">
    <h2>Warehouse Items</h2>
    
    <table class="table">
        <thead>
            <tr>
                <th>Serial Number</th>
                <th>Kind</th>
                <th>Model</th>
                <th>Weight</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item->serial_number }}</td>
                <td>{{ $item->kind }}</td>
                <td>{{ $item->model }}</td>
                <td>{{ $item->weight }}</td>
                <td>{{ $item->price }}</td>
                <td>
                    <button type="button" data-toggle="modal" data-target="#assignModal{{ $item->id }}">
                        Assign to Shop
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- {{ $items->links() }} --}}
</div>

@foreach($items as $item)
<div class="modal" id="assignModal{{ $item->id }}">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.warehouse.assign', $item->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h4>Assign Item to Shop</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Shop ID</label>
                        <input type="number" name="shop_id" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Shop Name</label>
                        <input type="text" name="shop_name" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Assign</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach