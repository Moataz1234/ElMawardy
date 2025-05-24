<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requests Management - Super User</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- BoxIcons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .request-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    @include('components.navbar')

    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="fw-bold text-dark">All Requests Management</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('super.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item active">Requests</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Add Requests -->
        <div class="row">
            <div class="col-12">
                <div class="card request-card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0"><i class="bx bx-plus me-2"></i>Add Requests</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Serial Number</th>
                                        <th>Model</th>
                                        <th>Shop</th>
                                        <th>Kind</th>
                                        <th>Weight</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($addRequests as $request)
                                    <tr>
                                        <td><strong>{{ $request->id }}</strong></td>
                                        <td>{{ $request->serial_number }}</td>
                                        <td>{{ $request->model }}</td>
                                        <td>{{ $request->shop_name }}</td>
                                        <td>{{ $request->kind }}</td>
                                        <td>{{ $request->weight }}g</td>
                                        <td>
                                            <span class="badge {{ $request->status == 'pending' ? 'bg-warning text-dark' : ($request->status == 'approved' ? 'bg-success' : 'bg-danger') }}">
                                                {{ ucfirst($request->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $request->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            @if($request->status == 'pending')
                                            <div class="btn-group" role="group">
                                                <form method="POST" action="{{ route('super.handle-request', ['type' => 'add', 'id' => $request->id]) }}" style="display: inline;">
                                                    @csrf
                                                    <input type="hidden" name="action" value="approve">
                                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this request?')">
                                                        <i class="bx bx-check"></i> Approve
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('super.handle-request', ['type' => 'add', 'id' => $request->id]) }}" style="display: inline;">
                                                    @csrf
                                                    <input type="hidden" name="action" value="reject">
                                                    <button type="submit" class="btn btn-sm btn-danger ms-1" onclick="return confirm('Reject this request?')">
                                                        <i class="bx bx-x"></i> Reject
                                                    </button>
                                                </form>
                                            </div>
                                            @else
                                                <span class="text-muted">No actions available</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-center">
                                {{ $addRequests->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transfer Requests -->
        <div class="row">
            <div class="col-12">
                <div class="card request-card">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0"><i class="bx bx-transfer me-2"></i>Transfer Requests</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Item</th>
                                        <th>From Shop</th>
                                        <th>To Shop</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transferRequests as $request)
                                    <tr>
                                        <td><strong>{{ $request->id }}</strong></td>
                                        <td>
                                            @if($request->goldItem)
                                                <div>
                                                    <strong>{{ $request->goldItem->serial_number }}</strong><br>
                                                    <small class="text-muted">{{ $request->goldItem->model }}</small>
                                                </div>
                                            @else
                                                <span class="badge bg-secondary">Pound Transfer</span>
                                            @endif
                                        </td>
                                        <td><span class="badge bg-light text-dark">{{ $request->from_shop_name }}</span></td>
                                        <td><span class="badge bg-light text-dark">{{ $request->to_shop_name }}</span></td>
                                        <td>{{ ucfirst($request->type) }}</td>
                                        <td>
                                            <span class="badge {{ $request->status == 'pending' ? 'bg-warning text-dark' : ($request->status == 'approved' ? 'bg-success' : 'bg-danger') }}">
                                                {{ ucfirst($request->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $request->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            @if($request->status == 'pending')
                                            <div class="btn-group" role="group">
                                                <form method="POST" action="{{ route('super.handle-request', ['type' => 'transfer', 'id' => $request->id]) }}" style="display: inline;">
                                                    @csrf
                                                    <input type="hidden" name="action" value="approve">
                                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this request?')">
                                                        <i class="bx bx-check"></i> Approve
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('super.handle-request', ['type' => 'transfer', 'id' => $request->id]) }}" style="display: inline;">
                                                    @csrf
                                                    <input type="hidden" name="action" value="reject">
                                                    <button type="submit" class="btn btn-sm btn-danger ms-1" onclick="return confirm('Reject this request?')">
                                                        <i class="bx bx-x"></i> Reject
                                                    </button>
                                                </form>
                                            </div>
                                            @else
                                                <span class="text-muted">No actions available</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-center">
                                {{ $transferRequests->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sale Requests -->
        <div class="row">
            <div class="col-12">
                <div class="card request-card">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0"><i class="bx bx-money me-2"></i>Sale Requests</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Item Serial</th>
                                        <th>Shop</th>
                                        <th>Customer</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($saleRequests as $request)
                                    <tr>
                                        <td><strong>{{ $request->id }}</strong></td>
                                        <td>{{ $request->item_serial_number }}</td>
                                        <td>{{ $request->shop_name }}</td>
                                        <td>
                                            @if($request->customer)
                                                {{ $request->customer->first_name }} {{ $request->customer->last_name }}
                                            @else
                                                {{ $request->customer_first_name }} {{ $request->customer_last_name }}
                                            @endif
                                        </td>
                                        <td><strong class="text-success">${{ number_format($request->price, 2) }}</strong></td>
                                        <td>
                                            <span class="badge {{ $request->status == 'pending' ? 'bg-warning text-dark' : ($request->status == 'approved' ? 'bg-success' : 'bg-danger') }}">
                                                {{ ucfirst($request->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $request->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            @if($request->status == 'pending')
                                            <div class="btn-group" role="group">
                                                <form method="POST" action="{{ route('super.handle-request', ['type' => 'sale', 'id' => $request->id]) }}" style="display: inline;">
                                                    @csrf
                                                    <input type="hidden" name="action" value="approve">
                                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this request?')">
                                                        <i class="bx bx-check"></i> Approve
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('super.handle-request', ['type' => 'sale', 'id' => $request->id]) }}" style="display: inline;">
                                                    @csrf
                                                    <input type="hidden" name="action" value="reject">
                                                    <button type="submit" class="btn btn-sm btn-danger ms-1" onclick="return confirm('Reject this request?')">
                                                        <i class="bx bx-x"></i> Reject
                                                    </button>
                                                </form>
                                            </div>
                                            @else
                                                <span class="text-muted">No actions available</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-center">
                                {{ $saleRequests->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pound Requests -->
        <div class="row">
            <div class="col-12">
                <div class="card request-card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="card-title mb-0"><i class="bx bx-coin-stack me-2"></i>Pound Requests</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Serial Number</th>
                                        <th>Shop</th>
                                        <th>Type</th>
                                        <th>Weight</th>
                                        <th>Quantity</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($poundRequests as $request)
                                    <tr>
                                        <td><strong>{{ $request->id }}</strong></td>
                                        <td>{{ $request->serial_number }}</td>
                                        <td>{{ $request->shop_name }}</td>
                                        <td><span class="badge bg-info">{{ $request->type }}</span></td>
                                        <td>{{ $request->weight }}g</td>
                                        <td>{{ $request->quantity }}</td>
                                        <td>
                                            <span class="badge {{ $request->status == 'pending' ? 'bg-warning text-dark' : ($request->status == 'approved' ? 'bg-success' : 'bg-danger') }}">
                                                {{ ucfirst($request->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $request->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            @if($request->status == 'pending')
                                            <div class="btn-group" role="group">
                                                <form method="POST" action="{{ route('super.handle-request', ['type' => 'pound', 'id' => $request->id]) }}" style="display: inline;">
                                                    @csrf
                                                    <input type="hidden" name="action" value="approve">
                                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this request?')">
                                                        <i class="bx bx-check"></i> Approve
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('super.handle-request', ['type' => 'pound', 'id' => $request->id]) }}" style="display: inline;">
                                                    @csrf
                                                    <input type="hidden" name="action" value="reject">
                                                    <button type="submit" class="btn btn-sm btn-danger ms-1" onclick="return confirm('Reject this request?')">
                                                        <i class="bx bx-x"></i> Reject
                                                    </button>
                                                </form>
                                            </div>
                                            @else
                                                <span class="text-muted">No actions available</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-center">
                                {{ $poundRequests->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 