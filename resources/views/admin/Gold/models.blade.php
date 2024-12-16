@extends('layouts.models_table')

@section('content')
    @include('profile.partials.admin.models')
@endsection

@push('modals')
    @include('profile.partials._image_modal')
@endpush

@push('scripts')

<script src="{{ asset('js/modal.js') }}"></script>
<script src="{{ asset('js/checkbox-selection.js') }}"></script>
@endpush
{{-- 
<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test View</title>
</head>
<body>
    <div class="container mt-5">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Model</th>
                    <th>SKU</th>
                    <th>Category</th>
                    <th> Serial Number</th>
                    <th> Weight</th>
                    <th>Avg Weight</th>
                </tr>
            </thead>
            <tbody>
                @foreach($models as $model)
                    @foreach($model->goldItems as $goldItem)
                        <tr>
                            <td>{{ $model->model }}</td>
                            <td>{{ $model->SKU }}</td>
                            <td>{{ $model->category }}</td>
                            <td>{{ $goldItem->serial_number }}</td>
                            <td>{{ $goldItem->weight }}</td>
                            <td>{{ $model->goldItemsAvg->stones_weight ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html> --}}
