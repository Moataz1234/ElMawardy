@extends('layouts.models_table')

@section('content')
    <form action="{{ route('talabat.store') }}" method="POST" class="custom-form" enctype="multipart/form-data">
        @csrf
        <div>
            <label for="model">Model Name:</label>
            <input type="text" id="model" name="model" required>
        </div>
        <div>
            <label for="scanned_image">Scanned Image:</label>
            <input type="file" id="scanned_image" name="scanned_image" accept="image/*">
        </div>
        <div>
            <label for="stars">Stars:</label>
            <input type="text" id="stars" name="stars" value="***">
        </div>
        <div>
            <label for="source">Source:</label>
            <input type="text" id="source" name="source" value="Production">
        </div>
        <div>
            <label for="first_production">First Production:</label>
            <br>
            <input type="date" id="first_production" name="first_production">
        </div>
        <div>
            <label for="semi_or_no">Semi or No:</label>
            <input type="text" id="semi_or_no" name="semi_or_no">
        </div>
        <button type="submit">Add Talabat</button>
    </form>
@endsection
