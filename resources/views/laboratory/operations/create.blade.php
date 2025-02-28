<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Laboratory Operation</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 20px;
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
        .form-group {
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white px-3">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Gold System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Laboratory</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Workshop</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Assembly</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <!-- Flash Messages -->
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Create New Operation</h5>
                        <a href="{{ route('laboratory.operations.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('laboratory.operations.store') }}" method="POST" id="operationForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="operation_number">Operation Number</label>
                                        <input type="text" 
                                               class="form-control @error('operation_number') is-invalid @enderror" 
                                               id="operation_number" 
                                               name="operation_number" 
                                               value="{{ old('operation_number') }}" 
                                               required>
                                        @error('operation_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="operation_date">Operation Date</label>
                                        <input type="date" 
                                               class="form-control @error('operation_date') is-invalid @enderror" 
                                               id="operation_date" 
                                               name="operation_date" 
                                               value="{{ old('operation_date', date('Y-m-d')) }}" 
                                               required>
                                        @error('operation_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Inputs Section -->
                            <div class="card mt-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Inputs</h6>
                                    <button type="button" class="btn btn-sm btn-success" id="addInput">
                                        <i class="fas fa-plus"></i> Add Input
                                    </button>
                                </div>
                                <div class="card-body" id="inputsContainer">
                                    <div class="row input-row mb-3">
                                        <div class="col-md-5">
                                            <label>Weight (g)</label>
                                            <input type="number" step="0.001" name="inputs[0][weight]" class="form-control weight-input" required>
                                        </div>
                                        <div class="col-md-5">
                                            <label>Purity</label>
                                            <input type="number" name="inputs[0][purity]" class="form-control" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label>&nbsp;</label>
                                            <button type="button" class="btn btn-danger form-control remove-input">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <strong>Total Input Weight: </strong>
                                    <span id="totalInputWeight">0</span> g
                                </div>
                            </div>

                            {{-- <div class="form-group mt-4">
                                <label for="notes">Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" 
                                          name="notes" 
                                          rows="3">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div> --}}

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Create Operation
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-hide alerts
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);

            // Handle adding new inputs
            let inputCount = 1;
            document.getElementById('addInput').addEventListener('click', function() {
                const container = document.getElementById('inputsContainer');
                const newRow = document.createElement('div');
                newRow.className = 'row input-row mb-3';
                newRow.innerHTML = `
                    <div class="col-md-5">
                        <label>Weight (g)</label>
                        <input type="number" step="0.001" name="inputs[${inputCount}][weight]" class="form-control weight-input" required>
                    </div>
                    <div class="col-md-5">
                        <label>Purity</label>
                        <input type="number" name="inputs[${inputCount}][purity]" class="form-control" value="1000" required>
                    </div>
                    <div class="col-md-2">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-danger form-control remove-input">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
                container.appendChild(newRow);
                inputCount++;
                updateTotalWeight();
            });

            // Handle removing inputs
            document.getElementById('inputsContainer').addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-input') || e.target.parentElement.classList.contains('remove-input')) {
                    const row = e.target.closest('.input-row');
                    if (document.querySelectorAll('.input-row').length > 1) {
                        row.remove();
                        updateTotalWeight();
                    }
                }
            });

            // Update total weight
            function updateTotalWeight() {
                const inputs = document.querySelectorAll('.weight-input');
                let total = 0;
                inputs.forEach(input => {
                    total += parseFloat(input.value || 0);
                });
                document.getElementById('totalInputWeight').textContent = total.toFixed(3);
            }

            // Listen for weight input changes
            document.getElementById('inputsContainer').addEventListener('input', function(e) {
                if (e.target.classList.contains('weight-input')) {
                    updateTotalWeight();
                }
            });
        });
    </script>
</body>
</html> 