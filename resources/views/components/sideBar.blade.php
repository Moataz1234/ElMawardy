<!DOCTYPE html>
<html>
<head>
    @if (auth()->user()->usertype === 'admin')
        @include('sidebars.admin-sidebar')
    @elseif (auth()->user()->usertype === 'user')
        @include('sidebars.user-sidebar')
    @elseif (auth()->user()->usertype === 'rabea')
        @include('sidebars.rabea-sidebar')
    @endif
</head>
<body>
</body>
</html>
