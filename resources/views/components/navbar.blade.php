<!DOCTYPE html>
<html>
    <x-app-layout>
        @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    </x-app-layout>
    <head>@if (auth()->user()->usertype === 'admin')
        @include('navbars.admin-navbar')
    @elseif (auth()->user()->usertype === 'user')
        @include('navbars.user-navbar')
    @elseif (auth()->user()->usertype === 'rabea')
        @include('navbars.rabea-navbar')
    @endif
    </head>
<body>

</body>
</html>