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

        .status-accepted_by_shop {
            background-color: #6f42c1;
            color: #fff;
        }

        .status-rejected_by_shop {
            background-color: #fd7e14;
            color: #fff;
        }

        .action-buttons {
            margin-top: 20px;
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }

        .select-all-container {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .select-all-container label {
            margin-left: 5px;
            margin-bottom: 0;
            cursor: pointer;
        }

        .form-check-input {
            cursor: pointer;
        }
        
        /* Ensure checkbox is aligned properly within the table cell */
        .checkbox-column {
            text-align: center;
            vertical-align:top ;
            width: 50px;
        }
        
        /* Additional column styling */
        .table th, .table td {
            vertical-align: middle;
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
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {!! session('error') !!}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="filters-container">
            <form action="{{ route('shop.workshop.requests') }}" method="GET" class="row align-items-end">
                {{-- <div class="col-md-3">
                    <label for="status" class="form-label">Request Status</label>
                    <select name="status" id="status" class="form-control custom-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="accepted_by_shop" {{ request('status') == 'accepted_by_shop' ? 'selected' : '' }}>Accepted By Shop</option>
                        <option value="rejected_by_shop" {{ request('status') == 'rejected_by_shop' ? 'selected' : '' }}>Rejected By Shop</option>
                    </select>
                </div> --}}

                <div class="col-md-3">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" class="form-control" id="date" name="date" 
                           value="{{ request('date') }}">
                </div>

                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           placeholder="Serial number, reason..." value="{{ request('search') }}">
                </div>

                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-block">Filter</button>
                    <a href="{{ route('shop.workshop.requests') }}" class="btn btn-secondary btn-block mt-2">Reset</a>
                </div>
            </form>
        </div>

        <form action="{{ route('shop.workshop.requests.handle') }}" method="POST" id="batchActionForm">
            @csrf
            <div class="select-all-container" style="margin-left: 10px;">
                <input type="checkbox" id="select-all" class="form-check-input" >
                <label for="select-all">Select All</label>
            </div>

            <div class="action-buttons">
                <button type="submit" name="action" value="accept" class="btn btn-success">
                    <i class="fas fa-check"></i> Accept Selected
                </button>
                <button type="submit" name="action" value="reject" class="btn btn-danger">
                    <i class="fas fa-times"></i> Reject Selected
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-striped border">
                    <thead>
                        <tr>
                            <th class="checkbox-column">
                                <div class="text-center">Select</div>
                            </th>
                            <th>Serial Number</th>
                            <th>Model</th>
                            <th>Weight</th>
                            <th>Requested By</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalRequests = count($requests ?? []);
                            $totalWeight = 0;
                        @endphp
                        @forelse ($requests ?? [] as $request)
                            @php
                                $goldItem = App\Models\GoldItem::where('serial_number', $request->serial_number)->first();
                                $itemModel = $goldItem ? $goldItem->model : 'N/A';
                                $itemWeight = $goldItem ? $goldItem->weight : 0;
                                $totalWeight += $itemWeight;
                            @endphp
                            <tr>
                                <td class="checkbox-column">
                                    @if(in_array($request->status, ['pending', 'approved']))
                                        <input type="checkbox" name="selected_items[]" value="{{ $request->id }}" class="form-check-input item-checkbox">
                                    @else
                                        <input type="checkbox" disabled class="form-check-input">
                                    @endif
                                </td>
                                <td>{{ $request->serial_number }}</td>
                                <td>{{ $itemModel }}</td>
                                <td>{{ $itemWeight }} g</td>
                                <td>{{ $request->requested_by }}</td>
                                <td>{{ $request->reason }}</td>
                                <td>
                                    <span class="badge status-{{ $request->status }}">
                                        {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                    </span>
                                </td>
                                <td>{{ $request->created_at }}</td>
                            </tr>
                        @empty
                            {{-- <tr>
                                <td colspan="8" class="text-center">
                                    <div class="alert alert-info">
                                        <p>No workshop requests found for <strong>{{ $shopName }}</strong>.</p>
                                        
                                        @if(isset($noRequestsFound) && $noRequestsFound && isset($allShops) && count($allShops) > 0)
                                            <hr>
                                            <p><strong>Possible Shop Name Mismatch:</strong></p>
                                            <p>Your user account shop name is <strong>"{{ $shopName }}"</strong>, but it doesn't match any shop names in our system.</p>
                                            <p>Workshop requests exist for the following shop names:</p>
                                            <ul class="text-left">
                                                @foreach($allShops as $shop)
                                                    <li>{{ $shop }}</li>
                                                @endforeach
                                            </ul>
                                            <p>Please contact an administrator to update your shop name to match one of these shop names.</p>
                                        @endif
                                    </div>
                                </td>
                            </tr> --}}
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        <div class="d-flex justify-content-between mt-4">
            <div class="total_items badge">
                Total Requests: <span>{{ $totalRequests }}</span>
            </div>
            <div class="total_weight badge">
                Total Weight: <span>{{ number_format($totalWeight, 2) }} g</span>
            </div>
        </div>

        @if(isset($requests) && method_exists($requests, 'links'))
            <div class="mt-4">
                {{ $requests->links() }}
            </div>
        @endif
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function() {
            // Select all checkbox functionality
            $('#select-all').on('change', function() {
                $('.item-checkbox:not(:disabled)').prop('checked', $(this).prop('checked'));
            });
            
            // Update select all checkbox when individual items are clicked
            $('.item-checkbox').on('change', function() {
                if (!$(this).prop('checked')) {
                    $('#select-all').prop('checked', false);
                } else {
                    const allChecked = $('.item-checkbox:not(:disabled)').length === $('.item-checkbox:checked').length;
                    $('#select-all').prop('checked', allChecked);
                }
            });
            
            // Form submission confirmation
            $('#batchActionForm').on('submit', function(e) {
                const action = $(document.activeElement).val();
                const selectedCount = $('.item-checkbox:checked').length;
                
                if (selectedCount === 0) {
                    e.preventDefault();
                    alert('Please select at least one item.');
                    return false;
                }
                
                const confirmMessage = action === 'accept' ? 
                    'Are you sure you want to accept these ' + selectedCount + ' items?' : 
                    'Are you sure you want to reject these ' + selectedCount + ' items?';
                
                if (!confirm(confirmMessage)) {
                    e.preventDefault();
                    return false;
                }
            });
        });
    </script>
</body>
</html> 