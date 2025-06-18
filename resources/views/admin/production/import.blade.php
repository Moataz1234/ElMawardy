<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Production Data - Elmawardy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @include('components.navbar')
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Import Production Data from Excel</h4>
                    <a href="{{ route('production.index') }}" class="btn btn-secondary btn-sm float-end">
                        Back to Production List
                    </a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="alert alert-warning">
                            {{ session('warning') }}
                            @if(session('import_errors'))
                                <hr>
                                <strong>Errors:</strong>
                                <ul class="mb-0">
                                    @foreach(session('import_errors') as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- File Upload Form -->
                    <form action="{{ route('production.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="excel_file" class="form-label">
                                <strong>Select Excel File</strong>
                            </label>
                            <input type="file" 
                                   class="form-control @error('excel_file') is-invalid @enderror" 
                                   id="excel_file" 
                                   name="excel_file" 
                                   accept=".xlsx,.xls,.csv"
                                   required>
                            <div class="form-text">
                                Supported formats: .xlsx, .xls, .csv (Max size: 10MB)
                            </div>
                            @error('excel_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload"></i> Import Production Data
                            </button>
                        </div>
                    </form>

                    <!-- Template Download -->
                    <div class="text-center mt-3">
                        <a href="{{ route('production.template') }}" class="btn btn-outline-success">
                            <i class="fas fa-download"></i> Download Sample Template
                        </a>
                    </div>

                    <!-- Instructions -->
                    <div class="mt-5">
                        <h5>Excel File Format Instructions</h5>
                        <div class="alert alert-info">
                            <strong>Your Excel file should have the following columns (in this exact order):</strong>
                            <ol class="mt-2 mb-2">
                                <li><strong>Model</strong> - The model name/code (required)</li>
                                <li><strong>Quantity</strong> - Total quantity to produce (required, must be positive number)</li>
                                <li><strong>Not Finished</strong> - Number of items not yet finished (required, must be 0 or positive)</li>
                                <li><strong>Gold Color</strong> - Gold color (optional: Yellow, White, or Rose - defaults to Yellow if empty)</li>
                                <li><strong>Order Date</strong> - Date of the order (optional, defaults to today if empty)</li>
                            </ol>
                            <strong>Notes:</strong>
                            <ul class="mt-2 mb-0">
                                <li>First row can be headers (will be skipped automatically)</li>
                                <li>Empty rows will be skipped</li>
                                <li>If a model with the same color already exists, it will be updated with new data</li>
                                <li>Gold Color is optional - if empty or null, it will default to "Yellow"</li>
                                <li>Valid Gold Colors are: Yellow, White, Rose (case-sensitive)</li>
                                <li>Date format can be: YYYY-MM-DD, DD/MM/YYYY, or Excel date format</li>
                            </ul>
                        </div>

                        <!-- Sample Table -->
                        <h6>Sample Data Format:</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Model</th>
                                        <th>Quantity</th>
                                        <th>Not Finished</th>
                                        <th>Gold Color</th>
                                        <th>Order Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>5-1790</td>
                                        <td>100</td>
                                        <td>25</td>
                                        <td>Yellow</td>
                                        <td>2024-01-15</td>
                                    </tr>
                                    <tr>
                                        <td>6-2100-A</td>
                                        <td>50</td>
                                        <td>10</td>
                                        <td>White</td>
                                        <td>15/01/2024</td>
                                    </tr>
                                    <tr>
                                        <td>7-3500-B</td>
                                        <td>75</td>
                                        <td>0</td>
                                        <td>Rose</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>8-4000-C</td>
                                        <td>30</td>
                                        <td>15</td>
                                        <td><em>(empty - defaults to Yellow)</em></td>
                                        <td>2024-01-18</td>
                                    </tr>
                                    <tr>
                                        <td>9-5000-D</td>
                                        <td>60</td>
                                        <td>30</td>
                                        <td><em>(empty - defaults to Yellow)</em></td>
                                        <td></td>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 