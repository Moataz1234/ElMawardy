<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Gold Items Average Stones Weight</title>
    <link href="{{ asset('css/create_form.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <!-- Include the navbar -->
    @include('components.navbar')

    <h1>Add New Gold Items Average Stones Weight</h1>

    <form class="create-form" action="{{ route('admin.gold_items_avg.store') }}" method="POST">
        @csrf
        <!-- Container for dynamic fields -->
        <div id="dynamic-field-container">
            <!-- Initial input row -->
            <div class="dynamic-field">
                <div style="width: 40%" class="form-group">
                    <label for="model">Model:</label>
                    <input list="models" name="model[]" id="model" required
                        onblur="checkModelExists(this, '{{ route('models.create') }}')">
                    <datalist id="models">
                        @foreach ($models as $model)
                            <option value="{{ $model->model }}"></option>
                        @endforeach
                    </datalist>
                </div>
                <div style="width: 40%" class="form-group">
                    <label for="stones_weight">Stones Weight</label>
                    <input type="number" name="stones_weight[]" step="0.01" required>
                </div>
            </div>
        </div>

        <!-- Buttons for adding and removing rows -->
        <div class="button-group">
            <button type="button" id="add-row" class="btn btn-secondary">Add Another Item</button>
            <button type="button" id="remove-row" class="btn btn-danger" style="display: none;">Remove Last
                Item</button>
        </div>

        <br>

        <!-- Submit button -->
        <button type="submit" class="btn btn-primary">Save</button>
        <a href="{{ route('admin.gold_items_avg.index') }}" class="btn btn-secondary">Cancel</a>
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function checkModelExists(modelInput, createRoute) {
            const model = modelInput.value.trim();

            if (model) {
                // Make an AJAX request to check if the model exists
                fetch(`/check-model-exists?model=${model}`)
                    .then(response => response.json())
                    .then(data => {
                        if (!data.exists) {
                            // Construct the URL
                            const url = `${createRoute}?model=${encodeURIComponent(model)}`;
                            console.log('Redirecting to:', url); // Debugging
                            window.location.href = url;
                        }
                    });
            }
        }
        document.getElementById('add-row').addEventListener('click', function() {
            const dynamicFieldContainer = document.getElementById('dynamic-field-container');

            // Create a new input row
            const newRow = document.createElement('div');
            newRow.classList.add('dynamic-field');

            newRow.innerHTML = `
                <div style="width: 40%" class="form-group">
                    <label for="model">Model:</label>
                    <input list="models" name="model[]" required>
                    <datalist id="models">
                        @foreach ($models as $model)
                            <option value="{{ $model->model }}"></option>
                        @endforeach
                    </datalist>
                </div>
                <div style="width: 40%" class="form-group">
                    <label for="stones_weight">Stones Weight</label>
                    <input type="number" name="stones_weight[]" step="0.01" required>
                </div>
            `;

            // Append the new row to the container
            dynamicFieldContainer.appendChild(newRow);

            // Show the "Remove Last Item" button if there is more than one row
            const removeButton = document.getElementById('remove-row');
            if (dynamicFieldContainer.children.length > 1) {
                removeButton.style.display = 'inline-block';
            }
        });

        document.getElementById('remove-row').addEventListener('click', function() {
            const dynamicFieldContainer = document.getElementById('dynamic-field-container');

            // Remove the last row if there is more than one row
            if (dynamicFieldContainer.children.length > 1) {
                dynamicFieldContainer.removeChild(dynamicFieldContainer.lastChild);
            }

            // Hide the "Remove Last Item" button if only one row remains
            if (dynamicFieldContainer.children.length === 1) {
                this.style.display = 'none';
            }
        });
    </script>
</body>

</html>
