<!DOCTYPE html>
<html>
    {{-- <x-app-layout>
        @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    </x-app-layout> --}}
    <head>@if (auth()->user()->usertype === 'admin')
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
