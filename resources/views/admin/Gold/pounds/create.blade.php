<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Gold Pounds</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    @include('components.navbar')
    <style>
        body {
            background-color: #f8f9fa;
        }

        .card {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border: none;
            margin-top: 2rem;
        }

        .card-header {
            background-color: #0d6efd;
            color: white;
            padding: 1rem;
        }

        .form-label {
            font-weight: 500;
            margin-top: 0.5rem;
        }

      

        /* From Uiverse.io by TCdesign-dev */
        #bottone1 {
            padding-left: 33px;
            padding-right: 33px;
            padding-bottom: 16px;
            padding-top: 16px;
            border-radius: 9px;
            border: none;
            font-family: inherit;
            text-align: center;
            cursor: pointer;
            transition: 0.4s;
        }

        #bottone1:hover {
            box-shadow: 7px 5px 56px -14px #06337b;
            background: #06337b;
            color: rgb(112, 200, 24);
        }

        #bottone1:active {
            transform: scale(0.97);
            box-shadow: 7px 5px 56px -10px #C3D900;
        }
        
    </style>
</head>

<body>
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">
                            <i class="fas fa-coins me-2"></i>Add New Gold Pounds
                        </h3>
                    </div>
                    <div class="card-body">
                        <form id="poundForm" action="{{ route('gold-pounds.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label for="type" class="form-label">Type</label>
                                <select name="type" id="type" class="form-select @error('type') is-invalid @enderror">
                                    <option value="">Select Type</option>
                                    <option value="standalone" {{ old('type') == 'standalone' ? 'selected' : '' }}>Standalone</option>
                                    <option value="in_item" {{ old('type') == 'in_item' ? 'selected' : '' }}>In Item</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="pound_type" class="form-label">Pound Model</label>
                                <select name="pound_type" id="pound_type" class="form-select select2">
                                    <option value="">Select Pound Model</option>
                                    <option value="custom">Custom Pound (Specify Kind, Weight, and Purity)</option>
                                    @foreach ($poundTypes as $pound)
                                        <option value="{{ $pound->id }}" 
                                                data-kind="{{ $pound->kind }}"
                                                data-weight="{{ $pound->weight }}"
                                                data-purity="{{ $pound->purity }}">
                                            {{ $pound->kind }} ({{ $pound->weight }}g - {{ $pound->purity }} karat)
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Custom Pound Options -->
                            <div id="custom_pound_group" style="display: none;">
                                <div class="mb-3">
                                    <label for="custom_kind" class="form-label">Custom Pound Kind</label>
                                    <input type="text" name="custom_kind" id="custom_kind" class="form-control" placeholder="Enter custom pound kind">
                                </div>
                                <div class="mb-3">
                                    <label for="custom_weight" class="form-label">Custom Weight (grams)</label>
                                    <input type="number" name="custom_weight" id="custom_weight" class="form-control" step="0.01" min="0" placeholder="Enter weight in grams">
                                </div>
                                <div class="mb-3">
                                    <label for="custom_purity" class="form-label">Custom Purity (karat)</label>
                                    <input type="number" name="custom_purity" id="custom_purity" class="form-control" step="0.1" min="0" max="999" placeholder="Enter purity">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">Pound Image (Optional)</label>
                                <div class="input-group">
                                    <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                                    <label class="input-group-text" for="image"><i class="fas fa-upload"></i></label>
                                </div>
                                <div id="imagePreview" class="mt-2" style="display: none;">
                                    <img src="" alt="Preview" style="max-width: 200px; max-height: 200px;">
                                </div>
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="shop_name" class="form-label">Shop</label>
                                <select name="shop_name" id="shop_name" class="form-select @error('shop_name') is-invalid @enderror">
                                    <option value="">Select Shop</option>
                                    @foreach ($shops as $shop)
                                        <option value="{{ $shop->name }}" {{ old('shop_name') == $shop->name ? 'selected' : '' }}>
                                            {{ $shop->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('shop_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3" id="serial_number_group" style="display: none;">
                                <label for="serial_number" class="form-label">Serial Number</label>
                                <input type="text" name="serial_number" id="serial_number" class="form-control @error('serial_number') is-invalid @enderror" value="{{ old('serial_number') }}" placeholder="Enter serial number">
                                @error('serial_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="quantity" class="form-label">Quantity</label>
                                <input type="number" name="quantity" id="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity', 1) }}" min="1" required>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid gap-2">
                                <button id="bottone1" type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus-circle"></i> <strong>Create Request</strong>
                                </button>
                                <a href="{{ url()->previous() }}" class="btn btn-dark">
                                    <i class="fas fa-arrow-left me-2"></i>Back
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('poundForm');
            const typeSelect = document.getElementById('type');
            const poundTypeSelect = document.getElementById('pound_type');
            const serialNumberGroup = document.getElementById('serial_number_group');
            const customPoundGroup = document.getElementById('custom_pound_group');
            const customKindInput = document.getElementById('custom_kind');
            const customWeightInput = document.getElementById('custom_weight');
            const customPurityInput = document.getElementById('custom_purity');
            const quantityInput = document.getElementById('quantity');

            function updateForm() {
                if (typeSelect.value === 'in_item') {
                    serialNumberGroup.style.display = 'block';
                    document.getElementById('serial_number').required = true;
                    quantityInput.value = '1';
                    quantityInput.readOnly = true;
                } else {
                    serialNumberGroup.style.display = 'none';
                    document.getElementById('serial_number').required = false;
                    quantityInput.readOnly = false;
                }
            }

            function updatePoundType() {
                const selectedValue = poundTypeSelect.value;
                if (selectedValue === 'custom') {
                    customPoundGroup.style.display = 'block';
                    customKindInput.required = true;
                    customWeightInput.required = true;
                    customPurityInput.required = true;
                } else {
                    customPoundGroup.style.display = 'none';
                    customKindInput.required = false;
                    customWeightInput.required = false;
                    customPurityInput.required = false;
                    customKindInput.value = '';
                    customWeightInput.value = '';
                    customPurityInput.value = '';
                }
            }

            typeSelect.addEventListener('change', updateForm);
            poundTypeSelect.addEventListener('change', updatePoundType);
            updateForm();
            updatePoundType();

            const imageInput = document.getElementById('image');
            const imagePreview = document.getElementById('imagePreview');
            const previewImg = imagePreview.querySelector('img');

            imageInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        imagePreview.style.display = 'block';
                    }
                    reader.readAsDataURL(this.files[0]);
                } else {
                    previewImg.src = '';
                    imagePreview.style.display = 'none';
                }
            });

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(form);

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: data.message,
                            confirmButtonColor: '#0d6efd',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.reset();
                                imagePreview.style.display = 'none';
                                updateForm();
                                updatePoundType();
                            }
                        });
                    } else {
                        if (data.errors && data.errors.length > 0) {
                            const errorMessage = data.errors.join('\n');
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                text: errorMessage,
                                confirmButtonColor: '#0d6efd',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            throw new Error(data.message || 'An unexpected error occurred');
                        }
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: error.message,
                        confirmButtonColor: '#0d6efd',
                        confirmButtonText: 'OK'
                    });
                });
            });
        });
    </script>
</body>
</html>