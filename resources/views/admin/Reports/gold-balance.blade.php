<!DOCTYPE html>
<html lang="en">
<head>
    @include('components.navbar')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gold Balance Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 20px;
            padding-bottom: 20px;
        }
        .card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .card-header {
            font-weight: 600;
        }
        .bg-primary {
            background-color: #0d6efd !important;
        }
        .text-white {
            color: #fff !important;
        }
        .total-row {
            font-weight: bold;
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h1 class="h4 mb-0">Gold Balance Report (All weights normalized to 18K)</h1>
            </div>
            <div class="card-body">
                <!-- Filter Form -->
                <form action="{{ route('gold-balance.report') }}" method="GET" class="mb-4">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="report_date" class="form-label">Report Date</label>
                            <input type="date" id="report_date" name="report_date" class="form-control" value="{{ $reportDate ? $reportDate->format('Y-m-d') : '' }}">
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" id="hide_inactive" name="hide_inactive" class="form-check-input" value="1" {{ request()->has('hide_inactive') ? 'checked' : '' }}>
                                <label for="hide_inactive" class="form-check-label">Hide inactive shops</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                        </div>
                    </div>
                </form>

                <!-- Month Filter Form -->
                <form action="{{ route('gold-balance.report') }}" method="GET" class="mb-4">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="month" class="form-label">Select Month</label>
                            <input type="month" id="month" name="month" class="form-control" value="{{ $selectedMonth ? $selectedMonth->format('Y-m') : '' }}">
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" id="hide_inactive_monthly" name="hide_inactive_monthly" class="form-check-input" value="1" {{ request()->has('hide_inactive_monthly') ? 'checked' : '' }}>
                                <label for="hide_inactive_monthly" class="form-check-label">Hide inactive shops</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">Apply Month Filter</button>
                        </div>
                    </div>
                </form>

                <!-- Tabs Navigation -->
                <ul class="nav nav-tabs mb-4" id="reportTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="daily-tab" data-bs-toggle="tab" data-bs-target="#daily" type="button" role="tab" aria-controls="daily" aria-selected="true">
                            Daily Report ({{ $reportDate->format('d M Y') }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="monthly-tab" data-bs-toggle="tab" data-bs-target="#monthly" type="button" role="tab" aria-controls="monthly" aria-selected="false">
                            Monthly Report ({{ $selectedMonth->format('F Y') }})
                        </button>
                    </li>
                </ul>

                <!-- Tabs Content -->
                <div class="tab-content" id="reportTabsContent">
                    <!-- Daily Report Tab -->
                    <div class="tab-pane fade show active" id="daily" role="tabpanel" aria-labelledby="daily-tab">
                        <!-- Date Title -->
                        <div class="alert alert-info mb-4">
                            <h5 class="mb-0">
                                Report Date: {{ $reportDate->format('d M Y') }}
                            </h5>
                        </div>

                        <!-- Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card text-center h-100 bg-light">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Buy</h5>
                                        <p class="card-text fs-2 fw-bold text-success">{{ number_format($totalBoughtWeight, 2) }} g</p>
                                        <p class="text-muted small">Normalized to 18K</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-center h-100 bg-light">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Sell</h5>
                                        <p class="card-text fs-2 fw-bold text-danger">{{ number_format($totalSoldWeight, 2) }} g</p>
                                        <p class="text-muted small">Normalized to 18K</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-center h-100 bg-light">
                                    <div class="card-body">
                                        <h5 class="card-title">Balance</h5>
                                        <p class="card-text fs-2 fw-bold {{ $balance >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($balance, 2) }} g
                                        </p>
                                        <p class="text-muted small">Bought - Sold</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Shop-Based Report Table -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Daily Shop Performance Report (18K Equivalent)</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-primary">
                                            <tr>
                                                <th>Shop Name</th>
                                                <th class="text-end">Bought Weight (g)</th>
                                                <th class="text-end">Sold Weight (g)</th>
                                                <th class="text-end">Shop Difference (g)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($shopReportData as $shop => $data)
                                                @if($shop !== 'Total')
                                                    <tr class="{{ ($data['sold'] == 0 && $data['bought'] == 0) ? 'text-muted' : '' }}">
                                                        <td>{{ $shop }}</td>
                                                        <td class="text-end">{{ number_format($data['bought'], 2) }}</td>
                                                        <td class="text-end">{{ number_format($data['sold'], 2) }}</td>
                                                        <td class="text-end {{ $data['shop_balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                            {{ number_format($data['shop_balance'], 2) }}
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="total-row">
                                                <td><strong>Total</strong></td>
                                                <td class="text-end"><strong>{{ number_format($shopReportData['Total']['bought'], 2) }}</strong></td>
                                                <td class="text-end"><strong>{{ number_format($shopReportData['Total']['sold'], 2) }}</strong></td>
                                                <td class="text-end {{ $shopReportData['Total']['shop_balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                    <strong>{{ number_format($shopReportData['Total']['shop_balance'], 2) }}</strong>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Daily Purity Tables -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h5 class="mb-0">Daily Sold Weight by Purity</h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Purity</th>
                                                    <th>Original Weight (g)</th>
                                                    <th>18K Equivalent (g)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($soldWeightByPurity as $purity => $weight)
                                                    @php
                                                        $purityValue = (int) preg_replace('/[^0-9]/', '', $purity);
                                                        $normalizedWeight = $weight * ($purityValue / 18);
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $purity }}</td>
                                                        <td>{{ number_format($weight, 2) }}</td>
                                                        <td>{{ number_format($normalizedWeight, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h5 class="mb-0">Daily Bought Weight by Purity (Kasr)</h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Purity</th>
                                                    <th>Original Weight (g)</th>
                                                    <th>18K Equivalent (g)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($boughtWeightByPurity as $purity => $weight)
                                                    @php
                                                        $purityValue = (int) preg_replace('/[^0-9]/', '', $purity);
                                                        $normalizedWeight = $weight * ($purityValue / 18);
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $purity }}</td>
                                                        <td>{{ number_format($weight, 2) }}</td>
                                                        <td>{{ number_format($normalizedWeight, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Report Tab -->
                    <div class="tab-pane fade" id="monthly" role="tabpanel" aria-labelledby="monthly-tab">
                        <!-- Monthly Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card text-center h-100 bg-light">
                                    <div class="card-body">
                                        <h5 class="card-title">Monthly Total Buy</h5>
                                        <p class="card-text fs-2 fw-bold text-success">{{ number_format($monthlyReportData['total_bought'], 2) }} g</p>
                                        <p class="text-muted small">Normalized to 18K</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-center h-100 bg-light">
                                    <div class="card-body">
                                        <h5 class="card-title">Monthly Total Sell</h5>
                                        <p class="card-text fs-2 fw-bold text-danger">{{ number_format($monthlyReportData['total_sold'], 2) }} g</p>
                                        <p class="text-muted small">Normalized to 18K</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-center h-100 bg-light">
                                    <div class="card-body">
                                        <h5 class="card-title">Monthly Balance</h5>
                                        <p class="card-text fs-2 fw-bold {{ $monthlyReportData['total_balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($monthlyReportData['total_balance'], 2) }} g
                                        </p>
                                        <p class="text-muted small">Bought - Sold</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Monthly Shop Performance Table -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Monthly Shop Performance Report (18K Equivalent)</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-primary">
                                            <tr>
                                                <th>Shop Name</th>
                                                <th class="text-end">Monthly Bought Weight (g)</th>
                                                <th class="text-end">Monthly Sold Weight (g)</th>
                                                <th class="text-end">Monthly Difference (g)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($monthlyReportData['shop_data'] as $shop => $data)
                                                @if($shop !== 'Total')
                                                    <tr class="{{ ($data['sold'] == 0 && $data['bought'] == 0) ? 'text-muted' : '' }}">
                                                        <td>{{ $shop }}</td>
                                                        <td class="text-end">{{ number_format($data['bought'], 2) }}</td>
                                                        <td class="text-end">{{ number_format($data['sold'], 2) }}</td>
                                                        <td class="text-end {{ $data['balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                            {{ number_format($data['balance'], 2) }}
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="total-row">
                                                <td><strong>Total</strong></td>
                                                <td class="text-end"><strong>{{ number_format($monthlyReportData['shop_data']['Total']['bought'], 2) }}</strong></td>
                                                <td class="text-end"><strong>{{ number_format($monthlyReportData['shop_data']['Total']['sold'], 2) }}</strong></td>
                                                <td class="text-end {{ $monthlyReportData['shop_data']['Total']['balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                    <strong>{{ number_format($monthlyReportData['shop_data']['Total']['balance'], 2) }}</strong>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Monthly Purity Tables -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h5 class="mb-0">Monthly Sold Weight by Purity</h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Purity</th>
                                                    <th>Original Weight (g)</th>
                                                    <th>18K Equivalent (g)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($monthlyReportData['sold_by_purity'] as $purity => $weight)
                                                    @php
                                                        $purityValue = (int) preg_replace('/[^0-9]/', '', $purity);
                                                        $normalizedWeight = $weight * ($purityValue / 18);
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $purity }}</td>
                                                        <td>{{ number_format($weight, 2) }}</td>
                                                        <td>{{ number_format($normalizedWeight, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h5 class="mb-0">Monthly Bought Weight by Purity (Kasr)</h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Purity</th>
                                                    <th>Original Weight (g)</th>
                                                    <th>18K Equivalent (g)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($monthlyReportData['bought_by_purity'] as $purity => $weight)
                                                    @php
                                                        $purityValue = (int) preg_replace('/[^0-9]/', '', $purity);
                                                        $normalizedWeight = $weight * ($purityValue / 18);
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $purity }}</td>
                                                        <td>{{ number_format($weight, 2) }}</td>
                                                        <td>{{ number_format($normalizedWeight, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('monthlyChart').getContext('2d');
            
            // Prepare data from PHP
            const monthlyData = @json($monthlyData);
            
            const labels = monthlyData.map(item => item.date);
            const soldData = monthlyData.map(item => item.sold);
            const boughtData = monthlyData.map(item => item.bought);
            const balanceData = monthlyData.map(item => item.balance);
            
            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Sold Weight (18K)',
                            data: soldData,
                            backgroundColor: 'rgba(75, 192, 192, 0.5)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Bought Weight (18K)',
                            data: boughtData,
                            backgroundColor: 'rgba(255, 99, 132, 0.5)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Balance',
                            data: balanceData,
                            type: 'line',
                            fill: false,
                            backgroundColor: 'rgba(54, 162, 235, 0.5)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 2,
                            tension: 0.1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Weight (g)'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Month'
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>