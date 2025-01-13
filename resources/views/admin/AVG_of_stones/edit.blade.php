<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Gold Items Average Stones Weight</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Include the navbar -->
    @include('components.navbar')

    <div class="container mt-4">
        <h1>Edit Gold Items Average Stones Weight</h1>

        <form action="{{ route('admin.gold_items_avg.update', $goldItemsAvg->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="model">Model</label>
                <input type="text" name="model" id="model" class="form-control" value="{{ $goldItemsAvg->model }}" required>
                <small class="text-muted">Enter the model name (must exist in the models table).</small>
            </div>
            <div class="form-group">
                <label for="stones_weight">Stones Weight</label>
                <input type="number" name="stones_weight" id="stones_weight" class="form-control" value="{{ $goldItemsAvg->stones_weight }}" step="0.01" required>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('admin.gold_items_avg.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>