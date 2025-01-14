@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Workshop Items</h1>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Items in Workshop</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Serial Number</th>
                            <th>Shop Name</th>
                            <th>Kind</th>
                            <th>Model</th>
                            <th>Weight</th>
                            <th>Transferred By</th>
                            <th>Transferred At</th>
                            <th>Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($workshopItems as $item)
                        <tr>
                            <td>{{ $item->serial_number }}</td>
                            <td>{{ $item->shop_name }}</td>
                            <td>{{ $item->kind }}</td>
                            <td>{{ $item->model }}</td>
                            <td>{{ $item->weight }}g</td>
                            <td>{{ $item->transferred_by }}</td>
                            <td>{{ $item->transferred_at->format('Y-m-d H:i') }}</td>
                            <td>{{ $item->transfer_reason }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $workshopItems->links() }}
        </div>
    </div>
</div>
@endsection
