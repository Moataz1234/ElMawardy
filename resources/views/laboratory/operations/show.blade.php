<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفاصيل العملية</title>
    
    <!-- Bootstrap RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar {
            margin-bottom: 20px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 20px;
        }
        .info-box {
            background-color: #fff;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white px-3">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">نظام الذهب</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">المعمل</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">تفاصيل العملية #{{ $operation->operation_number }}</h5>
                        <div>
                            @if($operation->status === 'active')
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#weightModal">
                                    <i class="fas fa-weight"></i> إضافة الأوزان
                                </button>
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#costModal">
                                    <i class="fas fa-money-bill"></i> إضافة التكاليف
                                </button>
                                <a href="{{ route('laboratory.operations.edit', $operation) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> تعديل
                                </a>
                            @endif
                            <a href="{{ route('laboratory.operations.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-right"></i> عودة للقائمة
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Operation Info -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="info-box">
                                    <strong>التاريخ:</strong> {{ $operation->operation_date->format('Y-m-d') }}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <strong>وزن الفضة:</strong> {{ number_format($operation->silver_weight, 3) }} جم
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong>المصروفات:</strong> {{ number_format(session('total_cost', 0), 2) }} ريال
                                        </div>
                                        <div class="col-md-4">
                                            <strong>الإيرادات:</strong> {{ number_format(session('total_earn', 0), 2) }} ريال
                                        </div>
                                        <div class="col-md-4">
                                            <strong>صافي الربح:</strong> {{ number_format($operation->operation_cost, 2) }} ريال
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Inputs and Outputs Section -->
                        <div class="row">
                            <!-- Inputs -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">التسليمات</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>الوزن</th>
                                                        <th>العيار</th>
                                                        <th>عيار 18</th>
                                                        <th>عيار 21</th>
                                                        <th>عيار 24</th>
                                                        <th>التاريخ</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $totalInput = 0;
                                                        $totalInput18 = 0;
                                                        $totalInput21 = 0;
                                                        $totalInput24 = 0;
                                                    @endphp
                                                    @foreach($operation->inputs()->orderBy('created_at', 'asc')->get() as $index => $input)
                                                    @php
                                                        $weight = $input->weight;
                                                        $purity = $input->purity;
                                                        $k18 = ($weight * $purity) / 750;
                                                        $k21 = ($weight * $purity) / 875;
                                                        $k24 = ($weight * $purity) / 1000;
                                                        
                                                        $totalInput += $weight;
                                                        $totalInput18 += $k18;
                                                        $totalInput21 += $k21;
                                                        $totalInput24 += $k24;
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ number_format($weight, 3) }} جم</td>
                                                        <td>{{ $purity }}</td>
                                                        <td>{{ number_format($k18, 3) }} جم</td>
                                                        <td>{{ number_format($k21, 3) }} جم</td>
                                                        <td>{{ number_format($k24, 3) }} جم</td>
                                                        <td>{{ $input->input_date->format('Y-m-d') }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot class="table-light">
                                                    <tr>
                                                        <th>الإجمالي</th>
                                                        <th>{{ number_format($totalInput, 3) }} جم</th>
                                                        <th></th>
                                                        <th>{{ number_format($totalInput18, 3) }} جم</th>
                                                        <th>{{ number_format($totalInput21, 3) }} جم</th>
                                                        <th>{{ number_format($totalInput24, 3) }} جم</th>
                                                        <th></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Outputs -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">الاستلامات</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>الوزن</th>
                                                        <th>العيار</th>
                                                        <th>عيار 18</th>
                                                        <th>عيار 21</th>
                                                        <th>عيار 24</th>
                                                        <th>التاريخ</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $totalOutput = 0;
                                                        $totalOutput18 = 0;
                                                        $totalOutput21 = 0;
                                                        $totalOutput24 = 0;
                                                    @endphp
                                                    @foreach($operation->outputs()->orderBy('created_at', 'asc')->get() as $index => $output)
                                                    @php
                                                        $weight = $output->weight;
                                                        $purity = $output->purity;
                                                        $k18 = ($weight * $purity) / 750;
                                                        $k21 = ($weight * $purity) / 875;
                                                        $k24 = ($weight * $purity) / 1000;
                                                        
                                                        $totalOutput += $weight;
                                                        $totalOutput18 += $k18;
                                                        $totalOutput21 += $k21;
                                                        $totalOutput24 += $k24;
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ number_format($weight, 3) }} جم</td>
                                                        <td>{{ $purity }}</td>
                                                        <td>{{ number_format($k18, 3) }} جم</td>
                                                        <td>{{ number_format($k21, 3) }} جم</td>
                                                        <td>{{ number_format($k24, 3) }} جم</td>
                                                        <td>{{ $output->output_date->format('Y-m-d') }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot class="table-light">
                                                    <tr>
                                                        <th>الإجمالي</th>
                                                        <th>{{ number_format($totalOutput, 3) }} جم</th>
                                                        <th></th>
                                                        <th>{{ number_format($totalOutput18, 3) }} جم</th>
                                                        <th>{{ number_format($totalOutput21, 3) }} جم</th>
                                                        <th>{{ number_format($totalOutput24, 3) }} جم</th>
                                                        <th></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Differences Summary -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="mb-0">ملخص الفروقات</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>البيان</th>
                                                <th>الوزن الأساسي</th>
                                                <th>عيار 18</th>
                                                <th>عيار 21</th>
                                                <th>عيار 24</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="table-success">
                                                <th>إجمالي التسليمات</th>
                                                <td>{{ number_format($totalInput, 3) }} جم</td>
                                                <td>{{ number_format($totalInput18, 3) }} جم</td>
                                                <td>{{ number_format($totalInput21, 3) }} جم</td>
                                                <td>{{ number_format($totalInput24, 3) }} جم</td>
                                            </tr>
                                            <tr class="table-warning">
                                                <th>إجمالي الاستلامات</th>
                                                <td>{{ number_format($totalOutput, 3) }} جم</td>
                                                <td>{{ number_format($totalOutput18, 3) }} جم</td>
                                                <td>{{ number_format($totalOutput21, 3) }} جم</td>
                                                <td>{{ number_format($totalOutput24, 3) }} جم</td>
                                            </tr>
                                            <tr class="table-info">
                                                <th>الفرق</th>
                                                <td>{{ number_format($totalInput - $totalOutput, 3) }} جم</td>
                                                <td>{{ number_format($totalInput18 - $totalOutput18, 3) }} جم</td>
                                                <td>{{ number_format($totalInput21 - $totalOutput21, 3) }} جم</td>
                                                <td>{{ number_format($totalInput24 - $totalOutput24, 3) }} جم</td>
                                            </tr>
                                            <tr class="table-danger">
                                                <th>الخسية</th>
                                                <td>{{ number_format(($totalInput18 - $totalOutput18)/$totalInput18 * 100, 3) }}%</td>
                                            </tr>
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>

    <!-- Weights Modal -->
    <div class="modal fade" id="weightModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('laboratory.operations.update-weights', $operation) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header">
                        <h5 class="modal-title">إضافة الأوزان</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">وزن الفضة (جم)</label>
                            <input type="number" step="0.001" name="silver_weight" class="form-control" 
                                   value="{{ $operation->silver_weight }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">وزن الذهب الإضافي (جم)</label>
                            <input type="number" step="0.001" name="gold_weight" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">عيار الذهب</label>
                            <input type="number" name="gold_purity" class="form-control" value="1000" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary">حفظ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Costs Modal -->
    <div class="modal fade" id="costModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('laboratory.operations.update-costs', $operation) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header">
                        <h5 class="modal-title">حساب صافي الربح</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">المصروفات الإضافية</label>
                            <input type="number" step="0.01" name="operation_cost" class="form-control" required>
                            <small class="text-muted">المصروفات الحالية: {{ number_format(session('total_cost', 0), 2) }} ريال</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الإيرادات الإضافية</label>
                            <input type="number" step="0.01" name="operation_earn" class="form-control" required>
                            <small class="text-muted">الإيرادات الحالية: {{ number_format(session('total_earn', 0), 2) }} ريال</small>
                        </div>
                        <div class="alert alert-info">
                            سيتم إضافة المبالغ الجديدة إلى المبالغ الحالية
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary">إضافة وحساب</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html> 