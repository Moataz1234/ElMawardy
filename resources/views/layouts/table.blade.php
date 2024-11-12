<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Shop's Items</title>
    
    <link href="{{ asset('css/first_page.css') }}" rel="stylesheet">
    <link href="{{ asset('css/modal.css') }}" rel="stylesheet">

</head>
<body>
    @include('dashboard')
    
    <main>
        @yield('content')
    </main>

    @stack('modals')
    @stack('scripts')
</body>
</html>