@extends('layouts.other_table')

@section('content')
    @include('profile.partials.Shops.all_items')
@endsection

@push('modals')
    @include('profile.partials._image_modal')
@endpush

@push('scripts')

<script src="{{ asset('js/modal.js') }}"></script>
<script src="{{ asset('js/checkbox-selection.js') }}"></script>
@endpush
