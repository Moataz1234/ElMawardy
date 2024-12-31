<!DOCTYPE html>
<html>
<head>
    <title>Barcode View</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- <link rel="stylesheet" href="{{ asset('CSS/first_page.css') }}"> --}}
    @include('components.navbar')
    {{-- <link rel="stylesheet" href="{{ asset('CSS/navbar.css') }}"> --}}
    
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
                                <th>Shop_ID</th>
                                <td>{{ $item->shop_id ?? 'Admin' }}</td>
                            </tr>
                            <tr>
                                <th>Model</th>
                                <td>{{ $item->model }}</td>
                            </tr>
                            <tr>
                                <th>Weight</th>
                                <td>{{ $item->weight }}</td>
                            </tr>
                            <!-- New fields for source and stars -->
                            <tr>
                                <th>Source</th>
                                <td>{{ optional($item->modelCategory)->source }}</td> <!-- Assuming modelCategory returns a Models instance -->
                            </tr>
                            <tr>
                                <th>Stars</th>
                                <td>{{ optional($item->modelCategory)->stars }}</td> <!-- Assuming modelCategory returns a Models instance -->
                            </tr>
                        </table>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
    

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#shop-filter, #date-filter').change(function() {
                const shopId = $('#shop-filter').val();
                const date = $('#date-filter').val();
    
                const queryParams = new URLSearchParams();
                if (shopId) queryParams.append('shop_id', shopId);
                if (date) queryParams.append('date', date);
    
                window.location.href = '{{ route("barcode.view") }}?' + queryParams.toString();
            });
    
            $('#exportForm').submit(function() {
                $('#export-shop-id').val($('#shop-filter').val());
                $('#export-date').val($('#date-filter').val());
            });
        });
    </script>
</body>
</html>