<!DOCTYPE html>
<html>
<head>
    <title>Barcode View</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @include('components.navbar')

    <style>
        .barcode-row {
            page-break-inside: avoid;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row mb-3 no-print">
            <div class="col-md-3">
                <select class="form-select" id="shop-filter">
                    <option value="">All Shops</option>
                    @foreach($shops as $shop)
                        <option value="{{ $shop->id }}" {{ request('shop_id') == $shop->id ? 'selected' : '' }}>
                            {{ $shop->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" id="date-filter" class="form-control" value="{{ request('date') }}">
            </div>
            
            <div class="col-md-3">
                <form id="exportForm" action="{{ route('barcode.export') }}" method="GET">
                    <input type="hidden" name="shop_id" id="export-shop-id" value="">
                    <input type="hidden" name="date" id="export-date" value="">
                    <button type="submit" class="btn btn-success">Export to Excel</button>
                </form>
            </div>
        </div>

        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Serial Number</th>
                    <th>Shop ID</th>
                    <th>Model</th>
                    <th>Weight</th>
                    <th>Source</th>
                    <th>To-Print</th>
                    <th>Stars</th>
                </tr>
            </thead>
            <tbody>
                @foreach($goldItems as $item)
                    <tr>
                        <td>{{ $item->serial_number }}</td>
                        <td>{{ $item->shop_id ?? 'Admin' }}</td>
                        <td>{{ $item->model }}</td>
                        <td>{{ $item->weight }}</td>
                        <td>{{ optional($item->modelCategory)->source }}</td>
                        <td>{{ $item->modified_source }}</td>
                        <td>{{ optional($item->modelCategory)->stars }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        document.getElementById('shop-filter').addEventListener('change', function () {
            const date = document.getElementById('date-filter').value;
            const shopId = this.value;
            window.location.href = `?shop_id=${shopId}&date=${date}`;
        });

        document.getElementById('date-filter').addEventListener('change', function () {
            const shopId = document.getElementById('shop-filter').value;
            const date = this.value;
            window.location.href = `?shop_id=${shopId}&date=${date}`;
        });

        document.getElementById('exportForm').addEventListener('submit', function () {
            document.getElementById('export-shop-id').value = document.getElementById('shop-filter').value;
            document.getElementById('export-date').value = document.getElementById('date-filter').value;
        });
    </script>
</body>
</html>
