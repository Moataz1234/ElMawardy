<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Shop's Items</title>
    
    <link href="{{ asset('css/first_page.css') }}" rel="stylesheet">
    <link href="{{ asset('css/modal.css') }}" rel="stylesheet">
    {{-- <link href="{{ asset('css/loader.css') }}" rel="stylesheet"> --}}

    {{-- <link href="{{ asset('css/navbar.css') }}" rel="stylesheet"> --}}
{{-- <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet"> --}}

</head>
<body>
    {{-- <x-loader /> --}}
    {{-- @include('components.loader') --}}
    @include('dashboard')
    {{-- @include('components.navbar') --}}
    @include('components.pagination')
    <div class="layout-container">
        <main class="main-content">
            @yield('content')
        </main>
    </div>

    @stack('modals')
    @stack('scripts')
</body>
</html>
