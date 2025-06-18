<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('components.navbar')
    <title>Edit Production Order #{{ $production->id }} - Elmawardy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar-brand {
            font-weight: bold;
            color: #fff !important;
        }
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: 1px solid rgba(0, 0, 0, 0.125);
        }
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
        }
        .form-label {
            font-weight: 500;
        }
        .btn {
            border-radius: 0.375rem;
        }
        .alert {
            border-radius: 0.5rem;
        }
        .progress {
            border-radius: 0.5rem;
        }
        
        /* Custom autocomplete styles */
        .autocomplete-container {
            position: relative;
        }
        
        .autocomplete-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #dee2e6;
            border-top: none;
            border-radius: 0 0 0.375rem 0.375rem;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }
        
        .autocomplete-item {
            padding: 0.75rem 1rem;
            cursor: pointer;
            border-bottom: 1px solid #f8f9fa;
        }
        
        .autocomplete-item:hover,
        .autocomplete-item.highlighted {
            background-color: #e9ecef;
        }
        
        .autocomplete-item:last-child {
            border-bottom: none;
        }
        
        .no-results {
            padding: 0.75rem 1rem;
            color: #6c757d;
            font-style: italic;
        }
    </style>
</head>
<body>

    <!-- Main Content -->
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-edit text-warning"></i>
                            Edit Production Order #{{ $production->id }}
                        </h4>
                        <a href="{{ route('production.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>

                    <div class="card-body">
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Current Status Card -->
                        <div class="alert alert-light border">
                            <div class="row">
                                <div class="col-md-2">
                                    <small class="text-muted">Current Model</small>
                                    <div class="fw-bold">{{ $production->model }}</div>
                                </div>
                                <div class="col-md-2">
                                    <small class="text-muted">Gold Color</small>
                                    <div class="fw-bold text-warning">{{ $production->gold_color ?? 'N/A' }}</div>
                                </div>
                                <div class="col-md-2">
                                    <small class="text-muted">Total Quantity</small>
                                    <div class="fw-bold text-primary">{{ $production->quantity }}</div>
                                </div>
                                <div class="col-md-2">
                                    <small class="text-muted">Not Finished</small>
                                    <div class="fw-bold text-warning">{{ $production->not_finished }}</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Progress</small>
                                    @php
                                        $progress = $production->quantity > 0 
                                            ? round((($production->quantity - $production->not_finished) / $production->quantity) * 100, 2)
                                            : 0;
                                        $progressClass = $progress >= 100 ? 'bg-success' : ($progress >= 50 ? 'bg-warning' : 'bg-danger');
                                    @endphp
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar {{ $progressClass }}" 
                                             style="width: {{ $progress }}%">
                                            {{ $progress }}%
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('production.update', $production) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="model" class="form-label">
                                    <i class="fas fa-cube text-info"></i>
                                    Model <span class="text-danger">*</span>
                                </label>
                                <div class="autocomplete-container">
                                    <input type="text" 
                                           class="form-control @error('model') is-invalid @enderror" 
                                           id="model" 
                                           name="model" 
                                           value="{{ old('model', $production->model) }}" 
                                           placeholder="Type to search models (e.g., 1-, 2-, etc.)"
                                           autocomplete="off"
                                           required>
                                    <div class="autocomplete-dropdown" id="modelDropdown"></div>
                                </div>
                                @error('model')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="fas fa-info-circle"></i>
                                    Start typing to filter models. You can search by any part of the model name.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="gold_color" class="form-label">
                                    <i class="fas fa-palette text-warning"></i>
                                    Gold Color <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('gold_color') is-invalid @enderror" 
                                        id="gold_color" 
                                        name="gold_color" 
                                        required>
                                    <option value="">Select gold color...</option>
                                    @foreach($goldColors as $color)
                                        <option value="{{ $color }}" 
                                                {{ old('gold_color', $production->gold_color) == $color ? 'selected' : '' }}>
                                            {{ $color }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('gold_color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="fas fa-info-circle"></i>
                                    Select the gold color for this production order.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="quantity" class="form-label">
                                    <i class="fas fa-calculator text-success"></i>
                                    Total Quantity <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       class="form-control @error('quantity') is-invalid @enderror" 
                                       id="quantity" 
                                       name="quantity" 
                                       value="{{ old('quantity', $production->quantity) }}" 
                                       min="1" 
                                       required>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="fas fa-info-circle"></i>
                                    Total quantity to be produced.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="not_finished" class="form-label">
                                    <i class="fas fa-clock text-warning"></i>
                                    Not Finished <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       class="form-control @error('not_finished') is-invalid @enderror" 
                                       id="not_finished" 
                                       name="not_finished" 
                                       value="{{ old('not_finished', $production->not_finished) }}" 
                                       min="0" 
                                       required>
                                @error('not_finished')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="fas fa-info-circle"></i>
                                    Number of items still to be produced.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="order_date" class="form-label">
                                    <i class="fas fa-calendar-alt text-primary"></i>
                                    Order Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control @error('order_date') is-invalid @enderror" 
                                       id="order_date" 
                                       name="order_date" 
                                       value="{{ old('order_date', $production->order_date->format('Y-m-d')) }}" 
                                       required>
                                @error('order_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Note:</strong> The "Not Finished" count decreases automatically when items are added to shops without the "talab" option checked.
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ route('production.index') }}" class="btn btn-secondary me-md-2">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Order
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Model data from server
        const models = @json($models->pluck('model'));
        
        // Autocomplete functionality
        class ModelAutocomplete {
            constructor(inputElement, dropdownElement, models) {
                this.input = inputElement;
                this.dropdown = dropdownElement;
                this.models = models;
                this.filteredModels = [];
                this.selectedIndex = -1;
                
                this.init();
            }
            
            init() {
                this.input.addEventListener('input', (e) => this.onInput(e));
                this.input.addEventListener('keydown', (e) => this.onKeyDown(e));
                this.input.addEventListener('focus', (e) => this.onFocus(e));
                
                // Close dropdown when clicking outside
                document.addEventListener('click', (e) => {
                    if (!this.input.contains(e.target) && !this.dropdown.contains(e.target)) {
                        this.hideDropdown();
                    }
                });
            }
            
            onInput(e) {
                const query = e.target.value.toLowerCase().trim();
                
                if (query.length === 0) {
                    this.hideDropdown();
                    return;
                }
                
                this.filteredModels = this.models.filter(model => 
                    model.toLowerCase().includes(query)
                ).slice(0, 10); // Limit to 10 results for performance
                
                this.selectedIndex = -1;
                this.showDropdown();
            }
            
            onKeyDown(e) {
                if (!this.isDropdownVisible()) return;
                
                switch(e.key) {
                    case 'ArrowDown':
                        e.preventDefault();
                        this.selectedIndex = Math.min(this.selectedIndex + 1, this.filteredModels.length - 1);
                        this.updateHighlight();
                        break;
                        
                    case 'ArrowUp':
                        e.preventDefault();
                        this.selectedIndex = Math.max(this.selectedIndex - 1, -1);
                        this.updateHighlight();
                        break;
                        
                    case 'Enter':
                        e.preventDefault();
                        if (this.selectedIndex >= 0) {
                            this.selectModel(this.filteredModels[this.selectedIndex]);
                        }
                        break;
                        
                    case 'Escape':
                        this.hideDropdown();
                        break;
                }
            }
            
            onFocus(e) {
                if (this.input.value.trim().length > 0) {
                    this.onInput(e);
                }
            }
            
            showDropdown() {
                if (this.filteredModels.length === 0) {
                    this.dropdown.innerHTML = '<div class="no-results">No models found</div>';
                } else {
                    this.dropdown.innerHTML = this.filteredModels.map((model, index) => 
                        `<div class="autocomplete-item" data-index="${index}" data-model="${model}">
                            ${this.highlightMatch(model, this.input.value)}
                         </div>`
                    ).join('');
                    
                    // Add click listeners to items
                    this.dropdown.querySelectorAll('.autocomplete-item').forEach(item => {
                        item.addEventListener('click', () => {
                            this.selectModel(item.dataset.model);
                        });
                    });
                }
                
                this.dropdown.style.display = 'block';
            }
            
            hideDropdown() {
                this.dropdown.style.display = 'none';
                this.selectedIndex = -1;
            }
            
            isDropdownVisible() {
                return this.dropdown.style.display === 'block';
            }
            
            updateHighlight() {
                this.dropdown.querySelectorAll('.autocomplete-item').forEach((item, index) => {
                    item.classList.toggle('highlighted', index === this.selectedIndex);
                });
            }
            
            selectModel(model) {
                this.input.value = model;
                this.hideDropdown();
                this.input.focus();
                
                // Trigger change event for form validation
                this.input.dispatchEvent(new Event('change'));
            }
            
            highlightMatch(text, query) {
                if (!query.trim()) return text;
                
                const regex = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
                return text.replace(regex, '<strong>$1</strong>');
            }
        }
        
        // Initialize autocomplete when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            const modelInput = document.getElementById('model');
            const modelDropdown = document.getElementById('modelDropdown');
            
            new ModelAutocomplete(modelInput, modelDropdown, models);
        });
    </script>
</body>
</html>