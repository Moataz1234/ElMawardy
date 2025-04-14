<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Workshop Requests</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            max-width: 1400px;
            margin-top: 2rem;
        }

        .filters-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .table {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .table thead th {
            background-color: #343a40;
            color: white;
            border-color: #454d55;
        }

        .badge {
            font-size: 14px;
            padding: 8px 12px;
            margin: 5px;
        }

        .total_items {
            background-color: #17a2b8;
            color: white;
        }

        .total_weight {
            background-color: #28a745;
            color: white;
        }

        .status-pending {
            background-color: #ffc107;
            color: #000;
        }

        .status-approved {
            background-color: #28a745;
            color: #fff;
        }

        .status-rejected {
            background-color: #dc3545;
            color: #fff;
        }
    </style>
</head>

<body>
    @include('components.navbar')
    <div class="container">
        <h1 class="text-center mb-4">My Workshop Transfer Requests</h1>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {!! session('success') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="filters-container">
            <form action="{{ route('shop.workshop.requests') }}" method="GET" class="row align-items-end g-3">
                <div class="col">
                    <label for="status" class="form-label">Request Status</label>
                    <select name="status" id="status" class="form-select custom-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <div class="col">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" class="form-control" id="date" name="date" 
                           value="{{ request('date', date('Y-m-d')) }}">
                </div>

                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <button type="reset" class="btn btn-secondary">Reset</button>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-striped border">
                <thead>
                    <tr>
                        <th>Serial Number</th>
                        <th>Requested By</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalRequests = count($requests ?? []);
                    @endphp
                    @forelse ($requests ?? [] as $request)
                        <tr>
                            <td>{{ $request->serial_number }}</td>
                            <td>{{ $request->requested_by }}</td>
                            <td>{{ $request->reason }}</td>
                            <td>
                                <span class="badge status-{{ $request->status }}">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </td>
                            <td>{{ $request->created_at }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <div class="total_items badge">
                Total Requests: <span>{{ $totalRequests }}</span>
            </div>
        </div>

        @if(isset($requests) && method_exists($requests, 'links'))
            <div class="mt-4">
                {{ $requests->links() }}
            </div>
        @endif
    </div>
</body>
</html> 