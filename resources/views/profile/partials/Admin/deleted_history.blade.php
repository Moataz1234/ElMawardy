<div class="container">
    <h2>Deleted Items History</h2>
    
    <table class="table">
        <thead>
            <tr>
                <th>Serial Number</th>
                <th>Model</th>
                <th>Shop Name</th>
                <th>Deleted By</th>
                <th>Deletion Reason</th>
                <th>Deleted At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($deletedItems as $item)
                <tr>
                    <td>{{ $item->serial_number }}</td>
                    <td>{{ $item->model }}</td>
                    <td>{{ $item->shop_name }}</td>
                    <td>{{ $item->deletedBy }}</td>
                    <td>{{ $item->deletion_reason ?? 'Not specified' }}</td>
                    <td>{{ $item->deleted_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    {{-- {{ $deletedItems->links() }} --}}
</div>