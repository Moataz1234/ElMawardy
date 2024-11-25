<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sold Items List</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/modal.css') }}" rel="stylesheet">
</head>
<body>
    <table class="table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Serial Number</th>
                <th>Shop Name</th>
                <th>Kind</th>
                <th>Model</th>
                <th>Gold Color</th>
                <th>Metal Purity</th>
                <th>Weight</th>
                <th>Sold Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($goldItems as $item)
                <tr>
                    <td><img src="{{ asset($item->link) }}" alt="Image" width="50" class="img-thumbnail"></td>
                    <td>{{ $item->serial_number }}</td>
                    <td>{{ $item->shop_name }}</td>
                    <td>{{ $item->kind }}</td>
                    <td>{{ $item->model }}</td>
                    <td>{{ $item->gold_color }}</td>
                    <td>{{ $item->metal_purity }}</td>
                    <td>{{ $item->weight }}</td>
                    <td>{{ $item->sold_date }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $goldItems->links('pagination::bootstrap-4') }}
</body>
</html>

