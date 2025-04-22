<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Add Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @include('components.navbar')
    
    <style>

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                font-size: 10pt;
            }
            .table {
                width: 100%;
                border-collapse: collapse;
            }
            .table td, .table th {
                border: 1px solid #000;
                padding: 5px;
            }
        }
        .header-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-info no-print">
            <div class="filters-container">
                <form action="{{ route('admin.add.requests.print') }}" method="GET" class="form-inline">
                    <select name="status" class="form-control mr-2">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                    </select>

                    <select name="shop_name" class="form-control mr-2">
                        <option value="">All Shops</option>
                        @foreach ($shops as $shop)
                            <option value="{{ $shop }}" {{ request('shop_name') == $shop ? 'selected' : '' }}>
                                {{ $shop }}
                            </option>
                        @endforeach
                    </select>

                    <input type="date" name="date" class="form-control mr-2" value="{{ request('date', date('Y-m-d')) }}">

                    <button type="submit" class="btn btn-primary mr-2">Filter</button>
                    <button type="button" class="btn btn-success" onclick="window.print()">Print</button>
                </form>
            </div>
        </div>

        <div class="print-header text-center mb-4">
            <h3>Add Requests Print</h3>
            <div class="row">
                <div class="col text-left">
                    <strong>Shop ID:</strong> {{ request('shop_name', 'All Shops') }}
                </div>
                <div class="col text-right">
                    <strong>Date:</strong> {{ request('date', date('Y-m-d')) }}
                </div>
            </div>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Serial Number</th>
                    <th>Model</th>
                    <th>Kind</th>
                    <th>Weight</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalWeight = 0;
                    $totalQuantity = 0;
                @endphp
                @forelse ($requests as $request)
                    <tr>
                        <td>{{ $request->serial_number }}</td>
                        <td>{{ $request->model }}</td>
                        <td>{{ $request->kind }}</td>
                        <td>{{ $request->weight }}</td>
                        <td>{{ $request->quantity ?? 1 }}</td>
                    </tr>
                    @php
                        $totalWeight += $request->weight;
                        $totalQuantity += $request->quantity ?? 1;
                    @endphp
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No requests found.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right"><strong>Total</strong></td>
                    <td><strong>{{ number_format($totalWeight, 2) }}</strong></td>
                    <td><strong>{{ $totalQuantity }}</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>