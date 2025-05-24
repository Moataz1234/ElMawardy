<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Customer - {{ $customer->first_name }} {{ $customer->last_name }}</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- BoxIcons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .customer-form-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .form-label {
            font-weight: 600;
            color: #495057;
        }
        .required-field::after {
            content: " *";
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
                <h2 class="fw-bold text-dark">Edit Customer</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('super.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('super.customers') }}" class="text-decoration-none">Customers</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('super.customers.show', $customer->id) }}" class="text-decoration-none">{{ $customer->first_name }} {{ $customer->last_name }}</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Customer Form -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card customer-form-card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="card-title mb-0"><i class="bx bx-edit me-2"></i>Edit Customer Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('super.customers.update', $customer->id) }}">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label required-field">First Name</label>
                                    <input type="text" 
                                           class="form-control @error('first_name') is-invalid @enderror" 
                                           id="first_name" 
                                           name="first_name" 
                                           value="{{ old('first_name', $customer->first_name) }}" 
                                           required>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label required-field">Last Name</label>
                                    <input type="text" 
                                           class="form-control @error('last_name') is-invalid @enderror" 
                                           id="last_name" 
                                           name="last_name" 
                                           value="{{ old('last_name', $customer->last_name) }}" 
                                           required>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email', $customer->email) }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="phone_number" class="form-label required-field">Phone Number</label>
                                    <input type="tel" 
                                           class="form-control @error('phone_number') is-invalid @enderror" 
                                           id="phone_number" 
                                           name="phone_number" 
                                           value="{{ old('phone_number', $customer->phone_number) }}" 
                                           required>
                                    @error('phone_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                          id="address" 
                                          name="address" 
                                          rows="3">{{ old('address', $customer->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Preferred Payment Method</label>
                                <select class="form-select @error('payment_method') is-invalid @enderror" 
                                        id="payment_method" 
                                        name="payment_method">
                                    <option value="">Select Payment Method</option>
                                    <option value="cash" {{ old('payment_method', $customer->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="credit_card" {{ old('payment_method', $customer->payment_method) == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                    <option value="debit_card" {{ old('payment_method', $customer->payment_method) == 'debit_card' ? 'selected' : '' }}>Debit Card</option>
                                    <option value="bank_transfer" {{ old('payment_method', $customer->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="installment" {{ old('payment_method', $customer->payment_method) == 'installment' ? 'selected' : '' }}>Installment</option>
                                </select>
                                @error('payment_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Customer Statistics (Read-only) -->
                            <div class="mb-3">
                                <h6 class="fw-bold text-muted">Customer Statistics</h6>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="text-center p-2 bg-light rounded">
                                            <strong class="text-primary">{{ $customer->goldItemsSold->count() }}</strong><br>
                                            <small class="text-muted">Total Purchases</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center p-2 bg-light rounded">
                                            <strong class="text-success">${{ number_format($customer->goldItemsSold->sum('price'), 2) }}</strong><br>
                                            <small class="text-muted">Total Spent</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center p-2 bg-light rounded">
                                            <strong class="text-info">{{ $customer->goldItemsSold->sum('weight') }}g</strong><br>
                                            <small class="text-muted">Total Weight</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center p-2 bg-light rounded">
                                            <strong class="text-warning">{{ $customer->created_at->format('M Y') }}</strong><br>
                                            <small class="text-muted">Member Since</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <div>
                                    <a href="{{ route('super.customers.show', $customer->id) }}" class="btn btn-outline-secondary me-2">
                                        <i class="bx bx-arrow-back me-2"></i>Back to Details
                                    </a>
                                    <a href="{{ route('super.customers') }}" class="btn btn-outline-info">
                                        <i class="bx bx-list-ul me-2"></i>All Customers
                                    </a>
                                </div>
                                <button type="submit" class="btn btn-warning">
                                    <i class="bx bx-save me-2"></i>Update Customer
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