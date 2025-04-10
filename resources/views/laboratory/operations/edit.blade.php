<!DOCTYPE html>
<html lang="ar">
<head>
    @include('components.navbar')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل العملية</title>
    
    <!-- Bootstrap RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container{
            margin-top: 20px;
            direction: rtl;
        }
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
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
                        <h5 class="mb-0">تعديل العملية #{{ $operation->operation_number }}</h5>
                        <div>
                            @if($operation->status === 'active')
                                <form action="{{ route('laboratory.operations.close', $operation) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('هل أنت متأكد من إغلاق العملية؟')">
                                        <i class="fas fa-lock"></i> إغلاق العملية
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('laboratory.operations.show', $operation) }}" class="btn btn-secondary">
                                <i class="fas fa-eye"></i> عرض التفاصيل
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Inputs Section -->
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
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
                                                <th>التاريخ</th>
                                            </tr>
                                        </thead>
                                        <tbody id="inputsTableBody">
                                            @foreach($operation->inputs()->orderBy('created_at', 'asc')->get() as $index => $input)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ number_format($input->weight, 3) }} جم</td>
                                                <td>{{ $input->purity }}</td>
                                                <td>{{ $input->input_date->format('Y-m-d') }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr id="newInputRow" style="display: none;">
                                                <form action="{{ route('laboratory.operations.add-input', $operation) }}" method="POST" id="newInputForm">
                                                    @csrf
                                                    <td></td>
                                                    <td>
                                                        <input type="number" step="0.001" name="weight" class="form-control form-control-sm" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="purity" class="form-control form-control-sm" required>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex">
                                                            <input type="date" name="input_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                                                            <button type="submit" class="btn btn-success btn-sm ms-1">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-danger btn-sm ms-1" onclick="hideInputRow()">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </form>
                                            </tr>
                                            <tr>
                                                <td colspan="4" class="text-center">
                                                    <button type="button" class="btn btn-success btn-sm" onclick="showInputRow()">
                                                        <i class="fas fa-plus"></i> إضافة تسليم
                                                    </button>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Outputs Section -->
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
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
                                                <th>التاريخ</th>
                                            </tr>
                                        </thead>
                                        <tbody id="outputsTableBody">
                                            @foreach($operation->outputs()->orderBy('created_at', 'asc')->get() as $index => $output)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ number_format($output->weight, 3) }} جم</td>
                                                <td>{{ $output->purity }}</td>
                                                <td>{{ $output->output_date->format('Y-m-d') }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr id="newOutputRow" style="display: none;">
                                                <form action="{{ route('laboratory.operations.add-output', $operation) }}" method="POST" id="newOutputForm">
                                                    @csrf
                                                    <td></td>
                                                    <td>
                                                        <input type="number" step="0.001" name="weight" class="form-control form-control-sm" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="purity" class="form-control form-control-sm" required>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex">
                                                            <input type="date" name="output_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                                                            <button type="submit" class="btn btn-success btn-sm ms-1">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-danger btn-sm ms-1" onclick="hideOutputRow()">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </form>
                                            </tr>
                                            <tr>
                                                <td colspan="4" class="text-center">
                                                    <button type="button" class="btn btn-success btn-sm" onclick="showOutputRow()">
                                                        <i class="fas fa-plus"></i> إضافة استلام
                                                    </button>
                                                </td>
                                            </tr>
                                        </tfoot>
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
        function showInputRow() {
            document.getElementById('newInputRow').style.display = 'table-row';
        }

        function hideInputRow() {
            document.getElementById('newInputRow').style.display = 'none';
        }

        function showOutputRow() {
            document.getElementById('newOutputRow').style.display = 'table-row';
        }

        function hideOutputRow() {
            document.getElementById('newOutputRow').style.display = 'none';
        }

        // Auto-hide success alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert-success');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>
</body>
</html> 