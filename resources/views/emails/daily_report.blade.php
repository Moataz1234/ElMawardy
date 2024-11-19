<!DOCTYPE html>
<html>
<head>
    <title>Daily Gold Sales Report</title>
</head>
<body>
    <h2>Daily Gold Sales Report</h2>
    <p>Please find attached the daily sales report for {{ now()->format('d-m-Y') }}.</p>
    
    <h3>Summary:</h3>
    <ul>
    @foreach($reportsData as $model => $data)
        <li>Model {{ $model }}: {{ $data['total_sold'] }} items sold</li>
    @endforeach
    </ul>
</body>
</html>