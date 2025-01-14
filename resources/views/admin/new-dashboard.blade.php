<!DOCTYPE html>
<html lang="en">
<head>
    @include('components.navbar')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Gold Inventory Management</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
    <link href="{{ asset('css/navbar.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <div class="dashboard-grid">
        <!-- Total Weight Sold Card -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3>Total Weight Sold by Year and Shop</h3>
            </div>
            @foreach($totalWeightSoldByYearAndShop as $year => $shops)
                <div class="year-section mb-4">
                    <h4 class="font-medium text-gray-700 mb-2">{{ $year }}</h4>
                    <div class="space-y-2">
                        @foreach($shops as $shopName => $weight)
                            <div class="list-item">
                                <span>{{ $shopName }}</span>
                                <span class="font-medium">{{ number_format($weight, 2) }} g</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Inventory Metrics Card -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3>Inventory Overview</h3>
            </div>
            <div class="metric-value">
                {{ number_format($totalWeightInventory, 2) }} g
            </div>
            <div class="metric-label">Total Weight in Inventory</div>
            
            <div class="mt-6">
                <div class="metric-value">
                    {{ number_format($inventoryTurnover, 2) }}
                </div>
                <div class="metric-label">Inventory Turnover Ratio</div>
            </div>
        </div>

        <!-- Sales Trends Card -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3>Sales Trends</h3>
            </div>
            <div class="chart-container">
                <canvas id="salesTrendsChart"></canvas>
            </div>
        </div>

        <!-- Top Selling Items Card -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3>Top Selling Items</h3>
            </div>
            <div class="space-y-2">
                @foreach($topSellingItems as $item)
                    <div class="list-item">
                        <span>{{ $item->model }}</span>
                        <span class="badge">{{ $item->total_quantity }} sold</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('weightChart').getContext('2d');
            const data = {
                labels: @json(array_keys($totalWeightSoldByYearAndShop)),
                datasets: [
                    @foreach($totalWeightSoldByYearAndShop as $year => $shops)
                    {
                        label: '{{ $year }}',
                        data: @json(array_values($shops)),
                        backgroundColor: 'rgba({{ rand(0, 255) }}, {{ rand(0, 255) }}, {{ rand(0, 255) }}, 0.5)',
                    },
                    @endforeach
                ]
            };

            const config = {
                type: 'bar',
                data: data,
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            stacked: true,
                        },
                        y: {
                            beginAtZero: true,
                            stacked: true,
                            title: {
                                display: true,
                                text: 'Weight (g)'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Total Weight Sold by Year and Shop'
                        }
                    }
                }
            };

            new Chart(ctx, config);
        });
        const salesTrendsCtx = document.getElementById('salesTrendsChart').getContext('2d');
        new Chart(salesTrendsCtx, {
            type: 'line',
            data: {
                labels: @json(array_keys($salesTrends)),
                datasets: [{
                    label: 'Total Weight Sold',
                    data: @json(array_values($salesTrends)),
                    borderColor: 'rgba(75, 192, 192, 1)',
                    fill: false,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Month'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Weight (g)'
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Sales Trends Over Time'
                    }
                }
            }
        });
    </script>
</body>
</html>
