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
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Gold Items Analysis</h3>
                        <div class="d-flex gap-2">
                            <form action="{{ route('gold-analysis.index') }}" method="GET" class="d-flex gap-2">
                                <select name="shop_name" class="form-select">
                                    <option value="">All Shops</option>
                                    @foreach($shops as $id => $name)
                                        <option value="{{ $name }}" {{ $selectedShop == $name ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </form>
                            <form action="{{ route('gold-analysis.export') }}" method="GET">
                                @if($selectedShop)
                                    <input type="hidden" name="shop_name" value="{{ $selectedShop }}">
                                @endif
                                <button type="submit" class="btn btn-success">
                                    Export to Excel
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Kind</th>
                                        <th>Total Items</th>
                                        <th>Total Weight</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($statistics as $stat)
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
                                        <td><strong>{{ $statistics->sum('total_items') }}</strong></td>
                                        <td><strong>{{ number_format($statistics->sum('total_weight'), 2) }} g</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 