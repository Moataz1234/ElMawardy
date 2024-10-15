<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weight Analysis</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <h1>Weight Analysis</h1>
    <p>Total Weight of All Gold Items: {{ $totalGoldItemWeight }} grams</p>
    <p>Total Weight of Gold Items Sold Today: {{ $totalGoldItemSoldWeightToday }} grams</p>
    <h2>Category Analysis</h2>
    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th>Total Count</th>
                <th>Total Weight (grams)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($kindAnalysis as $kind => $data)
                <tr>
                    <td>{{ $kind }}</td>
                    <td>{{ $data['count'] }}</td>
                    <td>{{ $data['weight'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Shop Analysis</h2>
    <table>
        <thead>
            <tr>
                <th>Shop Name</th>
                <th>Total Count</th>
                <th>Total Weight (grams)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($shopAnalysis as $shopName => $data)
                <tr>
                    <td>{{ $shopName }}</td>
                    <td>{{ $data['count'] }}</td>
                    <td>{{ $data['weight'] }}</td>
                </tr>
            @endforeach
        </tbody>
</body>
</html>
