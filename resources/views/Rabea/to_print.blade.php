
@extends('layouts.orders_table')

@section('title', 'Customer Orders')

@section('content')
{{-- <div class="main-container"> --}}
    {{-- <div class="sidebar"> --}}

    {{-- @include('sidebars.rabea-sidebar') --}}
    </div>
    <div class="main-content">
        @include('profile.partials.Rabea.to_print_table', ['orders' => $orders])
    </div>
    </div>
@endsection

@section('scripts')
@endsection