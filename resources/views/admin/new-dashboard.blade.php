<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Dashboard</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        .dashboard-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }
        .card {
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 10px;
            width: 300px;
            text-align: center;
        }
        .chart-container {
            width: 80%;
            margin: 20px auto;
        }
</head>
<body>
    <div class="dashboard-container">
        <div class="card">
            <h2>Total Weight Sold by Year and Shop</h2>
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
            <h2>Total Weight in Inventory</h2>
            <p>{{ number_format($totalWeightInventory, 2) }} g</p>
        </div>
        <div class="chart-container">
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
    </script>
</html>
