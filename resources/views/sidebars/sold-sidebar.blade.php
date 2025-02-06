{{-- <div class="profile-card relative">
    <label  for="dropdown-toggle" class="profile-info cursor-pointer font-medium text-gray-700 hover:text-gray-900">
        <h4 class="name">{{ Auth::user()->name }}</h4>
    </label>

    <div class="dropdown-menu absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <x-dropdown-link :href="route('logout')"
                             onclick="event.preventDefault(); this.closest('form').submit();"
                             class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                {{ __('Log Out') }}
            </x-dropdown-link>
        </form>
    </div>
</div> --}}

<nav class="second-section">
    <form action="{{ url()->current() }}" method="GET" class="search-form">
        @foreach(request()->except(['search', 'page', 'search_type']) as $key => $value)
            @if(is_array($value))
                @foreach($value as $item)
                    <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                @endforeach
            @else
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endif
        @endforeach
        <select name="search_type" class="search-type-dropdown">
            <option value="model" {{ request('search_type') == 'model' ? 'selected' : '' }}>Model</option>
            <option value="serial_number" {{ request('search_type') == 'serial_number' ? 'selected' : '' }}>Serial Number</option>
        </select>
        <input type="text" name="search" class="search-input" 
               value="{{ request('search') }}" placeholder="Search...">
        <button type="submit">Search</button>
    </form>
</nav>
<div class="container">
    <div class="filter-search">
        <form method="GET" action="{{ url()->current() }}" id="filterForm">
            @if(request('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
    
            <div class="radio-group">
                <h3>Sort By</h3>
                <div class="page-2">
                    <label>
                        <input type="radio" name="sort" value="serial_number" {{ request('sort') == 'serial_number' ? 'checked' : '' }}>
                        <span class="custom-radio"></span> Serial Number
                    </label>
                    <label>
                        <input type="radio" name="sort" value="model" {{ request('sort') == 'model' ? 'checked' : '' }}>
                        <span class="custom-radio"></span> Model
                    </label>
                    <label>
                        <input type="radio" name="sort" value="kind" {{ request('sort') == 'kind' ? 'checked' : '' }}>
                        <span class="custom-radio"></span> Kind
                    </label>
                    <label>
                        <input type="radio" name="sort" value="sold_date" {{ request('sort') == 'sold_date' ? 'checked' : '' }}>
                        <span class="custom-radio"></span> Sold Date
                    </label>
                    <label>
                        <input type="radio" name="sort" value="quantity" {{ request('sort') == 'quantity' ? 'checked' : '' }}>
                        <span class="custom-radio"></span> Quantity
                    </label>
                </div>
                <div class="horizontal-line"></div>
                {{-- <h3>Category</h3>
                @foreach ($categories as $item)
                <label>
                    <input type="checkbox" name="categories[]" value="{{ $item }}"
                    {{ in_array($item, request('categories', [])) ? 'checked' : '' }}>
                    <span class="custom-checkbox-purity"></span> {{ $category }}
                </label>
                @endforeach
     --}}
                {{-- <div class="horizontal-line"></div>
                <h3>Gold Color</h3>
                @foreach ($gold_color as $gold_color)
                <label>
                    <input type="checkbox" name="gold_color[]" value="{{ $gold_color }}"
                        {{ in_array($gold_color, request('gold_color', [])) ? 'checked' : '' }}>
                    <span class="custom-checkbox-purity"></span> {{ $gold_color }}
                </label>
                @endforeach
     --}}
                <div class="horizontal-line"></div>
                <h3>Kind</h3>
                @foreach ($kind as $item)
                <label>
                    <input type="checkbox" name="kind[]" value="{{ $item }}"
                        {{ in_array($item, request('kind', [])) ? 'checked' : '' }}>
                    <span class="custom-checkbox-kind"></span> {{ $item }}
                </label>
                @endforeach
    
                <div class="button-container">
                    <button type="submit" class="reset-button">Set</button>
                    <button type="button" onclick="location.href='{{ url()->current() }}';"  class="reset-button">Reset</a>
                    </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get all filter inputs
            const filterInputs = document.querySelectorAll('#filterForm select, #filterForm input');
            
            // Add change event listener to each input
            filterInputs.forEach(input => {
                input.addEventListener('change', function() {
                    document.getElementById('filterForm').submit();
                });
            });
        });
        </script>