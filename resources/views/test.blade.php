<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test View</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Data from Models, Gold Items, and Gold Items Avg</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Model</th>
                    <th>SKU</th>
                    <th>Category</th>
                    <th>Gold Item Serial Number</th>
                    <th>Gold Item Weight</th>
                    <th>Average Stones Weight</th>
                </tr>
            </thead>
            <tbody>
                @foreach($models as $model)
                    @foreach($model->goldItems as $goldItem)
                        <tr>
                            <td>{{ $model->model }}</td>
                            <td>{{ $model->SKU }}</td>
                            <td>{{ $model->category }}</td>
                            <td>{{ $goldItem->serial_number }}</td>
                            <td>{{ $goldItem->weight }}</td>
                            <td>{{ $model->goldItemsAvg->stones_weight ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
