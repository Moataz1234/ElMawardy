
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
                <div class="horizontal-line"></div>
                <h3>Gold Color</h3>
                @foreach ($gold_color as $gold_color)
                <label>
                    <input type="checkbox" name="gold_color[]" value="{{ $gold_color }}"
                        {{ in_array($gold_color, request('gold_color', [])) ? 'checked' : '' }}>
                    <span class="custom-checkbox-purity"></span> {{ $gold_color }}
                </label>
                @endforeach
    
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
                    <a href="{{ url()->current() }}" class="reset-button">Reset</a>
                </div>
            </div>
        </form>
    </div>
    @include('components.pagination')

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