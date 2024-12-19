@extends('layouts.models_table')

@section('content')
    <form action="{{ route('models.update', $model) }}" method="POST" class="custom-form" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div>
            <label for="model">Model Name:</label>
            <input type="text" id="model" name="model" value="{{ $model->model }}" required>
        </div>
        <div>
            <label for="scanned_image">Scanned Image:</label>
            @if($model->scanned_image)
                <img src="{{ asset('storage/' . $model->scanned_image) }}" alt="Current Scanned Image" style="max-width: 200px; max-height: 200px;">
            @endif
            <input type="file" id="scanned_image" name="scanned_image" accept="image/*">
        </div>
        <div>
            <label for="website_image">Website Image:</label>
            @if($model->website_image)
                <img src="{{ asset('storage/' . $model->website_image) }}" alt="Current Website Image" style="max-width: 200px; max-height: 200px;">
            @endif
            <input type="file" id="website_image" name="website_image" accept="image/*">
        </div>
        <div>
            <label for="category">Category:</label>
            <input type="text" id="category" name="category" value="{{ $model->category }}">
        </div>
        <div>
            <label for="source">Source:</label>
            <input type="text" id="source" name="source" value="{{ $model->source }}">
        </div>
        <div>
            <label for="first_production">First Production:</label>
            <br>
            <input type="date" id="first_production" name="first_production" value="{{ $model->first_production }}">
        </div>
        <div>
            <label for="semi_or_no">Semi or No:</label>
            <input type="text" id="semi_or_no" name="semi_or_no" value="{{ $model->semi_or_no }}">
        </div>
        <div>
            <label for="average_of_stones">Average of Stones:</label>
            <input type="number" id="average_of_stones" name="average_of_stones" step="0.01" value="{{ $model->average_of_stones }}">
        </div>
        <button type="submit">Update Model</button>
    </form>
@endsection