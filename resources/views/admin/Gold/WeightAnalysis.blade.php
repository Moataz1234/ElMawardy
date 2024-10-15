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
</body>
</html>
