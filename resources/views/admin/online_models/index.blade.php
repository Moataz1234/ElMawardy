@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1>Online Models Management</h1>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('online-models.create') }}" class="btn btn-primary">Add New SKU</a>
            <a href="{{ route('online-models.import') }}" class="btn btn-success">Import from Excel</a>
            <form method="POST" action="{{ route('online-models.clear') }}" class="d-inline" onsubmit="return confirm('Are you sure you want to clear all online models?')">
                @csrf
                <button type="submit" class="btn btn-danger">Clear All</button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3>Online Models ({{ $onlineModels->total() }})</h3>
            <p>These SKUs will be shown in the WordPress API</p>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>SKU</th>
                        <th>Notes</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($onlineModels as $model)
                        <tr>
                            <td>{{ $model->id }}</td>
                            <td>{{ $model->sku }}</td>
                            <td>{{ $model->notes }}</td>
                            <td>{{ $model->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <form method="POST" action="{{ route('online-models.destroy', $model->id) }}" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No online models found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $onlineModels->links() }}
        </div>
    </div>
</div>
@endsection 