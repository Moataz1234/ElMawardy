<!DOCTYPE html>
<html lang="en">
<head>
    @include('components.navbar')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gold Item Weight History</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 1200px;
            padding-top: 20px;
        }
        .card-header {
            background-color: #343a40;
            color: white;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .table thead th {
            background-color: #e9ecef;
            border-color: #dee2e6;
        }
        .difference-positive {
            color: green;
            font-weight: bold;
        }
        .difference-negative {
            color: red;
            font-weight: bold;
        }
        .difference-zero {
            color: grey;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h4 class="mb-0">Gold Item Weight History</h4>
                    </div>

                    <div class="card-body">
                        <form method="GET" action="{{ route('gold-item-weight-history.index') }}" class="mb-4">
                            <div class="input-group">
                                <input type="text" name="search_serial_number" class="form-control"
                                       placeholder="Search by Serial Number..." value="{{ $searchSerialNumber ?? '' }}">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">Search</button>
                                    @if($searchSerialNumber)
                                        <a href="{{ route('gold-item-weight-history.index') }}" class="btn btn-secondary">Clear</a>
                                    @endif
                                </div>
                            </div>
                        </form>

                        @if($weightHistories->isEmpty())
                            <p class="text-center">No weight history records found.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Date</th>
                                            <th>User</th>
                                            <th>Serial Number</th>
                                            <th>Model</th>
                                            <th>Weight Before (g)</th>
                                            <th>Weight After (g)</th>
                                            <th>Difference (g)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($weightHistories as $index => $history)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $history->created_at->format('Y-m-d H:i:s') }}</td>
                                            <td>{{ $history->user ? $history->user->name : 'N/A' }}</td>
                                            <td>{{ $history->goldItem ? $history->goldItem->serial_number : 'N/A' }}</td>
                                            <td>{{ $history->goldItem ? $history->goldItem->model : 'N/A' }}</td>
                                            <td>{{ number_format($history->weight_before, 2) }}</td>
                                            <td>{{ number_format($history->weight_after, 2) }}</td>
                                            @php
                                                $difference = $history->weight_after - $history->weight_before;
                                                $difference_class = $difference > 0 ? 'difference-positive' : ($difference < 0 ? 'difference-negative' : 'difference-zero');
                                            @endphp
                                            <td class="{{ $difference_class }}">{{ number_format($difference, 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html> 