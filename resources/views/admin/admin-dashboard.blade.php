<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Dashboard</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @include('components.navbar')
</head>
<body>
    <div class="dashboard-container">
        <!-- Summary Stats -->
        <div class="stats-grid">
            @foreach($todayStats as $stat)
            <div class="stats-card">
                <div class="kpi-label">{{ $stat->shop_name }}</div>
                <div class="kpi-value">{{ $stat->total_items }}</div>
                <div class="kpi-label">Items Sold Today</div>
                <div class="trend-up">↑ 12% from yesterday</div>
            </div>
            @endforeach
        </div>

        <!-- Popular Models -->
        <div class="table-container">
            <h2>Top Selling Models</h2>
            <table>
                <thead>
                    <tr>
                        <th>Model</th>
                        <th>Times Sold</th>
                        <th>Total Quantity</th>
                        <th>Trend</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($popularModels as $model)
                    <tr>
                        <td>{{ $model->model }}</td>
                        <td>{{ $model->sold_count }}</td>
                        <td>{{ $model->total_quantity }}</td>
                        <td class="trend-up">↑ 8%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Sales Chart -->
        <div class="chart-container">
            <h2>Sales by Category</h2>
            <canvas id="salesChart"></canvas>
        </div>
    </div>
    <div class="analysis-section">
        <!-- Weight Analysis Card -->
        <div class="card">
            <h2>Weight Analysis</h2>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Weight Sold (g)</th>
                            <th>Weight in Stock (g)</th>
                            <th>Items Sold</th>
                            <th>Items in Stock</th>
                            <th>Stock Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($weightAnalysis as $analysis)
                            @php
                                $inventoryItem = $inventoryAnalysis->firstWhere('kind', $analysis->kind);
                                $stockRatio = $inventoryItem 
                                    ? ($analysis->total_weight_sold / $inventoryItem->total_weight_inventory) * 100 
                                    : 0;
                            @endphp
                            <tr>
                                <td>{{ $analysis->kind }}</td>
                                <td>{{ number_format($analysis->total_weight_sold, 2) }}</td>
                                <td>{{ $inventoryItem ? number_format($inventoryItem->total_weight_inventory, 2) : 0 }}</td>
                                <td>{{ $analysis->items_count }}</td>
                                <td>{{ $inventoryItem ? $inventoryItem->items_in_stock : 0 }}</td>
                                <td>
                                    <div class="stock-status" style="background-color: 
                                        {{ $stockRatio > 80 ? '#ff4444' : ($stockRatio > 50 ? '#ffbb33' : '#00C851') }}">
                                        {{ $stockRatio > 80 ? 'Low' : ($stockRatio > 50 ? 'Medium' : 'Good') }}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    
        <!-- Sales Comparison Card -->
        <div class="card">
            <h2>Sales Comparison</h2>
            <div class="sales-metrics">
                <div class="metric">
                    <span class="metric-label">Today's Sales</span>
                    <span class="metric-value">{{ number_format($todaySales, 2) }}</span>
                </div>
                <div class="metric">
                    <span class="metric-label">Yesterday's Sales</span>
                    <span class="metric-value">{{ number_format($yesterdaySales, 2) }}</span>
                </div>
                <div class="metric">
                    <span class="metric-label">Change</span>
                    <span class="metric-value {{ $percentChange >= 0 ? 'positive' : 'negative' }}">
                        {{ number_format($percentChange, 1) }}%
                    </span>
                </div>
            </div>
        </div>
    
        <!-- Weight Distribution Chart -->
        <div class="card">
            <h2>Weight Distribution</h2>
            <canvas id="weightDistributionChart"></canvas>
        </div>
    </div>
<style>
 /* dashboard.css */
 .analysis-section {
    margin-top: 2rem;
    display: grid;
    gap: 1.5rem;
}

.stock-status {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    color: white;
    text-align: center;
    font-weight: 500;
}

.sales-metrics {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    padding: 1rem;
}

.metric {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 1rem;
    background-color: #f8f9fa;
    border-radius: 8px;
}

.metric-label {
    font-size: 0.875rem;
    color: #6c757d;
    margin-bottom: 0.5rem;
}

.metric-value {
    font-size: 1.5rem;
    font-weight: 600;
}

.positive {
    color: #00C851;
}

.negative {
    color: #ff4444;
}
:root {
    /* Professional Color Palette */
    --primary: #1C4E80;
    --secondary: #A5D8DD;
    --accent: #EA6A47;
    --success: #6AB187;
    --warning: #FFB400;
    --danger: #D32D41;
    --background: #F6F8FA;
    --card-bg: #FFFFFF;
    --text-primary: #2D3748;
    --text-secondary: #718096;
    --border: #E2E8F0;
}

body {
    background-color: var(--background);
    font-family: 'Inter', sans-serif;
    color: var(--text-primary);
    line-height: 1.5;
}

/* Dashboard Layout */
.dashboard-container {
    padding: 2rem;
    max-width: 1440px;
    margin: 0 auto;
}

/* Stats Cards Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stats-card {
    background: var(--card-bg);
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    transition: transform 0.2s;
}

.stats-card:hover {
    transform: translateY(-2px);
}

/* Tables */
.table-container {
    background: var(--card-bg);
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

th {
    background-color: var(--primary);
    color: white;
    font-weight: 600;
    padding: 1rem;
    text-align: left;
}

td {
    padding: 1rem;
    border-bottom: 1px solid var(--border);
}

/* Charts */
.chart-container {
    background: var(--card-bg);
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

/* KPI Values */
.kpi-value {
    font-size: 2rem;
    font-weight: 700;
    color: var(--primary);
    margin-bottom: 0.5rem;
}

.kpi-label {
    color: var(--text-secondary);
    font-size: 0.875rem;
}

/* Trends */
.trend-up {
    color: var(--success);
}

.trend-down {
    color: var(--danger);
}
</style>
<script>
    
    // Initialize charts with professional styling
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($salesByCategory->pluck('kind')) !!},
            datasets: [{
                label: 'Sales Volume',
                data: {!! json_encode($salesByCategory->pluck('total_quantity')) !!},
                backgroundColor: '#1C4E80',
                borderColor: '#1C4E80',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Sales Distribution by Category'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#E2E8F0'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
    const weightCtx = document.getElementById('weightDistributionChart').getContext('2d');
new Chart(weightCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($weightAnalysis->pluck('kind')) !!},
        datasets: [{
            label: 'Weight Sold',
            data: {!! json_encode($weightAnalysis->pluck('total_weight_sold')) !!},
            backgroundColor: '#1C4E80',
        }, {
            label: 'Weight in Stock',
            data: {!! json_encode($inventoryAnalysis->pluck('total_weight_inventory')) !!},
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