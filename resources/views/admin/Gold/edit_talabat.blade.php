@extends('layouts.models_table')

@section('content')
    <form action="{{ route('talabat.update', $talabat) }}" method="POST" class="custom-form" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div>
            <label for="model">Model Name:</label>
            <input type="text" id="model" name="model" value="{{ $talabat->model }}" required>
        </div>
        <div>
            <label for="scanned_image">Scanned Image:</label>
            @if($talabat->scanned_image)
                <img src="{{ asset('storage/' . $talabat->scanned_image) }}" alt="Current Scanned Image" style="max-width: 200px; max-height: 200px;">
            @endif
            <input type="file" id="scanned_image" name="scanned_image" accept="image/*">
        </div>
        <div>
            <label for="stars">Stars:</label>
            <input type="text" id="stars" name="stars" value="{{ $talabat->stars }}">
        </div>
        <div>
            <label for="source">Source:</label>
            <input type="text" id="source" name="source" value="{{ $talabat->source }}">
        </div>
        <div>
            <label for="first_production">First Production:</label>
            <br>
            <input type="date" id="first_production" name="first_production" value="{{ $talabat->first_production }}">
        </div>
        <div>
            <label for="semi_or_no">Semi or No:</label>
            <input type="text" id="semi_or_no" name="semi_or_no" value="{{ $talabat->semi_or_no }}">
        </div>
        <button type="submit">Update Talabat</button>
    </form>
@endsection
