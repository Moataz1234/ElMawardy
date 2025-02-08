<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    @include('components.navbar')
</head>
<div class="container mt-4">
    <h2 class="text-center mb-4">Item Requests</h2>

    <table class="table table-striped table-bordered shadow-sm">
        <thead class="thead-dark">
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
            @foreach ($requests as $request)
                <tr>
                    <td><img src="{{ asset($request->scaned_image) }}" width="50" class="img-thumbnail"></td>
                    <td>{{ $request->item->serial_number }}</td>
                    <td>{{ $request->admin->name }}</td>
                    <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                    <td class="text-capitalize">{{ $request->status }}</td>
                    <td>
                        @if ($request->status === 'pending')
                            <form method="POST" action="{{ route('shop.requests.update', $request) }}"
                                class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" name="status" value="accepted"
                                    class="btn btn-success btn-sm mr-2">
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

    <div class="d-flex justify-content-center">
        {{ $requests->links() }}
    </div>
</div>
