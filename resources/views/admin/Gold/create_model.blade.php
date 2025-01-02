@extends('layouts.models_table')

@section('content')
    <form action="{{ route('models.store') }}" method="POST" class="custom-form" enctype="multipart/form-data">
        @csrf
        <div>
            <label for="model">Model Name:</label>
            <input type="text" id="model" name="model" required>
        </div>
        <div>
            <label for="scanned_image">Scanned Image:</label>
            <input type="file" id="scanned_image" name="scanned_image" accept="image/*">
        </div>
        {{-- <div>
            <label for="website_image">Website Image:</label>
            <input type="file" id="website_image" name="website_image" accept="image/*">
        </div> --}}
        <div>
            <label for="category">Stars:</label>
            <input type="text" id="category" name="category" value="***">
        </div>
        <div>
            <label for="source">Source:</label>
            <input type="text" id="source" name="source" value="Production">
        </div>
        <div>
            <label for="first_production">First Production:</label>
            <br>
            <input type="date" id="first_production" name="first_production" value="Production">
        </div>
        <div>
            <label for="semi_or_no">Semi or No:</label>
            <input type="text" id="semi_or_no" name="semi_or_no">
        </div>
        {{-- <div>
            <label for="average_of_stones">Average of Stones:</label>
            <input type="number" id="average_of_stones" name="average_of_stones" step="0.01">
        </div> --}}
        <button type="submit">Add Model</button>
    </form>
@endsection