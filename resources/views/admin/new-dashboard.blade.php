<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Dashboard</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        .dashboard-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }
        .card {
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 10px;
            width: 300px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="card">
            <h2>Total Weight Sold by Year and Shop</h2>
            @foreach($totalWeightSoldByYearAndShop as $year => $shops)
                <h3>{{ $year }}</h3>
                <ul>
                    @foreach($shops as $shopName => $weight)
                        <li>{{ $shopName }}: {{ number_format($weight, 2) }} g</li>
                    @endforeach
                </ul>
            @endforeach
        </div>
        <div class="card">
            <h2>Total Weight in Inventory</h2>
            <p>{{ number_format($totalWeightInventory, 2) }} g</p>
        </div>
    </div>
</body>
</html>
