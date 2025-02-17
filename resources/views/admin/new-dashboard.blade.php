<!DOCTYPE html>
<html lang="en">
<head>
    @include('components.navbar')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Gold Inventory Management</title>
    <!-- Add Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
    <link href="{{ asset('css/navbar.css') }}" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container-fluid py-4">
        <div class="row g-4">
            <div class="dashboard-grid">
                <!-- Weight Distribution Card -->
                <div class="dashboard-card section-primary">
                    <div class="card-header">
                        <h3>Weight Distribution</h3>
                        <div class="total-weight">Total: {{ number_format($totalWeightInventory, 2) }}g</div>
                    </div>
                    <div class="chart-container">
                        <canvas id="shopWeightChart"></canvas>
                    </div>
                </div>

                <!-- Inventory by Category -->
                {{-- <div class="dashboard-card section-success">
                    <div class="card-header">
                        <h3>Category Distribution</h3>
                        <div class="subtitle">Items by Category</div>
                    </div>
                    <div class="chart-container">
                        <canvas id="categoryChart"></canvas>
                        <div class="metric-value">{{ $kindInventory->sum('total_items') }}</div>
                    </div>
                </div> --}}

                <!-- Sales Analysis -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>Sales Performance</h3>
                    </div>
                    <div class="chart-container">
                        <canvas id="salesChart"></canvas>
                        <div class="metric-value">{{ $kindSalesAnalysis->sum('total_sold') }}</div>
                    </div>
                </div>

                <!-- Monthly Trends -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>Monthly Sales Trends</h3>
                    </div>
                    <div class="chart-container">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>

                <!-- Inventory Turnover -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>Inventory Turnover Analysis</h3>
                        <div class="subtitle">Annual Performance by Shop</div>
                    </div>
                    <div class="chart-container">
                        <canvas id="turnoverChart"></canvas>
                    </div>
                    <div class="turnover-legend mt-3">
                        <div class="row">
                            <div class="col">
                                <h6>Efficiency Rating:</h6>
                                <ul class="list-unstyled">
                                    <li><span class="badge bg-success">Excellent</span> ≥ 4.0</li>
                                    <li><span class="badge bg-info">Good</span> ≥ 3.0</li>
                                    <li><span class="badge bg-warning">Average</span> ≥ 2.0</li>
                                    <li><span class="badge bg-danger">Needs Improvement</span> < 2.0</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Performing Items -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>Top Performers</h3>
                    </div>
                    <div class="chart-container">
                        <canvas id="topItemsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Define a larger color palette with direct color values
            const chartColors = [
                '#4f46e5',    // Indigo
                '#10b981',    // Emerald
                '#f97316',    // Orange
                '#3b82f6',    // Blue
                '#8b5cf6',    // Purple
                '#ec4899',    // Pink
                '#14b8a6',    // Teal
                '#6366f1',    // Lighter Indigo
                '#84cc16',    // Lime
                '#06b6d4',    // Cyan
                '#f59e0b',    // Amber
                '#ef4444'     // Red
            ];

            // Common chart options
            const commonChartOptions = {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            font: {
                                size: 12  // Restored normal font size
                            },
                            boxWidth: 10
                        }
                    },
                    tooltip: {
                        titleFont: {
                            size: 14
                        },
                        bodyFont: {
                            size: 13
                        },
                        padding: 12
                    }
                }
            };

            // Doughnut chart specific options
            const doughnutOptions = {
                ...commonChartOptions,
                cutout: '70%',
                plugins: {
                    ...commonChartOptions.plugins,
                    tooltip: {
                        ...commonChartOptions.plugins.tooltip,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value.toFixed(2)}g (${percentage}%)`;
                            }
                        }
                    }
                }
            };

            // Bar chart specific options
            const barOptions = {
                ...commonChartOptions,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: {
                                size: 12
                            }
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 12
                            }
                        }
                    }
                }
            };

            // Shop Weight Chart with adjusted settings
            new Chart(document.getElementById('shopWeightChart').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: @json(array_keys(reset($totalWeightSoldByYearAndShop))),
                    datasets: [{
                        data: @json(array_values(reset($totalWeightSoldByYearAndShop))),
                        backgroundColor: chartColors,
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: doughnutOptions
            });

            // Category Distribution Chart
            // new Chart(document.getElementById('categoryChart').getContext('2d'), {
            //     type: 'doughnut',
            //     data: {
            //         labels: @json($kindInventory->pluck('kind')),
            //         datasets: [{
            //             data: @json($kindInventory->pluck('total_items')),
            //             backgroundColor: chartColors,
            //             borderWidth: 2,
            //             borderColor: '#ffffff'
            //         }]
            //     },
            //     options: doughnutOptions
            // });

            // Sales Performance Chart
            new Chart(document.getElementById('salesChart').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: @json($kindSalesAnalysis->pluck('kind')),
                    datasets: [{
                        data: @json($kindSalesAnalysis->pluck('total_sold')),
                        backgroundColor: chartColors,
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: doughnutOptions
            });

            // Monthly Trends Chart
            new Chart(document.getElementById('trendChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: @json(array_column($monthlyTrends, 'month')),
                    datasets: [{
                        label: 'Sales Count',
                        data: @json(array_column($monthlyTrends, 'sales')),
                        borderColor: '#4f46e5',
                        backgroundColor: '#4f46e520',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Total Weight (g)',
                        data: @json(array_column($monthlyTrends, 'weight')),
                        borderColor: '#10b981',
                        backgroundColor: '#10b98120',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Inventory Turnover Chart
            const turnoverData = @json($turnoverRates);
            new Chart(document.getElementById('turnoverChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: Object.keys(turnoverData),
                    datasets: [{
                        label: 'Turnover Rate',
                        data: Object.values(turnoverData).map(item => item.rate),
                        backgroundColor: Object.values(turnoverData).map(item => {
                            switch(item.efficiency) {
                                case 'Excellent': return '#10b981';
                                case 'Good': return '#3b82f6';
                                case 'Average': return '#f97316';
                                default: return '#ef4444';
                            }
                        }),
                        borderWidth: 1
                    }]
                },
                options: barOptions
            });

            // Top Performers Chart
            new Chart(document.getElementById('topItemsChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: @json($topPerformers->pluck('model')),
                    datasets: [{
                        label: 'Items Sold',
                        data: @json($topPerformers->pluck('total_sold')),
                        backgroundColor: chartColors.slice(0, 10),
                        borderWidth: 1,
                        borderColor: '#ffffff'
                    }, {
                        label: 'Total Weight (g)',
                        data: @json($topPerformers->pluck('total_weight')),
                        backgroundColor: chartColors.slice(0, 10).map(color => color + '80'),
                        borderWidth: 1,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    indexAxis: 'y',  // This makes it a horizontal bar chart
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.dataset.label || '';
                                    const value = context.parsed.x || 0;
                                    return `${label}: ${value.toFixed(2)}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
