<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gold Inventory Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            grid-gap: 5px;
            margin: 20px;
        }

        .header {
            grid-column: span 12;
            background-color: #d98835;
            text-align: center;
            font-size: 24px;
            padding: 10px;
            color: white;
        }

        .first-production, .last-production, .shop, .sold-pieces, .gold-title, .production-date, .production-details, .all-rest, .gold-types, .stats {
            border: 1px solid black;
            text-align: center;
            padding: 5px;
        }

        .first-production, .last-production, .production-details {
            grid-column: span 3;
        }

        .shop {
            grid-column: span 2;
        }

        .sold-pieces {
            grid-column: span 2;
        }

        .gold-title {
            grid-column: span 4;
            background: linear-gradient(to bottom, #f8b500, #f88c00);
            color: white;
            font-weight: bold;
            font-size: 18px;
        }

        .production-date {
            grid-column: span 3;
        }

        .production-details {
            grid-column: span 3;
        }

        .all-rest {
            grid-column: span 3;
            background-color: #e0e0ff;
            font-weight: bold;
        }

        .shops-grid {
            grid-column: span 9;
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            grid-gap: 5px;
        }

        .shop-box {
            border: 1px solid black;
            padding: 5px;
            background-color: #f2f2f2;
            text-align: center;
            min-height: 40px; /* Space for shop data */
        }

        .gold-types {
            grid-column: span 12;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
        }

        .gold-type-box {
            padding: 10px;
            background-color: #f2f2f2;
            border: 1px solid black;
            min-height: 30px; /* Space for dynamic gold type data */
        }

        .stats {
            grid-column: span 3;
            padding: 10px;
            border: 1px solid black;
            background-color: #ffebcc;
            min-height: 30px; /* Space for stats data */
        }

        .image-section {
            grid-column: span 12;
            text-align: center;
            padding: 20px;
            min-height: 150px; /* Space for image */
        }

        .footer {
            grid-column: span 12;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            padding: 10px;
            grid-gap: 5px;
        }

        .footer div {
            padding: 10px;
            border: 1px solid black;
            background-color: #f2f2f2;
            text-align: center;
            min-height: 40px; /* Space for footer content */
        }

        .empty-space {
            min-height: 30px;
        }

    </style>
</head>
<body>

<div class="container">
    <!-- Header -->
    @foreach($modelsData as $model => $data)
        <div class="gold-title">{{ $model }}</div>
        <div class="stats">Total Production: {{ $data['total_production'] }}</div>
        <div class="stats">Total Sold: {{ $data['total_sold'] }}</div>
        <div class="stats">Remaining: {{ $data['remaining'] }}</div>
        <div class="stats">Gold Color: {{ $data['gold_color'] }}</div>
        <div class="stats">Source: {{ $data['source'] }}</div>
        <div class="image-section">
            @if($data['link'])
                <img src="{{ asset($data['link']) }}" alt="Gold Item Image" width="150">
            @else
                <p>No Image Available</p>
            @endif
        </div>
        <div class="shops-grid">
            @foreach($data['shops'] as $shop)
                <div class="shop-box">{{ $shop }}</div>
            @endforeach
        </div>
    @endforeach
</div>

</body>
</html>
