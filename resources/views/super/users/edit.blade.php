<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Super User</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- BoxIcons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .edit-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .required-field {
            color: #dc3545;
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
                <h2 class="fw-bold text-dark">Edit User</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('super.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('super.users') }}" class="text-decoration-none">Users</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Edit User Form -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card edit-card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-user-pin me-2"></i>Edit User: {{ $user->name }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Display Success/Error Messages -->
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bx bx-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bx bx-error me-2"></i>
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('super.users.update', $user->id) }}">
                            @csrf
                            @method('PUT')
                            
                            <div class="row g-3">
                                <!-- Name & Email -->
                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-semibold">
                                        Name <span class="required-field">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bx bx-user"></i></span>
                                        <input type="text" 
                                               class="form-control @error('name') is-invalid @enderror" 
                                               id="name" 
                                               name="name" 
                                               value="{{ old('name', $user->name) }}" 
                                               required>
                                    </div>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-semibold">
                                        Email <span class="required-field">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                                        <input type="email" 
                                               class="form-control @error('email') is-invalid @enderror" 
                                               id="email" 
                                               name="email" 
                                               value="{{ old('email', $user->email) }}" 
                                               required>
                                    </div>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3 mt-2">
                                <!-- User Type & Shop -->
                                <div class="col-md-6">
                                    <label for="usertype" class="form-label fw-semibold">
                                        User Type <span class="required-field">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bx bx-shield"></i></span>
                                        <select class="form-select @error('usertype') is-invalid @enderror" 
                                                id="usertype" 
                                                name="usertype" 
                                                required>
                                            <option value="">Select User Type</option>
                                            <option value="super" {{ old('usertype', $user->usertype) == 'super' ? 'selected' : '' }}>
                                                Super Admin
                                            </option>
                                            <option value="admin" {{ old('usertype', $user->usertype) == 'admin' ? 'selected' : '' }}>
                                                Admin
                                            </option>
                                            <option value="rabea" {{ old('usertype', $user->usertype) == 'rabea' ? 'selected' : '' }}>
                                                Rabea
                                            </option>
                                            <option value="Acc" {{ old('usertype', $user->usertype) == 'Acc' ? 'selected' : '' }}>
                                                Accountant
                                            </option>
                                            <option value="user" {{ old('usertype', $user->usertype) == 'user' ? 'selected' : '' }}>
                                                User
                                            </option>
                                        </select>
                                    </div>
                                    @error('usertype')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="shop_name" class="form-label fw-semibold">Shop Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bx bx-store"></i></span>
                                        <select class="form-select @error('shop_name') is-invalid @enderror" 
                                                id="shop_name" 
                                                name="shop_name">
                                            <option value="">Select Shop</option>
                                            @foreach($shops as $shop)
                                                <option value="{{ $shop->name }}" 
                                                        {{ old('shop_name', $user->shop_name) == $shop->name ? 'selected' : '' }}>
                                                    {{ $shop->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('shop_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3 mt-2">
                                <!-- Password -->
                                <div class="col-12">
                                    <label for="password" class="form-label fw-semibold">New Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bx bx-lock-alt"></i></span>
                                        <input type="password" 
                                               class="form-control @error('password') is-invalid @enderror" 
                                               id="password" 
                                               name="password" 
                                               placeholder="Leave blank to keep current password">
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text text-muted">
                                        <i class="bx bx-info-circle me-1"></i>
                                        Leave blank if you don't want to change the password
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                <a href="{{ route('super.users') }}" class="btn btn-outline-secondary">
                                    <i class="bx bx-arrow-back me-2"></i>Back to Users
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save me-2"></i>Update User
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
</body>
</html> 