<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gold Items Average Stones Weight</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/pagination.css') }}" rel="stylesheet">
<body>
    <!-- Include the navbar -->
    @include('components.navbar')

    <div class="container mt-4">
        <h1>Gold Items Average Stones Weight</h1>
        <a href="{{ route('admin.gold_items_avg.create') }}" class="btn btn-primary mb-3">Add New Record</a>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Search Form -->
        <form action="{{ route('admin.gold_items_avg.index') }}" method="GET" class="mb-4">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search by model..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary">Search</button>
                @if (request('search'))
                    <a href="{{ route('admin.gold_items_avg.index') }}" class="btn btn-secondary">Clear</a>
                @endif
            </div>
        </form>

        <table class="table table-success">
            <thead>
                <tr>
                    <th>Model</th>
                    <th>Stones Weight</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($goldItemsAvg as $item)
                    <tr>
                        <td>{{ $item->model }}</td>
                        <td>{{ $item->stones_weight }}</td>
                        <td>
                            <a href="{{ route('admin.gold_items_avg.edit', $item->id) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('admin.gold_items_avg.destroy', $item->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination Links -->
        {{-- @include('components.models_pagination') --}}
        {{ $goldItemsAvg->appends(request()->query())->links() }} <!-- Pagination with search and sort parameters -->
        {{-- {{ $goldItemsAvg->appends(['search' => request('search')])->links() }} --}}
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>