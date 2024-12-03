
<div class="container">
    <h2>Item Requests</h2>
    
    <table class="table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Serial Number</th>
                <th>Requested By</th>
                <th>Request Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requests as $request)
                <tr>
                    <td><img src="{{ asset($request->item->link) }}" width="50" class="img-thumbnail"></td>
                    <td>{{ $request->item->serial_number }}</td>
                    <td>{{ $request->admin->name }}</td>
                    <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ ucfirst($request->status) }}</td>
                    <td>
                        @if($request->status === 'pending')
                            <form method="POST" action="{{ route('shop.requests.update', $request) }}" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" name="status" value="accepted" class="btn btn-success btn-sm">
                                    Accept
                                </button>
                                <button type="submit" name="status" value="rejected" class="btn btn-danger btn-sm">
                                    Reject
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    {{ $requests->links() }}
</div>
