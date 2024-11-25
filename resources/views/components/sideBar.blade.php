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
</html><div class="sidebar">
    <ul class="sidebar-list">
        <li class="sidebar-item"><a href="{{ route('admin.dashboard') }}" class="sidebar-link">Dashboard</a></li>
        <li class="sidebar-item"><a href="{{ route('gold-items.index') }}" class="sidebar-link">Inventory</a></li>
        <li class="sidebar-item"><a href="{{ route('gold-items.sold') }}" class="sidebar-link">Sold Items</a></li>
        <li class="sidebar-item"><a href="{{ route('orders.index') }}" class="sidebar-link">Orders</a></li>
        <li class="sidebar-item"><a href="{{ route('reports.index') }}" class="sidebar-link">Reports</a></li>
    </ul>
</div>
