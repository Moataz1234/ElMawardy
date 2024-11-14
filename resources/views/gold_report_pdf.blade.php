<!DOCTYPE html>
<html>
<head>
    <title>Gold Report</title>
    <style>
        .page-break {
            page-break-after: always;
        }
        .report-section {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    @foreach($reportsData as $model => $data)
    <div class="report-section">
        <div class="header">
            <h2>Model: {{ $model }}</h2>
            {{-- <p>At Workshop: {{ $data['workshop_count'] }}</p> --}}
            <p>Order Date: {{ $data['order_date'] }}</p>
            <p>Gold Color: {{ $data['gold_color'] }}</p>
            <p>Source: {{ $data['source'] }}</p>
        </div>

        <table>
            <tr>
                <th>Model</th>
                <th>Remaining</th>
                <th>Total Production</th>
                <th>Total Sold</th>
            </tr>
            <tr>
                <td>{{ $model }}</td>
                <td>{{ $data['remaining'] }}</td>
                <td>{{ $data['total_production'] }}</td>
                <td>{{ $data['total_sold'] }}</td>
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
                <td>{{ $shop }}</td>
                <td>{{ $counts['all_rests'] }}</td>
                <td>{{ $counts['white_gold'] }}</td>
                <td>{{ $counts['yellow_gold'] }}</td>
                <td>{{ $counts['rose_gold'] }}</td>
            </tr>
            @endforeach
        </table>
    </div>
    <div class="page-break"></div>
    @endforeach
</body>
</html>