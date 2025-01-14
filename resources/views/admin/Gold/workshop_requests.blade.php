@extends('layouts.admin')

@section('content')
<div class="container-fluid" style="margin-left: 200px;">
    <h1 class="h3 mb-2 text-gray-800 text-center">Workshop Transfer Requests</h1>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary text-center">Pending Workshop Transfers</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered mx-auto" id="dataTable" width="80%" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="text-center">Serial Number</th>
                            <th class="text-center">Shop Name</th>
                            <th class="text-center">Requested By</th>
                            <th class="text-center">Reason</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($requests as $request)
                        <tr>
                            <td class="text-center">{{ $request->serial_number }}</td>
                            <td class="text-center">{{ $request->shop_name }}</td>
                            <td class="text-center">{{ $request->requested_by }}</td>
                            <td class="text-center">{{ $request->reason }}</td>
                            <td class="text-center">
                                <span class="badge badge-{{ $request->status === 'pending' ? 'warning' : ($request->status === 'approved' ? 'success' : 'danger') }}">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($request->status === 'pending')
                                <form method="POST" action="{{ route('workshop.requests.handle', $request->id) }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('workshop.requests.handle', $request->id) }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $requests->links() }}
        </div>
    </div>
</div>
@endsection
