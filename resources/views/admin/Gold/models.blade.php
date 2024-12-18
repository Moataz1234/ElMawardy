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
