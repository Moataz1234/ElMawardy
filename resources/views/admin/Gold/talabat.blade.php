@extends('layouts.models_table')

@section('content')
    <div style="display: flex;flex-wrap:wrap" class="sidebar">
        <form  method="GET" action="{{ route('talabat.index') }}" class="search-form">
            <input type="text" name="search" placeholder="Search by talabat name" value="{{ request('search') }}" class="sidebar-input">
            <button type="submit" class="sidebar-button">Search</button>

        </form>
        <a class="reset-button" style="padding: 10px" href="{{ route('talabat.create') }}">Add New Talabat</a>

    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Model</th>
                <th>Scanned Image</th>
                <th>Stars</th>
                <th>Source</th>
                <th>First Production</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($talabat as $talabatItem)
            <tr>
                <td>{{ $talabatItem->model }}</td>
                <td>
                    @if($talabatItem->scanned_image)
                        <img src="{{ asset('storage/' . $talabatItem->scanned_image) }}" alt="Scanned Image" style="max-width: 100px; max-height: 100px;">
                    @endif
                </td>
                <td>{{ $talabatItem->stars }}</td>
                <td>{{ $talabatItem->source }}</td>
                <td>{{ $talabatItem->first_production }}</td>
                <td>
                    <a  class="navbar-link" style="color:blue" href="{{ route('talabat.edit', $talabatItem) }}">Edit</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection

@push('modals')
    @include('profile.partials._image_modal')
    @include('components.models_pagination')
@endpush

@push('scripts')
    <script src="{{ asset('js/modal.js') }}"></script>
    <script src="{{ asset('js/checkbox-selection.js') }}"></script>
@endpush
