@extends('layouts.models_table')

@section('content')
    <div class="sidebar">
        <form method="GET" action="{{ route('models.index') }}">
            <input type="text" name="search" placeholder="Search by model name" value="{{ request('search') }}">
            <button type="submit">Search</button>
        </form>
        <form method="GET" action="{{ route('models.index') }}">
            <select name="sort">
                <option value="model">Model</option>
                <option value="SKU">SKU</option>
                <option value="category">Category</option>
                <option value="source">Source</option>
                <option value="first_production">First Production</option>
                <option value="semi_or_no">Semi or No</option>
                <option value="average_of_stones">Average of Stones</option>
            </select>
            <select name="direction">
                <option value="asc">Ascending</option>
                <option value="desc">Descending</option>
            </select>
            <button type="submit">Sort</button>
        </form>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Model</th>
                <th>SKU</th>
                <th>Category</th>
                <th>Source</th>
                <th>First Production</th>
                <th>Semi or No</th>
                <th>Average of Stones</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($models as $model)
            <tr>
                <td>{{ $model->model }}</td>
                <td>{{ $model->SKU }}</td>
                <td>{{ $model->category }}</td>
                <td>{{ $model->source }}</td>
                <td>{{ $model->first_production }}</td>
                <td>{{ $model->semi_or_no }}</td>
                <td>{{ $model->average_of_stones }}</td>
                <td>
                    <a href="{{ route('models.edit', $model) }}">Edit</a>
                    <form action="{{ route('models.destroy', $model) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <a href="{{ route('models.create') }}">Add New Model</a>
@endsection

@push('modals')
    @include('profile.partials._image_modal')
@endpush

@push('scripts')

<script src="{{ asset('js/modal.js') }}"></script>
<script src="{{ asset('js/checkbox-selection.js') }}"></script>
@endpush
