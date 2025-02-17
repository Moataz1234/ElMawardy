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
                    <div class="card-body" style="direction: rtl;">
                        <form id="poundForm" action="{{ route('gold-pounds.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label for="type" class="form-label">النوع</label>
                                <select name="type" id="type"
                                    class="form-select @error('type') is-invalid @enderror">
                                    <option value="">Select Type</option>
                                    <option value="standalone" {{ old('type') == 'standalone' ? 'selected' : '' }}>لوحده
                                    </option>
                                    <option value="in_item" {{ old('type') == 'in_item' ? 'selected' : '' }}>في قطعة
                                    </option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="pound_type" class="form-label">موديل الجنيه</label>
                                <select name="pound_type" id="pound_type" class="form-select select2">
                                    <option value="">اختر موديل الجنيه</option>
                                    @foreach ($poundTypes as $pound)
                                        <option value="{{ $pound->id }}" 
                                                data-kind="{{ $pound->kind }}"
                                                data-weight="{{ $pound->weight }}"
                                                data-purity="{{ $pound->purity }}">
                                            {{ $pound->kind }} ({{ $pound->weight }}g - {{ $pound->purity }} قيراط)
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Variant Options Group -->
                            <div id="variant_options_group" style="display: none;">
                                <!-- Custom Weight Field -->
                                <div class="mb-3">
                                    <label for="custom_weight" class="form-label">الوزن المخصص (جرام)</label>
                                    <input type="number" name="custom_weight" id="custom_weight" 
                                           class="form-control" step="0.01" min="0" 
                                           placeholder="ادخل الوزن بالجرام">
                                </div>

                                <!-- Custom Purity Field -->
                                <div class="mb-3">
                                    <label for="custom_purity" class="form-label">العيار المخصص</label>
                                    <input type="number" name="custom_purity" id="custom_purity" 
                                    class="form-control" step="0.1" min="0" 
                                    placeholder="ادخل العيار ">
                                    {{-- <select name="custom_purity" id="custom_purity" class="form-select select2-tags">
                                        <option value="">اختر العيار</option>
                                        <option value="24">999 قيراط</option>
                                        <option value="22">916 قيراط</option>
                                        <option value="21">875 قيراط</option>
                                        <option value="18">750 قيراط</option>
                                    </select> --}}
                                </div>
                            </div>

                            <!-- Image Upload Field -->
                            <div class="mb-3">
                                <label for="image" class="form-label">صورة الجنيه (اختياري)</label>
                                <div class="input-group">
                                    <input type="file" name="image" id="image" 
                                           class="form-control @error('image') is-invalid @enderror" 
                                           accept="image/*">
                                    <label class="input-group-text" for="image">
                                        <i class="fas fa-upload"></i>
                                    </label>
                                </div>
                                <div id="imagePreview" class="mt-2" style="display: none;">
                                    <img src="" alt="Preview" style="max-width: 200px; max-height: 200px;">
                                </div>
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="shop_name" class="form-label">المحل</label>
                                <select name="shop_name" id="shop_name"
                                    class="form-select @error('shop_name') is-invalid @enderror">
                                    <option value=""> اختر محل</option>
                                    @foreach ($shops as $shop)
                                        <option value="{{ $shop->name }}"
                                            {{ old('shop_name') == $shop->name ? 'selected' : '' }}>
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
                                <input type="text" name="serial_number" id="serial_number"
                                    class="form-control @error('serial_number') is-invalid @enderror"
                                    value="{{ old('serial_number') }}" placeholder="Enter serial number">
                                @error('serial_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="quantity" class="form-label">الكمية</label>
                                <input type="number" name="quantity" id="quantity"
                                    class="form-control @error('quantity') is-invalid @enderror"
                                    value="{{ old('quantity', 1) }}" min="1" required>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid gap-2">
                                <button id="bottone1" type="submit" class="btn btn-primary ">
                                    <i class="fas fa-plus-circle "></i> <strong>Create Request</strong>
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

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('poundForm');
            const typeSelect = document.getElementById('type');
            const poundTypeSelect = document.getElementById('pound_type');
            const serialNumberGroup = document.getElementById('serial_number_group');
            const customWeightGroup = document.getElementById('variant_options_group');
            const customWeightInput = document.getElementById('custom_weight');
            const customPuritySelect = document.getElementById('custom_purity');
            const quantityInput = document.getElementById('quantity');

            // Handle form type changes
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

            // Handle pound type changes
            function updatePoundType() {
                const selectedOption = poundTypeSelect.options[poundTypeSelect.selectedIndex];
                const kind = selectedOption?.dataset?.kind;
                const variantOptionsGroup = document.getElementById('variant_options_group');

                if (kind === 'pound_varient' || kind === 'bar_varient') {
                    variantOptionsGroup.style.display = 'block';
                    customWeightInput.required = true;
                    customPuritySelect.required = true;
                } else {
                    variantOptionsGroup.style.display = 'none';
                    customWeightInput.required = false;
                    customPuritySelect.required = false;
                    customWeightInput.value = '';
                    customPuritySelect.value = '';
                }
            }

            typeSelect.addEventListener('change', updateForm);
            poundTypeSelect.addEventListener('change', updatePoundType);

            // Run on page load
            updateForm();
            updatePoundType();

            // Image preview functionality
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

            // Handle form submission
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
                            title: 'نجاح!',
                            text: data.message,
                            confirmButtonColor: '#0d6efd',
                            confirmButtonText: 'حسناً'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.reset();
                                imagePreview.style.display = 'none';
                                updateForm();
                                updatePoundType();
                            }
                        });
                    } else {
                        // Show validation errors if they exist
                        if (data.errors && data.errors.length > 0) {
                            const errorMessage = data.errors.join('\n');
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ في التحقق',
                                text: errorMessage,
                                confirmButtonColor: '#0d6efd',
                                confirmButtonText: 'حسناً'
                            });
                        } else {
                            throw new Error(data.message || 'حدث خطأ غير متوقع');
                        }
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ!',
                        text: error.message,
                        confirmButtonColor: '#0d6efd',
                        confirmButtonText: 'حسناً'
                    });
                });
            });

            // Show success message if exists in session
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#0d6efd'
                });
            @endif

            // Show error message if exists in session
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '{{ session('error') }}',
                    confirmButtonColor: '#0d6efd'
                });
            @endif

            // Initialize Select2 for regular dropdowns
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });

            // Initialize Select2 with tags for custom purity
            $('.select2-tags').select2({
                theme: 'bootstrap-5',
                width: '100%',
                tags: true,
                createTag: function (params) {
                    return {
                        id: params.term,
                        text: params.term + ' قيراط',
                        newOption: true
                    }
                }
            });
        });
    </script>
</body>

</html>
