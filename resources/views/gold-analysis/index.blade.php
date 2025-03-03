<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gold Items Analysis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @include('components.navbar')
    <style>
        body {
            background-color: #f8f9fa;
        }
        
        .table-container {
            border-radius: 12px;
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.1);
            background-color: white;
            padding: 20px;
            margin: 20px 0;
            overflow-x: auto;
            padding-bottom: 10px;
        }

        .table {
            margin-bottom: 0;
            background-color: white;
        }

        .weight-cell {
            font-size: 0.85em;
            color: #6c757d;
            display: block;
            margin-top: 2px;
        }

        .shop-header {
            background-color: #ffd700 !important; /* Gold color */
            color: #000;
            padding: 15px 10px;
            border-bottom: 2px solid #daa520; /* Darker gold */
        }

        .shop-name {
            font-size: 0.9em;
            color: #000;
            font-weight: 500;
        }

        .shop-id {
            font-size: 1.1em;
            font-weight: bold;
            color: #000;
        }

        .total-column {
            background-color: #fff3cd !important; /* Light gold */
            font-weight: 500;
        }

        .table td {
            vertical-align: middle;
            padding: 12px 8px;
        }

        .kind-cell {
            font-weight: 500;
            color: #495057;
            background-color: #f8f9fa;
        }

        .number-cell {
            font-family: 'Arial', sans-serif;
            font-size: 1.1em;
            color: #0d6efd; /* Bootstrap primary blue */
        }

        .footer-row {
            background: linear-gradient(45deg, #daa520, #ffd700) !important; /* Gold gradient */
            color: #000 !important;
        }

        .footer-row .weight-cell {
            color: #000 !important;
            opacity: 0.8;
        }

        .export-btn {
            background: linear-gradient(45deg, #28a745, #20c997);
            border: none;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .export-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }

        .table-hover tbody tr:hover {
            background-color: #fff3cd !important;
            transition: background-color 0.3s ease;
        }

        .page-title {
            color: #daa520;
            font-weight: bold;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }

        /* Zebra striping for rows */
        .table tbody tr:nth-of-type(odd) {
            background-color: #fcfcfc;
        }

        /* Custom styles for weight icons */
        .fa-weight-hanging {
            color: #daa520;
        }

        /* Responsive font sizes */
        @media (max-width: 768px) {
            .shop-id {
                font-size: 0.9em;
            }
            .shop-name {
                font-size: 0.8em;
            }
            .number-cell {
                font-size: 0.9em;
            }
        }

        /* Add this new style for empty cells */
        .empty-cell {
            background-color: #ffe6e6 !important; /* Light red background */
            transition: background-color 0.3s ease;
        }
        
        .empty-cell .number-cell {
            color: #dc3545 !important; /* Bootstrap danger color */
        }
        
        .empty-cell .weight-cell {
            color: #dc3545 !important;
            opacity: 0.7;
        }

        .empty-cell .fa-weight-hanging {
            color: #dc3545 !important;
        }

        /* Modify hover effect for empty cells */
        .table-hover tbody tr:hover .empty-cell {
            background-color: #ffd9d9 !important;
        }

        .shop-footer {
            background-color: #f8f9fa;
            border-top: 2px solid #daa520;
        }

        .shop-footer .shop-id {
            font-size: 0.9em;
            font-weight: bold;
            color: #000;
        }

        .shop-footer .shop-name {
            font-size: 0.8em;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-4 px-4">
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h2 class="page-title">
                    <i class="fas fa-chart-bar me-2"></i>Gold Items Analysis
                </h2>
                <form action="{{ route('gold-analysis.export') }}" method="GET">
                    <button type="submit" class="btn export-btn text-white">
                        <i class="fas fa-file-excel me-2"></i>Export to Excel
                    </button>
                </form>
            </div>
        </div>

        <div class="table-container">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th class="text-center align-middle kind-cell">Kind</th>
                        @foreach($shopStatistics as $shopData)
                            <th class="text-center shop-header">
                                <div class="shop-id">Shop #{{ $shopData['shop']->id }}</div>
                                <div class="shop-name">{{ $shopData['shop']->name }}</div>
                            </th>
                        @endforeach
                        <th class="text-center align-middle total-column">
                            <i class="fas fa-calculator me-1"></i>Total
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $allKinds = collect();
                        foreach($shopStatistics as $shopData) {
                            $allKinds = $allKinds->concat($shopData['statistics']->pluck('kind'));
                        }
                        $allKinds = $allKinds->unique()->sort();
                        $columnTotals = [];
                        $columnWeightTotals = [];
                    @endphp

                    @foreach($allKinds as $kind)
                        <tr>
                            <td class="kind-cell">{{ $kind }}</td>
                            @php
                                $rowTotal = 0;
                                $rowWeightTotal = 0;
                            @endphp

                            @foreach($shopStatistics as $shopId => $shopData)
                                @php
                                    $stat = $shopData['statistics']->firstWhere('kind', $kind);
                                    $items = $stat ? $stat->total_items : 0;
                                    $weight = $stat ? $stat->total_weight : 0;
                                    $rowTotal += $items;
                                    $rowWeightTotal += $weight;
                                    $columnTotals[$shopId] = ($columnTotals[$shopId] ?? 0) + $items;
                                    $columnWeightTotals[$shopId] = ($columnWeightTotals[$shopId] ?? 0) + $weight;
                                @endphp
                                
                                <td class="text-center {{ $items == 0 ? 'empty-cell' : '' }}">
                                    <span class="number-cell">{{ number_format($items, 0) }}</span>
                                    <span class="weight-cell">
                                        {{-- <i class="fas fa-weight-hanging me-1"></i> --}}
                                        {{ number_format($weight, 3) }}g
                                    </span>
                                </td>
                            @endforeach

                            <td class="text-center total-column">
                                <strong class="number-cell">{{ number_format($rowTotal, 0) }}</strong>
                                <span class="weight-cell">
                                    {{-- <i class="fas fa-weight-hanging me-1"></i> --}}
                                    <strong>{{ number_format($rowWeightTotal, 3) }}g</strong>
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="footer-row">
                        <td><strong>Total</strong></td>
                        @foreach($shopStatistics as $shopId => $shopData)
                            <td class="text-center">
                                <strong class="number-cell">{{ number_format($columnTotals[$shopId] ?? 0, 0) }}</strong>
                                <span class="weight-cell">
                                    <i class="fas fa-weight-hanging me-1"></i>
                                    <strong>{{ number_format($columnWeightTotals[$shopId] ?? 0, 3) }}g</strong>
                                </span>
                            </td>
                        @endforeach
                        <td class="text-center">
                            <strong class="number-cell">{{ number_format(array_sum($columnTotals), 0) }}</strong>
                            <span class="weight-cell">
                                <i class="fas fa-weight-hanging me-1"></i>
                                <strong>{{ number_format(array_sum($columnWeightTotals), 3) }}g</strong>
                            </span>
                        </td>
                    </tr>
                    <tr class="shop-footer">
                        <td></td>
                        @foreach($shopStatistics as $shopId => $shopData)
                            <td class="text-center">
                                <div class="shop-id">Shop #{{ $shopData['shop']->id }}</div>
                                <div class="shop-name">{{ $shopData['shop']->name }}</div>
                            </td>
                        @endforeach
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 