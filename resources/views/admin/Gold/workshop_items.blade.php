@extends('layouts.admin')

@section('content')
<div class="container-fluid" style="margin-left: 200px;">
    <h1 class="h3 mb-2 text-gray-800 text-center">Workshop Items</h1>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary text-center">Items in Workshop</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered mx-auto" id="dataTable" width="80%" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="text-center">Serial Number</th>
                            <th class="text-center">Shop Name</th>
                            <th class="text-center">Kind</th>
                            <th class="text-center">Model</th>
                            <th class="text-center">Weight</th>
                            <th class="text-center">Transferred By</th>
                            <th class="text-center">Transferred At</th>
                            <th class="text-center">Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($workshopItems as $item)
                        <tr>
                            <td class="text-center">{{ $item->serial_number }}</td>
                            <td class="text-center">{{ $item->shop_name }}</td>
                            <td class="text-center">{{ $item->kind }}</td>
                            <td class="text-center">{{ $item->model }}</td>
                            <td class="text-center">{{ $item->weight }}g</td>
                            <td class="text-center">{{ $item->transferred_by }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($item->transferred_at)->format('Y-m-d H:i') }}</td>
                            <td class="text-center">{{ $item->transfer_reason }}</td>
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
