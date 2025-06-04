@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Edit Production Order #{{ $production->id }}</h4>
                    <a href="{{ route('production.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>

                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('production.update', $production) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="model" class="form-label">Model <span class="text-danger">*</span></label>
                            <select class="form-select @error('model') is-invalid @enderror" 
                                    id="model" 
                                    name="model" 
                                    required>
                                <option value="">Select a model...</option>
                                @foreach($models as $model)
                                    <option value="{{ $model->model }}" 
                                            {{ old('model', $production->model) == $model->model ? 'selected' : '' }}>
                                        {{ $model->model }}
                                    </option>
                                @endforeach
                            </select>
                            @error('model')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="quantity" class="form-label">Total Quantity <span class="text-danger">*</span></label>
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
                            <div class="form-text">Total quantity to be produced.</div>
                        </div>

                        <div class="mb-3">
                            <label for="not_finished" class="form-label">Not Finished <span class="text-danger">*</span></label>
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
                            <div class="form-text">Number of items still to be produced.</div>
                        </div>

                        <div class="mb-3">
                            <label for="order_date" class="form-label">Order Date <span class="text-danger">*</span></label>
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
@endsection 