<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Model - Super User</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- BoxIcons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .create-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .form-label {
            font-weight: 600;
            color: #495057;
        }
        .btn-purple {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
        }
        .btn-purple:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            color: white;
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
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold text-dark">Add New Model</h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('super.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('super.models.index') }}" class="text-decoration-none">Models</a></li>
                                <li class="breadcrumb-item active">Add New</li>
                            </ol>
                        </nav>
                    </div>
                    <a href="{{ route('super.models.index') }}" class="btn btn-outline-secondary">
                        <i class="bx bx-arrow-back me-2"></i>Back to Models
                    </a>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bx bx-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bx bx-error me-2"></i>Please fix the following errors:
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Create Form -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card create-card">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-plus-circle me-2"></i>Model Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('super.models.store') }}" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="row g-3">
                                <!-- Model Name -->
                                <div class="col-md-6">
                                    <label for="model" class="form-label">Model Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('model') is-invalid @enderror" 
                                           id="model" name="model" value="{{ old('model') }}" 
                                           placeholder="Enter model name" required>
                                    @error('model')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- SKU -->
                                <div class="col-md-6">
                                    <label for="SKU" class="form-label">SKU</label>
                                    <input type="text" class="form-control @error('SKU') is-invalid @enderror" 
                                           id="SKU" name="SKU" value="{{ old('SKU') }}" 
                                           placeholder="Enter SKU">
                                    @error('SKU')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Scanned Image URL -->
                                <div class="col-md-6">
                                    <label for="scanned_image" class="form-label">Scanned Image URL</label>
                                    <input type="url" class="form-control @error('scanned_image') is-invalid @enderror" 
                                           id="scanned_image" name="scanned_image" value="{{ old('scanned_image') }}" 
                                           placeholder="https://example.com/image.jpg">
                                    @error('scanned_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Website Image URL -->
                                <div class="col-md-6">
                                    <label for="website_image" class="form-label">Website Image URL</label>
                                    <input type="url" class="form-control @error('website_image') is-invalid @enderror" 
                                           id="website_image" name="website_image" value="{{ old('website_image') }}" 
                                           placeholder="https://example.com/image.jpg">
                                    @error('website_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Stars (Model Type) -->
                                <div class="col-md-6">
                                    <label for="stars" class="form-label">Model Type (Stars)</label>
                                    <select class="form-select @error('stars') is-invalid @enderror" id="stars" name="stars">
                                        <option value="">Select model type</option>
                                        <option value="1" {{ old('stars') == '1' ? 'selected' : '' }}>Type 1</option>
                                        <option value="2" {{ old('stars') == '2' ? 'selected' : '' }}>Type 2</option>
                                        <option value="3" {{ old('stars') == '3' ? 'selected' : '' }}>Type 3</option>
                                        <option value="4" {{ old('stars') == '4' ? 'selected' : '' }}>Type 4</option>
                                        <option value="5" {{ old('stars') == '5' ? 'selected' : '' }}>Type 5</option>
                                    </select>
                                    @error('stars')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Source -->
                                <div class="col-md-6">
                                    <label for="source" class="form-label">Source</label>
                                    <select class="form-select @error('source') is-invalid @enderror" id="source" name="source">
                                        <option value="">Select source</option>
                                        <option value="internal" {{ old('source') == 'internal' ? 'selected' : '' }}>Internal</option>
                                        <option value="external" {{ old('source') == 'external' ? 'selected' : '' }}>External</option>
                                        <option value="imported" {{ old('source') == 'imported' ? 'selected' : '' }}>Imported</option>
                                        <option value="custom" {{ old('source') == 'custom' ? 'selected' : '' }}>Custom</option>
                                    </select>
                                    @error('source')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- First Production -->
                                <div class="col-md-6">
                                    <label for="first_production" class="form-label">First Production Date</label>
                                    <input type="date" class="form-control @error('first_production') is-invalid @enderror" 
                                           id="first_production" name="first_production" value="{{ old('first_production') }}">
                                    @error('first_production')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Semi or No -->
                                <div class="col-md-6">
                                    <label for="semi_or_no" class="form-label">Semi or No</label>
                                    <select class="form-select @error('semi_or_no') is-invalid @enderror" id="semi_or_no" name="semi_or_no">
                                        <option value="">Select option</option>
                                        <option value="1 pound" {{ old('semi_or_no') == '1 pound' ? 'selected' : '' }}>1 Pound</option>
                                        <option value="1/2 pound" {{ old('semi_or_no') == '1/2 pound' ? 'selected' : '' }}>1/2 Pound</option>
                                        <option value="1/4 pound" {{ old('semi_or_no') == '1/4 pound' ? 'selected' : '' }}>1/4 Pound</option>
                                        <option value="semi" {{ old('semi_or_no') == 'semi' ? 'selected' : '' }}>Semi</option>
                                        <option value="no" {{ old('semi_or_no') == 'no' ? 'selected' : '' }}>No</option>
                                    </select>
                                    @error('semi_or_no')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Average of Stones -->
                                <div class="col-md-12">
                                    <label for="average_of_stones" class="form-label">Average of Stones (grams)</label>
                                    <input type="number" step="0.001" class="form-control @error('average_of_stones') is-invalid @enderror" 
                                           id="average_of_stones" name="average_of_stones" value="{{ old('average_of_stones') }}" 
                                           placeholder="Enter average weight of stones">
                                    @error('average_of_stones')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('super.models.index') }}" class="btn btn-outline-secondary">
                                            <i class="bx bx-x me-1"></i>Cancel
                                        </a>
                                        <button type="submit" class="btn btn-purple">
                                            <i class="bx bx-save me-1"></i>Create Model
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 