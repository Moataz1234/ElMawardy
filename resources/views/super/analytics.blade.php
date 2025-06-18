<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard - Super User</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- BoxIcons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .analytics-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 1.5rem;
        }
        .summary-card {
            text-align: center;
            padding: 1.5rem;
            border-radius: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            margin-bottom: 1rem;
        }
        .summary-card h3 {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .summary-card p {
            margin: 0;
            opacity: 0.9;
        }
        .progress-custom {
            height: 8px;
            border-radius: 4px;
            background-color: #e9ecef;
        }
        .chart-container {
            position: relative;
            height: 400px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    @include('components.navbar')

    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="fw-bold text-dark">Analytics Dashboard</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('super.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item active">Analytics</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="summary-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h3>{{ $analytics['monthly_sales']->sum('count') }}</h3>
                    <p>Total Sales This Year</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <h3>{{ number_format($analytics['monthly_sales']->sum('total'), 2) }}</h3>
                    <p>Total Revenue This Year</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <h3>{{ $analytics['shop_performance']->count() }}</h3>
                    <p>Active Shops</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                    <h3>{{ $analytics['top_models']->count() }}</h3>
                    <p>Active Models</p>
                </div>
            </div>
        </div>

        <!-- Monthly Sales Chart -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card analytics-card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-line-chart me-2"></i>Monthly Sales Overview {{ date('Y') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="monthlySalesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shop Performance & Top Models -->
        <div class="row">
            <div class="col-xl-6">
                <div class="card analytics-card">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-store me-2"></i>Shop Performance
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Shop Name</th>
                                        <th>Users</th>
                                        <th>Orders</th>
                                        <th>Performance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($analytics['shop_performance'] as $shop)
                                    <tr>
                                        <td>
                                            <strong>{{ $shop->name }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $shop->users_count }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">{{ $shop->orders_count }}</span>
                                        </td>
                                        <td>
                                            <div class="progress progress-custom">
                                                <div class="progress-bar bg-success" 
                                                     style="width: {{ min(($shop->orders_count / max($analytics['shop_performance']->max('orders_count'), 1)) * 100, 100) }}%">
                                                </div>
                                            </div>
                                            <small class="text-muted">
                                                {{ number_format(($shop->orders_count / max($analytics['shop_performance']->sum('orders_count'), 1)) * 100, 1) }}%
                                            </small>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Models -->
            <div class="col-xl-6">
                <div class="card analytics-card">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-crown me-2"></i>Top 10 Models
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Model</th>
                                        <th>Items Count</th>
                                        <th>Popularity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($analytics['top_models'] as $index => $model)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($index < 3)
                                                    <i class="bx {{ $index == 0 ? 'bx-trophy text-warning' : ($index == 1 ? 'bx-medal text-secondary' : 'bx-award text-warning') }} me-2"></i>
                                                @endif
                                                <strong>{{ $model->model }}</strong>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $model->gold_items_count }}</span>
                                        </td>
                                        <td>
                                            <div class="progress progress-custom">
                                                <div class="progress-bar bg-info" 
                                                     style="width: {{ min(($model->gold_items_count / max($analytics['top_models']->max('gold_items_count'), 1)) * 100, 100) }}%">
                                                </div>
                                            </div>
                                            <small class="text-muted">
                                                {{ number_format(($model->gold_items_count / max($analytics['top_models']->sum('gold_items_count'), 1)) * 100, 1) }}%
                                            </small>
                                        </td>
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Monthly Sales Chart
        const ctx = document.getElementById('monthlySalesChart').getContext('2d');
        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        
        const salesData = @json($analytics['monthly_sales']);
        const chartData = Array(12).fill(0);
        const revenueData = Array(12).fill(0);
        
        salesData.forEach(item => {
            chartData[item.month - 1] = item.count;
            revenueData[item.month - 1] = item.total;
        });

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: monthNames,
                datasets: [{
                    label: 'Sales Count',
                    data: chartData,
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#667eea',
                    pointBorderColor: '#667eea',
                    pointRadius: 6,
                    pointHoverRadius: 8
                }, {
                    label: 'Revenue ($)',
                    data: revenueData,
                    borderColor: '#f093fb',
                    backgroundColor: 'rgba(240, 147, 251, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#f093fb',
                    pointBorderColor: '#f093fb',
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Sales Count'
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Revenue ($)'
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                    },
                    x: {
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    }
                }
            }
        });
    });
    </script>
</body>
</html> 