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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            const toggleButton = document.createElement('button');
            toggleButton.textContent = 'Toggle Sidebar';
            toggleButton.style.position = 'fixed';
            toggleButton.style.top = '10px';
            toggleButton.style.left = '10px';
            toggleButton.style.zIndex = '1000';
            toggleButton.onclick = function() {
                sidebar.classList.toggle('collapsed');
            };
            document.body.appendChild(toggleButton);

            const sidebarItems = document.querySelectorAll('.sidebar-item');
            sidebarItems.forEach(item => {
                item.setAttribute('title', item.textContent.trim());
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
