<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عمليات المعمل</title>
    
    <!-- Bootstrap RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom CSS -->
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
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0,0,0,.125);
        }
        .table th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }
        .cursor-pointer {
            cursor: pointer;
        }
        .cursor-pointer:hover {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white px-3">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">نظام الذهب</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">المعمل</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">الورشة</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">التركيب</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0">عمليات المعمل</h5>
                        <a href="{{ route('laboratory.operations.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> عملية جديدة
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="modal fade" id="purityModal" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">اختر العيار</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="d-grid gap-2">
                                            <button class="btn btn-lg btn-outline-primary purity-btn" data-purity="750">عيار 18</button>
                                            <button class="btn btn-lg btn-outline-primary purity-btn" data-purity="875">عيار 21</button>
                                            <button class="btn btn-lg btn-outline-primary purity-btn" data-purity="1000">عيار 24</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>رقم العملية</th>
                                        <th>التاريخ</th>
                                        <th>إجمالي الداخل</th>
                                        <th class="cursor-pointer" data-bs-toggle="modal" data-bs-target="#purityModal">
                                            العيار <i class="fas fa-filter"></i>
                                        </th>
                                        <th>إجمالي الخارج</th>
                                        <th class="cursor-pointer" data-bs-toggle="modal" data-bs-target="#purityModal">
                                            العيار <i class="fas fa-filter"></i>
                                        </th>
                                        <th>الخسية</th>
                                        <th>وزن الفضة</th>
                                        <th>التكلفة </th>
                                        <th>الحالة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($operations as $operation)
                                    @php
                                        // Calculate inputs totals
                                        $totalInputWeight = $operation->inputs->sum('weight');
                                        $totalInputK18 = $operation->inputs->sum(function($input) {
                                            return ($input->weight * $input->purity) / 750;
                                        });
                                        $totalInputK21 = $operation->inputs->sum(function($input) {
                                            return ($input->weight * $input->purity) / 875;
                                        });
                                        $totalInputK24 = $operation->inputs->sum(function($input) {
                                            return ($input->weight * $input->purity) / 1000;
                                        });

                                        // Calculate outputs totals
                                        $totalOutputWeight = $operation->outputs->sum('weight');
                                        $totalOutputK18 = $operation->outputs->sum(function($output) {
                                            return ($output->weight * $output->purity) / 750;
                                        });
                                        $totalOutputK21 = $operation->outputs->sum(function($output) {
                                            return ($output->weight * $output->purity) / 875;
                                        });
                                        $totalOutputK24 = $operation->outputs->sum(function($output) {
                                            return ($output->weight * $output->purity) / 1000;
                                        });
                                    @endphp
                                    <tr>
                                        <td>{{ $operation->operation_number }}</td>
                                        <td>{{ $operation->operation_date->format('Y-m-d') }}</td>
                                        <td>{{ number_format($totalInputWeight, 3) }} جم</td>
                                        <td class="purity-cell-input" 
                                            data-weight="{{ $totalInputWeight }}"
                                            data-k18="{{ $totalInputK18 }}"
                                            data-k21="{{ $totalInputK21 }}"
                                            data-k24="{{ $totalInputK24 }}">
                                            {{ number_format($totalInputWeight, 3) }} جم
                                        </td>
                                        <td>{{ number_format($totalOutputWeight, 3) }} جم</td>
                                        <td class="purity-cell-output" 
                                            data-weight="{{ $totalOutputWeight }}"
                                            data-k18="{{ $totalOutputK18 }}"
                                            data-k21="{{ $totalOutputK21 }}"
                                            data-k24="{{ $totalOutputK24 }}">
                                            {{ number_format($totalOutputWeight, 3) }} جم
                                        </td>
                                        <td>{{ number_format($operation->loss, 3) }}%</td>
                                        
                                        <td>{{ number_format($operation->silver_weight, 3) }} جم</td>
                                        <td>{{ number_format($operation->operation_cost, 2) }} </td>
                                        <td>
                                            @if($operation->status === 'active')
                                                <span class="badge bg-success">نشط</span>
                                            @else
                                                <span class="badge bg-secondary">مغلق</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('laboratory.operations.show', $operation) }}" 
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> عرض
                                            </a>
                                            @if($operation->status === 'active')
                                                <a href="{{ route('laboratory.operations.edit', $operation) }}" 
                                                   class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i> تعديل
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-end mt-3">
                            {{ $operations->links() }}
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

            const purityButtons = document.querySelectorAll('.purity-btn');
            const purityCellsInput = document.querySelectorAll('.purity-cell-input');
            const purityCellsOutput = document.querySelectorAll('.purity-cell-output');
            const modal = document.getElementById('purityModal');
            const bsModal = new bootstrap.Modal(modal);

            purityButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const selectedPurity = this.dataset.purity;
                    const karatKey = {
                        '750': 'k18',
                        '875': 'k21',
                        '1000': 'k24'
                    }[selectedPurity];
                    
                    // Update input cells
                    purityCellsInput.forEach(cell => {
                        const value = cell.dataset[karatKey];
                        cell.innerHTML = `${Number(value).toFixed(3)} جم`;
                    });

                    // Update output cells
                    purityCellsOutput.forEach(cell => {
                        const value = cell.dataset[karatKey];
                        cell.innerHTML = `${Number(value).toFixed(3)} جم`;
                    });

                    // Update header text to show selected karat
                    const karatMap = {
                        750: 'عيار 18',
                        875: 'عيار 21',
                        1000: 'عيار 24'
                    };
                    document.querySelector('th[data-bs-toggle="modal"]').innerHTML = 
                        `${karatMap[selectedPurity]} <i class="fas fa-filter"></i>`;

                    bsModal.hide();
                });
            });
        });
    </script>
</body>
</html> 