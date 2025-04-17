@include('components.navbar')

<style>
    .statistics-container {
        padding: 20px;
        background-color: #f8f9fa;
        border-radius: 15px;
    }

    .shop-header {
        text-align: center;
        margin-bottom: 30px;
        padding: 20px;
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        position: relative;
    }

    .shop-header h2 {
        margin-bottom: 15px;
        font-weight: bold;
    }

    .export-btn {
        position: absolute;
        top: 20px;
        right: 20px;
        padding: 10px 20px;
        font-weight: bold;
        border-radius: 30px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        background-color: #28a745;
        border-color: #28a745;
        color: white;
        text-decoration: none;
    }

    .export-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
        background-color: #218838;
        border-color: #1e7e34;
    }

    .export-btn i {
        margin-left: 8px;
    }

    .total-summary {
        display: flex;
        justify-content: center;
        gap: 30px;
    }

    .summary-item {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.1em;
    }

    .statistics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .stat-card {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-header {
        background: #f8f9fa;
        padding: 15px;
        text-align: center;
        border-bottom: 2px solid #007bff;
    }

    .stat-header h3 {
        margin: 0;
        color: #007bff;
        font-size: 1.2em;
        font-weight: bold;
    }

    .stat-body {
        padding: 20px;
    }

    .stat-item {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .stat-icon {
        width: 40px;
        height: 40px;
        background: #007bff;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
    }

    .stat-info {
        flex-grow: 1;
    }

    .stat-label {
        display: block;
        color: #6c757d;
        font-size: 0.9em;
        margin-bottom: 5px;
    }

    .stat-value {
        display: block;
        font-size: 1.2em;
        font-weight: bold;
        color: #212529;
    }

    @media (max-width: 768px) {
        .statistics-grid {
            grid-template-columns: 1fr;
        }

        .total-summary {
            flex-direction: column;
            gap: 15px;
        }
        
        .export-btn {
            position: static;
            margin: 10px auto 20px;
            display: block;
        }
    }
</style>

<div class="container mt-4">
    <div class="statistics-container">
        <div class="shop-header">
            <h2>{{ $shopName }} إحصائيات الجرد</h2>
            <a href="{{ route('items.statistics.export') }}" class="btn export-btn">
                تصدير إلى إكسل <i class="fas fa-file-excel"></i>
            </a>
            <div class="total-summary">
                <div class="summary-item">
                    <i class="fas fa-cubes"></i>
                    <span>إجمالي القطع: {{ $statistics->sum('total_items') }}</span>
                </div>
                <div class="summary-item">
                    <i class="fas fa-weight"></i>
                    <span>إجمالي الوزن: {{ number_format($statistics->sum('total_weight'), 3) }} جرام</span>
                </div>
            </div>
        </div>

        <div class="statistics-grid">
            @foreach($statistics as $stat)
                <div class="stat-card">
                    <div class="stat-header">
                        <h3>{{ $stat->kind }}</h3>
                    </div>
                    <div class="stat-body">
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-cube"></i>
                            </div>
                            <div class="stat-info">
                                <span class="stat-label">عدد القطع</span>
                                <span class="stat-value">{{ $stat->total_items }}</span>
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-balance-scale"></i>
                            </div>
                            <div class="stat-info">
                                <span class="stat-label">الوزن</span>
                                <span class="stat-value">{{ number_format($stat->total_weight, 3) }} جرام</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div> 