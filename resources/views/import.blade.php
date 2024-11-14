<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Excel</title>
</head>
<body>
    <h2>Upload Excel File</h2>
    <form action="{{ route('excel.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label for="file">Choose Excel File:</label>
        <input type="file" name="file" id="file" accept=".xlsx, .xls, .csv" required>
        <button type="submit">Upload</button>
    </form>
    <div class="print-section">
        <a href="{{ route('daily.report.pdf') }}" class="btn btn-primary">Download PDF Report</a>
    </div></body>
</html>
