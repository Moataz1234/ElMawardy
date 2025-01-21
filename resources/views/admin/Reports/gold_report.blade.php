<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            padding: 20px;
        }

        /* Container styles */
        .report-page {
            page-break-after: always;
            margin-bottom: 20px;
        }

        .report-page:last-child {
            page-break-after: avoid;
        }

        /* Header section */
        .header-section {
            width: 100%;
            margin-bottom: 20px;
            position: relative;
        }

        .image-section {
            width: 60%;
            float: left;
        }

        .image-section img {
            width: 300px;
            height: auto;
            border: 5px solid #6A6458;
        }

        .info-section {
            width: 35%;
            float: right;
        }

        /* Info boxes */
        .info-box {
            background-color: #6A6458;
            color: white;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            text-align: center;
        }

        .data-text {
            background-color: white;
            color: #333;
            padding: 5px;
            margin-top: 5px;
            font-weight: bold;
        }

        /* Table styles */
        .table-section {
            width: 100%;
            margin-top: 20px;
            clear: both;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th {
            background-color: #6A6458;
            color: white;
            padding: 8px;
            text-align: center;
            border: 1px solid #6A6458;
        }

        td {
            padding: 8px;
            text-align: center;
            border: 1px solid #6A6458;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .shop-name {
            background-color: #8c8c8c;
            color: white;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        /* Filter form styles */
        .filter-form {
            margin-bottom: 20px;
        }

        .filter-form input[type="date"] {
            padding: 5px;
            font-size: 16px;
        }

        .filter-form button {
            padding: 5px 10px;
            font-size: 16px;
            background-color: #6A6458;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .filter-form button:hover {
            background-color: #5a5448;
        }

        .export-button {
            padding: 5px 10px;
            font-size: 16px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-left: 10px;
        }

        .export-button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <h1>Sales Reports</h1>

    <!-- Date Filter Form -->
    <form action="{{ route('reports.view') }}" method="GET" class="filter-form">
        <label for="date">Select Date:</label>
        <input type="date" name="date" value="{{ $selectedDate }}" required>
        <button type="submit">Filter</button>
        <a href="{{ route('reports.view', ['date' => $selectedDate, 'export' => 'pdf']) }}" class="export-button">
            Export as PDF
        </a>
    </form>

    <!-- Report Data -->
    @if(count($reportsData) > 0)
        @foreach($reportsData as $model => $data)
            <div class="report-page">
                <div class="header-section clearfix">
                    <div class="image-section">
                        @if($data['image_path'])
                            <img src="{{ public_path($data['image_path']) }}" alt="Product Image"/>
                        @endif
                    </div>
                    <div class="info-section">
                        <div class="info-box">
                            At Workshop
                            <div class="data-text">{{ $data['workshop_count'] }}</div>
                        </div>
                        <div class="info-box">
                            Order Date
                            <div class="data-text">{{ $data['order_date'] }}</div>
                        </div>
                        <div class="info-box">
                            Gold Color
                            <div class="data-text">{{ $data['gold_color'] }}</div>
                        </div>
                        <div class="info-box">
                            Source
                            <div class="data-text">{{ $data['source'] }}</div>
                        </div>
                    </div>
                </div>

                <div class="table-section">
                    <table>
                        <tr>
                            <th>Model</th>
                            <th>Remaining</th>
                            <th>Total Production</th>
                            <th>Total Sold</th>
                        </tr>
                        <tr>
                            <td>{{ $data['model'] }}</td>
                            <td>{{ $data['remaining'] }}</td>
                            <td>{{ $data['total_production'] }}</td>
                            <td>{{ $data['total_sold'] }}</td>
                        </tr>
                    </table>

                    <table>
                        <tr>
                            <th>First Production</th>
                            <th>Last Production</th>
                            <th>Shop</th>
                            <th> Sold Pieces</th>
                        </tr>
                        <tr>
                            <td>{{ $data['first_sale'] }}</td>
                            <td>{{ $data['last_sale'] }}</td>
                            <td>{{ $data['shop'] }}</td>
                            <td>{{ $data['pieces_sold_today'] }}</td>
                        </tr>
                    </table>

                    <table>
                        <tr>
                            <th>Shop</th>
                            <th>All Rests</th>
                            <th>White Gold</th>
                            <th>Yellow Gold</th>
                            <th>Rose Gold</th>
                        </tr>
                        @foreach($data['shops_data'] as $shop => $counts)
                        <tr>
                            <td class="shop-name">{{ $shop }}</td>
                            <td>{{ $counts['all_rests'] }}</td>
                            <td>{{ $counts['white_gold'] }}</td>
                            <td>{{ $counts['yellow_gold'] }}</td>
                            <td>{{ $counts['rose_gold'] }}</td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        @endforeach
    @else
        <div class="alert alert-info">No sales found for this date</div>
    @endif
</body>
</html>