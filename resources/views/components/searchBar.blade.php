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
        @include('searchbars.admin-searchbar')
    @elseif (auth()->user()->usertype === 'user')
        @include('searchbars.user-searchbar')
    @elseif (auth()->user()->usertype === 'rabea')
        @include('searchbars.rabea-searchbar')
    @endif
    </head>
<body>

</body>
</html>