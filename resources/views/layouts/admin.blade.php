<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    
    <link href="{{ asset('css/first_page.css') }}" rel="stylesheet">
    <link href="{{ asset('css/modal.css') }}" rel="stylesheet">
</head>
<body>
    @include('dashboard')
    {{-- @include('components.pagination') --}}
    <div class="layout-container">
        <main class="main-content">
            @yield('content')
        </main>
    </div>

    @stack('modals')
    @stack('scripts')
</body>
</html>
