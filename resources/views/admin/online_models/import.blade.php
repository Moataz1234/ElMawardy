@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1>Import Online Models from Excel</h1>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('online-models.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3>Import from Excel</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('online-models.import.process') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="excel_file" class="form-label">Excel File</label>
                    <input type="file" class="form-control @error('excel_file') is-invalid @enderror" id="excel_file" name="excel_file" required>
                    @error('excel_file')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        Upload an Excel file (.xlsx, .xls) or CSV file with SKUs in the first column.
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Import</button>
            </form>
        </div>

        <div class="card-footer">
            <h4>Excel Format</h4>
            <p>The Excel file should have the following format:</p>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Notes (optional)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>G12345</td>
                        <td>Example product</td>
                    </tr>
                    <tr>
                        <td>G67890</td>
                        <td>Second product</td>
                    </tr>
                </tbody>
            </table>
            <p>Note: Only SKUs that exist in your Models database will be imported.</p>
        </div>
    </div>
</div>
@endsection 