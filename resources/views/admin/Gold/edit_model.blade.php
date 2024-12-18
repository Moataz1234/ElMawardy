@extends('layouts.models_table')

@section('content')
    <form action="{{ route('models.update', $model) }}" method="POST" class="custom-form">
        @csrf
        @method('PUT')
        <div>
            <label for="model">Model Name:</label>
            <input type="text" id="model" name="model" value="{{ $model->model }}" required>
        </div>
        <div>
            <label for="SKU">SKU:</label>
            <input type="text" id="SKU" name="SKU" value="{{ $model->SKU }}" required>
        </div>
        <div>
            <label for="category">Category:</label>
            <input type="text" id="category" name="category" value="{{ $model->category }}" required>
        </div>
        <div>
            <label for="source">Source:</label>
            <input type="text" id="source" name="source" value="{{ $model->source }}" required>
        </div>
        <div>
            <label for="first_production">First Production:</label>
            <input type="date" id="first_production" name="first_production" value="{{ $model->first_production }}" required>
        </div>
        <div>
            <label for="semi_or_no">Semi or No:</label>
            <input type="text" id="semi_or_no" name="semi_or_no" value="{{ $model->semi_or_no }}" required>
        </div>
        <div>
            <label for="average_of_stones">Average of Stones:</label>
            <input type="number" id="average_of_stones" name="average_of_stones" step="0.01" value="{{ $model->average_of_stones }}" required>
        </div>
        <button type="submit">Update Model</button>
    </form>
@endsection
