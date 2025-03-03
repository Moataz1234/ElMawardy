<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gold Items Analysis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @include('components.navbar')

</head>
<body>
    <div class="container-fluid mt-4">
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h2>Gold Items Analysis - All Shops</h2>
                <form action="{{ route('gold-analysis.export') }}" method="GET">
                    <button type="submit" class="btn btn-success">
                        Export All to Excel
                    </button>
                </form>
            </div>
        </div>

        <div class="row">
            @foreach($shopStatistics as $shopData)
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">Shop #{{ $shopData['shop']->id }} - {{ $shopData['shop']->name }}</h4>
                            <form action="{{ route('gold-analysis.export') }}" method="GET">
                                <input type="hidden" name="shop_name" value="{{ $shopData['shop']->name }}">
                                <button type="submit" class="btn btn-sm btn-success">
                                    Export
                                </button>
                            </form>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-sm">
                                    <thead>
                                        <tr>
                                            <th>Kind</th>
                                            <th>Total Items</th>
                                            <th>Total Weight</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($shopData['statistics'] as $stat)
                                        <tr>
                                            <td>{{ $stat->kind }}</td>
                                            <td>{{ $stat->total_items }}</td>
                                            <td>{{ number_format($stat->total_weight, 2) }} g</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-dark">
                                            <td><strong>Total</strong></td>
                                            <td><strong>{{ $shopData['statistics']->sum('total_items') }}</strong></td>
                                            <td><strong>{{ number_format($shopData['statistics']->sum('total_weight'), 2) }} g</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 