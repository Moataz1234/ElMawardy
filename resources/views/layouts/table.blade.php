<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Shop's Items</title>
    
    <link href="{{ asset('css/first_page.css') }}" rel="stylesheet">
    <link href="{{ asset('css/modal.css') }}" rel="stylesheet">
    <link href="{{ asset('css/navbar.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sidebar.css') }}" rel="stylesheet">

</head>
<body>
    <div class="layout-container">
        @include('components.sidebar')
        <main class="main-content">
            @yield('content')
        </main>
    </div>

    @stack('modals')
    @stack('scripts')
</body>
</html>
