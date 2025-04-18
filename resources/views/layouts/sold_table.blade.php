<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Sold Items</title>
    
    <link href="{{ asset('css/first_page.css') }}" rel="stylesheet">
    <link href="{{ asset('css/modal.css') }}" rel="stylesheet">

</head>
<body>
    @include('dashboard')
    {{-- @include('sidebars.sold-sidebar') --}}
    {{-- @include('components.navbar') --}}
    @include('components.pagination')
    <main>
        @yield('content')
    </main>

    @stack('modals')
    @stack('scripts')
</body>
</html>