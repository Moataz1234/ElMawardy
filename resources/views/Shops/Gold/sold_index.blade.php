@extends('layouts.sold_table')

@section('content')
    {{-- @include('profile.partials._price_table') --}}
    @include('profile.partials.sold_list')
@endsection

@push('modals')
    @include('profile.partials._image_modal')
@endpush

@push('scripts')
{{-- <script>
    const sellRouteUrl = "{{ route('shop-items.bulkSellForm') }}";
    const transferRouteUrl = "{{ route('shop-items.bulkTransferForm') }}";
</script> --}}
<script src="{{ asset('js/modal.js') }}"></script>
<script src="{{ asset('js/checkbox-selection.js') }}"></script>
@endpush