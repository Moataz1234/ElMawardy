@extends('layouts.models_table')

@section('content')
    <style>
        .tabs {
            display: flex;
            margin-bottom: 20px;
        }

        .tab {
            padding: 10px 20px;
            margin-right: 10px;
            text-decoration: none;
            color: #000;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .tab.active {
            background-color: #007bff;
            color: #fff;
            border-color: #007bff;
        }
    </style>
  
    <div style="display: flex;flex-wrap:wrap" class="sidebar">
        <form method="GET" action="{{ route('models.index') }}" class="search-form">
            <input type="text" name="search" placeholder="Search by model name" value="{{ request('search') }}"
                class="sidebar-input">
                
        <input type="hidden" name="tab" value="{{ request('tab') }}">
            <button type="submit" class="sidebar-button">Search</button>
        </form>
        <a class="reset-button" style="padding: 10px" href="{{ route('models.create') }}">Add New Model</a>
    </div>

    <!-- Tabs -->
    <div class="tabs">
        <a href="{{ route('models.index', ['tab' => 'all', 'search' => request('search')]) }}"
            class="tab {{ request('tab') !== 'talabat' ? 'active' : '' }}">
            All Models
        </a>
        <a href="{{ route('models.index', ['tab' => 'talabat', 'search' => request('search')]) }}"
            class="tab {{ request('tab') === 'talabat' ? 'active' : '' }}">
            Talabat
        </a>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Model</th>
                {{-- <th>SKU</th> --}}
                <th>Images</th>
                {{-- <th>Website Image</th> --}}
                <th>Stars</th>
                <th>Source</th>
                <th>First Production</th>
                <th>Semi or No </th>
                <th>Avg_Of_Stones</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($models as $model)
                <tr>
                    <td>{{ $model->model ?? $model->talabat }}</td>
                    {{-- <td>{{ $model->SKU }}</td> --}}
                    <td>
                        @if ($model->scanned_image)
                            <img src="{{ asset('storage/' . $model->scanned_image) }}" alt="Scanned Image"
                                style="max-width: 100px; max-height: 100px;">
                        @endif
                    </td>
                    {{-- <td>
                        @if ($model->website_image)
                            <img src="{{ asset('storage/' . $model->website_image) }}" alt="Website Image"
                                style="max-width: 100px; max-height: 100px;">
                        @endif
                    </td> --}}
                    <td>{{ $model->stars }}</td>
                    <td>{{ $model->source }}</td>
                    <td>{{ $model->first_production }}</td>
                    <td>{{ $model->semi_or_no }}</td>
                    <td>{{ $model->average_of_stones }}</td>
                    <td>
                        <a class="navbar-link" style="color:blue" href="{{ route('models.edit', $model) }}">Edit</a>
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
