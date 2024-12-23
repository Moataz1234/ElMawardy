<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@yield('title', 'Customer Orders')</title>
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <link href="{{ asset('css/order-details.css') }}" rel="stylesheet">
        <link href="{{ asset('css/first_page.css') }}" rel="stylesheet">
        <link href="{{ asset('css/order_index.css') }}" rel="stylesheet">
        <link href="{{ asset('css/sidebar.css') }}" rel="stylesheet">
        <link href="{{ asset('css/navbar.css') }}" rel="stylesheet">
        <link href="{{ asset('css/style.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOM8kNq7/8z2zVw5U5NAuTp6WVsMSXJ1pO9aX1l" crossorigin="anonymous">
    
        @include('components.navbar')
        @include('profile.partials.Rabea.navigation')
       
        @yield('styles')
    </head>
<body>
    <div class="layout-container">
        {{-- @include('profile.partials.Rabea.navigation') --}}
        @yield('content')
    </div>

    @yield('scripts')
    <script src="Scripts/Scripts.js"></script>
  
</body>
</html>