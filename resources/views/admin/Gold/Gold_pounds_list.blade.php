<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gold Pounds List</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body>
    <h1>Gold Pounds List</h1>
    <table>
        <thead>
            <tr>
                <th>Kind</th>
                <th>Weight</th>
                <th>Purity</th>
                <th>Quantity</th>
                <th>Total Weight</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($goldPounds as $pound)
                <tr>
                    <td>{{ $pound->kind }}</td>
                    <td>{{ $pound->weight }}</td>
                    <td>{{ $pound->purity }}</td>
                    <td>{{ $pound->quantity }}</td>
                    <td>{{ $pound->total_weight }}</td>
                    <td>{{ $pound->description }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
