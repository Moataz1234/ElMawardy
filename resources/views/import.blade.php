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
    </div>
  
    <div class="container">
        <!-- Update Sources for Gold Items -->
        <div class="card mt-4">
            <div class="card-header">
                Update Sources for Gold Items
            </div>
            <div class="card-body">
                <form action="{{ route('import-gold-items.update-sources') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="gold-source-file">Select Excel File</label>
                        <input type="file" class="form-control" id="gold-source-file" name="file" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Gold Items Sources</button>
                </form>
            </div>
        </div>

        <!-- Update Sources for Sold Items -->
        <div class="card mt-4">
            <div class="card-header">
                Update Sources for Sold Items
            </div>
            <div class="card-body">
                <form action="{{ route('import-sold-items.update-sources') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="sold-source-file">Select Excel File</label>
                        <input type="file" class="form-control" id="sold-source-file" name="file" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Sold Items Sources</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
