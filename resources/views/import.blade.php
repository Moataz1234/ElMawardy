<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Excel</title>
</head>
<body>
  
    <h2>Upload Excel File Gold items</h2>
    <form action="{{ route('import.excel') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" required>
        <button type="submit">Import Gold Items Data</button>
    </form>
    <h2>Upload Excel File Gold items sold</h2>
    <form action="{{ route('import.excel-sold') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" required>
        <button type="submit">Import Sold Items Data</button>
    </form>
    <h2>Upload Excel File Models</h2>
    <form action="{{ route('import.excel-models') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" required>
        <button type="submit">Import Models Data</button>
    </form>
    <h2>Download Daily Report </h2>
    <div class="print-section">
        <a href="{{ route('daily.report.pdf') }}" class="btn btn-primary">Download PDF Report</a>
    </div></body>
</html>
