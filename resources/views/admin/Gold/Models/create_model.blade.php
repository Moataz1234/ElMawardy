@extends('layouts.models_table')

@section('content')
    <form action="{{ route('models.store') }}" method="POST" class="custom-form" enctype="multipart/form-data">
        @csrf
        <div>
            <label for="model">Model Name:</label>
            <input type="text" id="model" name="model" value="{{ $model ?? '' }}" required>
            <label for="talabat">Talabat:</label>
            <input type="checkbox" id="talabat" name="talabat">
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
            <input type="date" id="first_production" name="first_production" value="Production">
        </div>
        <div>
            <label for="semi_or_no">Semi or No:</label>
            <input type="text" id="semi_or_no" name="semi_or_no">
        </div>
        <button type="submit">Add Model</button>
    </form>

    <script>
        let debounceTimer;

        document.getElementById('model').addEventListener('input', function() {
            clearTimeout(debounceTimer); // Clear the previous timer

            const prefix = this.value.trim(); // Get the value from the input field
            const isTalabat = document.getElementById('talabat').checked; // Check if Talabat is selected

            // Set a delay before making the AJAX request
            debounceTimer = setTimeout(() => {
                if (prefix) {
                    fetch(`/generate-model?prefix=${prefix}&talabat=${isTalabat}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.model) {
                                // Update the input field with the generated model
                                this.value = data.model;
                            }
                        });
                }
            }, 300); // 300ms delay
        });
    </script>
@endsection