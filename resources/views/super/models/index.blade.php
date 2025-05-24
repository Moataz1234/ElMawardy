<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Models Management - Super User</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- BoxIcons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .models-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .filter-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .model-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
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
                        <h2 class="fw-bold text-dark">Models Management</h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('super.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                                <li class="breadcrumb-item active">Models</li>
                            </ol>
                        </nav>
                    </div>
                    <a href="{{ route('super.models.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus me-2"></i>Add New Model
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

        <!-- Models Table -->
        <div class="row">
            <div class="col-12">
                <div class="card models-card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="bx bx-grid me-2"></i>All Models</h5>
                        <div>
                            <span class="badge bg-info fs-6">Total: {{ $models->total() ?? 0 }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Model</th>
                                        <th>SKU</th>
                                        <th>Images</th>
                                        <th>Stars</th>
                                        <th>Source</th>
                                        <th>Production</th>
                                        <th>Semi/No</th>
                                        <th>Avg Stones</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($models ?? [] as $model)
                                    <tr>
                                        <td><strong>{{ $model->id }}</strong></td>
                                        <td>
                                            <span class="badge bg-primary">{{ $model->model }}</span>
                                        </td>
                                        <td>{{ $model->SKU ?? 'N/A' }}</td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                @if($model->scanned_image)
                                                    <img src="{{ $model->scanned_image }}" alt="Scanned" class="model-image" title="Scanned Image">
                                                @endif
                                                @if($model->website_image)
                                                    <img src="{{ $model->website_image }}" alt="Website" class="model-image" title="Website Image">
                                                @endif
                                                @if(!$model->scanned_image && !$model->website_image)
                                                    <span class="text-muted">No images</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if($model->stars)
                                                <span class="badge bg-warning text-dark">{{ $model->stars }}</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($model->source)
                                                <span class="badge bg-success">{{ $model->source }}</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($model->first_production)
                                                <span class="text-info">{{ $model->first_production }}</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($model->semi_or_no)
                                                <span class="badge bg-warning text-dark">{{ $model->semi_or_no }}</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($model->average_of_stones)
                                                <span class="text-primary">{{ $model->average_of_stones }}g</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('super.models.show', $model->id) }}" 
                                                   class="btn btn-sm btn-outline-info" 
                                                   title="View Model">
                                                    <i class="bx bx-show"></i>
                                                </a>
                                                <a href="{{ route('super.models.edit', $model->id) }}" 
                                                   class="btn btn-sm btn-outline-primary ms-1" 
                                                   title="Edit Model">
                                                    <i class="bx bx-edit"></i>
                                                </a>
                                                <form method="POST" 
                                                      action="{{ route('super.models.destroy', $model->id) }}" 
                                                      style="display: inline;" 
                                                      onsubmit="return confirm('Are you sure you want to delete this model?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-danger ms-1" 
                                                            title="Delete Model">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="bx bx-grid fs-1 d-block mb-2"></i>
                                                No models found
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        @if(isset($models) && $models->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $models->appends(request()->query())->links('vendor.pagination.custom-super') }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 