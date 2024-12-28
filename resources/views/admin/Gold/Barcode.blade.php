<!DOCTYPE html>
<html>
<head>
    <title>Barcode View</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
    <div class="container-fluid">
        <div class="row mb-3 no-print">
            <div class="col-md-4">
                <select class="form-select" id="shop-filter">
                    <option value="">All Shops</option>
                    @foreach($shops as $shop)
                        <option value="{{ $shop->id }}" {{ request('shop_id') == $shop->id ? 'selected' : '' }}>
                            {{ $shop->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <form id="exportForm" action="{{ route('barcode.export') }}" method="GET">
                    <input type="hidden" name="shop_id" id="export-shop-id" value="">
                    <button type="submit" class="btn btn-success" id="export-excel">Export to Excel</button>
                </form>
            </div>
        </div>

        <div id="barcode-content">
            @foreach($goldItems->chunk(2) as $chunk)
                <div class="row barcode-row mb-4">
                    @foreach($chunk as $item)
                        <div class="col-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Serial Number</th>
                                    <td>{{ $item->serial_number }}</td>
                                </tr>
                                <tr>
                                    <th>Shop</th>
                                    <td>{{ $item->shop_name ?? 'Admin' }}</td>
                                </tr>
                                <tr>
                                    <th>Model</th>
                                    <td>{{ $item->model }}</td>
                                </tr>
                                <tr>
                                    <th>Weight</th>
                                    <td>{{ $item->weight }}</td>
                                </tr>
                            </table>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Shop filter functionality
            $('#shop-filter').change(function() {
                const shopId = $(this).val();
                window.location.href = '{{ route("barcode.view") }}?shop_id=' + shopId;
            });

            // Export to Excel functionality
            $('#export-excel').click(function(e) {
                const shopId = $('#shop-filter').val();
                $('#export-shop-id').val(shopId);
                $('#exportForm').submit();
            });
        });
    </script>
</body>
</html>