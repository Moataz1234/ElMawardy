<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weight Analysis Dashboard</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @include('components.navbar')
</head>
<body>
    <div class="dashboard-container">
        <!-- Popular Models -->
        <div class="table-container">
            <h2>Top Selling Models</h2>
            <table>
                <thead>
                    <tr>
                        <th>Model</th>
                        <th>Total Quantity</th>
                        <th>Total Weight</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($popularModels as $model)
                    <tr>
                        <td>{{ $model->model }}</td>
                        <td>{{ $model->total_quantity }}</td>
                        <td>{{ number_format($model->total_weight, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Weight Analysis Card -->
        <div class="card">
            <h2>Shop Weight Analysis</h2>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Shop Name</th>
                            <th>Weight Sold (g)</th>
                            <th>Weight in Stock (g)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($shopWeightAnalysis as $analysis)
                        <tr>
                            <td>{{ $analysis->shop_name }}</td>
                            <td>{{ number_format($analysis->total_weight_sold, 2) }}</td>
                            <td>{{ number_format($analysis->total_weight_inventory, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Weight Distribution Chart -->
        <div class="card">
            <h2>Weight Distribution by Shop</h2>
            <canvas id="weightDistributionChart"></canvas>
        </div>
    </div>

<style>
/* Your existing CSS remains the same */
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const weightCtx = document.getElementById('weightDistributionChart').getContext('2d');
    new Chart(weightCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($shopWeightAnalysis->pluck('shop_name')) !!},
            datasets: [{
                label: 'Weight Sold',
                data: {!! json_encode($shopWeightAnalysis->pluck('total_weight_sold')) !!},
                backgroundColor: '#1C4E80',
            }, {
                label: 'Weight in Stock',
                data: {!! json_encode($shopWeightAnalysis->pluck('total_weight_inventory')) !!},
                backgroundColor: '#A5D8DD',
            }]
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
                }
            },
            plugins: {
                legend: {
                    position: 'top'
                }
            }
        }
    });
});
const shopCtx = document.getElementById('shopWeightChart').getContext('2d');
const shopData = {!! json_encode($shopWeightAnalysis->groupBy('shop_name')->map(function($items) {
    return [
        'sold' => $items->sum('total_weight_sold'),
        'inventory' => $items->sum('total_weight_inventory')
    ];
})) !!};

new Chart(shopCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($shopWeightAnalysis->pluck('shop_name')->unique()) !!},
        datasets: [{
            label: 'Weight Sold',
            data: Object.values(shopData).map(d => d.sold),
            backgroundColor: '#1C4E80',
        }, {
            label: 'Weight in Stock',
            data: Object.values(shopData).map(d => d.inventory),
            backgroundColor: '#A5D8DD',
        }]
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
            }
        },
        plugins: {
            legend: {
                position: 'top'
            }
        }
    }
});
</script>
</body>
</html>