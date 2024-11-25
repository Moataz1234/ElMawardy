<!DOCTYPE html>
<html lang="en">
<head>
    @include('components.navbar')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Dashboard</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <div class="card">
            <h3>Total Weight Sold by Year and Shop</h3>
            @foreach($totalWeightSoldByYearAndShop as $year => $shops)
                <h3>{{ $year }}</h3>
                <ul>
                    @foreach($shops as $shopName => $weight)
                        <li>{{ $shopName }}: {{ number_format($weight, 2) }} g</li>
                    @endforeach
                </ul>
            @endforeach
        </div>
        <div class="card">
            <h3>Total Weight in Inventory</h3   >
            <p>{{ number_format($totalWeightInventory, 2) }} g</p>
        </div>
        <div class="card">
            <h3>Sales Trends Over Time</h3>
            <canvas id="salesTrendsChart"></canvas>
        </div>
        <div class="card">
            <h3>Top Selling Items</h3>
            <ul>
                @foreach($topSellingItems as $item)
                    <li>{{ $item->model }}: {{ $item->total_quantity }} sold</li>
                @endforeach
            </ul>
        </div>
        <div class="card">
            <h3>Inventory Turnover Ratio</h3>
            <p>{{ number_format($inventoryTurnover, 2) }}</p>
        </div>
            <canvas id="weightChart"></canvas>
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
