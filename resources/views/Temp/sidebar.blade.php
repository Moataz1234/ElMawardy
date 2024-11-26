{{-- <nav class="second-section">

    <div class="second-section-item search-container">
        <form action="{{ url()->current() }}" method="GET">
            <input type="text" name="search" class="search-input" placeholder="Model Name" value="{{ request('search') }}">
            <button type="submit">Search</button>
        </form>
    </div>
   
  
</nav> --}}

<div class="container">
    <div class="filter-search">
        <form method="GET" action="{{ url()->current() }}">
            <div class="radio-group">
                <h3>Sort By</h3>
                <div class="page-2">
                    <label>
                        <input type="radio" name="sort" value="serial_number" {{ request('sort') == 'serial_number' ? 'checked' : '' }}>
                        <span class="custom-radio"></span> Serial Number
                    </label>
                    {{-- <label>
                        <input type="radio" name="sort" value="shop_name" {{ request('sort') == 'shop_name' ? 'checked' : '' }}>
                        <span class="custom-radio"></span> Shop Name
                    </label> --}}
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
                    
                     {{--  <label>
                        <input type="radio" name="sort" value="sold_date" {{ request('sort') == 'sold_date' ? 'checked' : '' }}>
                        <span class="custom-radio"></span> Sold Date
                    </label>
                    <label>
                        <input type="radio" name="sort" value="a-z" {{ request('sort') == 'a-z' ? 'checked' : '' }}>
                        <span class="custom-radio"></span> A-Z
                    </label>
                    <label>
                        <input type="radio" name="sort" value="z-a" {{ request('sort') == 'z-a' ? 'checked' : '' }}>
                        <span class="custom-radio"></span> Z-A
                    </label> --}}
                    {{-- <label>
                        <input type="radio" name="sort" value="new" {{ request('sort') == 'new' ? 'checked' : '' }}>
                        <span class="custom-radio"></span> New
                    </label>
                    <label>
                        <input type="radio" name="sort" value="old" {{ request('sort') == 'old' ? 'checked' : '' }}>
                        <span class="custom-radio"></span> Old
                    </label> --}}
                </div>
                <div class="horizontal-line"></div>
                <h3>Category</h3>
                <label>
                    <input type="checkbox" name="category[]" value="*" 
                        {{ in_array('*', request('category', [])) ? 'checked' : '' }}>
                    <span class="custom-checkbox-purity"></span> *
                </label>
                <label>
                    <input type="checkbox" name="category[]" value="**" 
                        {{ in_array('**', request('category', [])) ? 'checked' : '' }}>
                    <span class="custom-checkbox-purity"></span> **
                </label>
                <label>
                    <input type="checkbox" name="category[]" value="***" 
                        {{ in_array('***', request('category', [])) ? 'checked' : '' }}>
                    <span class="custom-checkbox-purity"></span> ***
                </label>
                <div class="horizontal-line"></div>
                <h3>Metal Purity</h3>
            <label>
                <input type="checkbox" name="metal_purity[]" value="18K" 
                    {{ in_array('18K', request('metal_purity', [])) ? 'checked' : '' }}>
                <span class="custom-checkbox-purity"></span> 18K
            </label>
            <label>
                <input type="checkbox" name="metal_purity[]" value="21K"
                    {{ in_array('21K', request('metal_purity', [])) ? 'checked' : '' }}>
                <span class="custom-checkbox-purity"></span> 21K
            </label>
            <label>
                <input type="checkbox" name="metal_purity[]" value="24K"
                    {{ in_array('24K', request('metal_purity', [])) ? 'checked' : '' }}>
                <span class="custom-checkbox-purity"></span> 24K
            </label>
            
            <div class="horizontal-line"></div>
            <h3>Kind</h3>
            <label>
                <input type="checkbox" name="kind[]" value="Ring"
                    {{ in_array('Ring', request('kind', [])) ? 'checked' : '' }}>
                <span class="custom-checkbox-kind"></span> Ring
            </label>
            <label>
                <input type="checkbox" name="kind[]" value="Necklace"
                    {{ in_array('Necklace', request('kind', [])) ? 'checked' : '' }}>
                <span class="custom-checkbox-kind"></span> Necklace
            </label>
            <label>
                <input type="checkbox" name="kind[]" value="Anklet"
                    {{ in_array('Anklet', request('kind', [])) ? 'checked' : '' }}>
                <span class="custom-checkbox-kind"></span> Anklet
            </label>
            <label>
                <input type="checkbox" name="kind[]" value="Cufflink"
                    {{ in_array('Cufflink', request('kind', [])) ? 'checked' : '' }}>
                <span class="custom-checkbox-kind"></span> Cufflink
            </label>
            <label>
                <input type="checkbox" name="kind[]" value="Earring"
                    {{ in_array('Earring', request('kind', [])) ? 'checked' : '' }}>
                <span class="custom-checkbox-kind"></span> Earring
            </label>
            <label>
                <input type="checkbox" name="kind[]" value="Medal"
                    {{ in_array('Medal', request('kind', [])) ? 'checked' : '' }}>
                <span class="custom-checkbox-kind"></span> Medal
            </label>
            <label>
                <input type="checkbox" name="kind[]" value="Bracelet"
                    {{ in_array('Bracelet', request('kind', [])) ? 'checked' : '' }}>
                <span class="custom-checkbox-kind"></span> Bracelet
            </label>
            <label>
                <input type="checkbox" name="kind[]" value="Brooch"
                    {{ in_array('Brooch', request('kind', [])) ? 'checked' : '' }}>
                <span class="custom-checkbox-kind"></span> Brooch
            </label>
        </div>
        <div class="horizontal-line"></div>
        <h3>Shop Name</h3>
        <label>
            <input type="checkbox" name="shop_name[]" value="Arkan" 
                {{ in_array('Arkan', request('shop_name', [])) ? 'checked' : '' }}>
            <span class="custom-checkbox-shop"></span> Arkan
        </label>
        <label>
            <input type="checkbox" name="shop_name[]" value="District 5" 
                {{ in_array('District 5', request('shop_name', [])) ? 'checked' : '' }}>
            <span class="custom-checkbox-shop"></span> District 5
        </label>
        <label>
            <input type="checkbox" name="shop_name[]" value="U Venues" 
                {{ in_array('U Venues', request('shop_name', [])) ? 'checked' : '' }}>
            <span class="custom-checkbox-shop"></span> U Venues
        </label>
        <label>
            <input type="checkbox" name="shop_name[]" value="Mall of Egypt" 
                {{ in_array('Mall of Egypt', request('shop_name', [])) ? 'checked' : '' }}>
            <span class="custom-checkbox-shop"></span> Mall of Egypt
        </label>
        <label>
            <input type="checkbox" name="shop_name[]" value="Zamalek" 
                {{ in_array('Zamalek', request('shop_name', [])) ? 'checked' : '' }}>
            <span class="custom-checkbox-shop"></span> Zamalek
        </label>
        <label>
            <input type="checkbox" name="shop_name[]" value="Nasr City" 
                {{ in_array('Nasr City', request('shop_name', [])) ? 'checked' : '' }}>
            <span class="custom-checkbox-shop"></span> Nasr City
        </label>
        <label>
            <input type="checkbox" name="shop_name[]" value="Mall of Arabia" 
                {{ in_array('Mall of Arabia', request('shop_name', [])) ? 'checked' : '' }}>
            <span class="custom-checkbox-shop"></span> Mall of Arabia
        </label>
        <label>
            <input type="checkbox" name="shop_name[]" value="Mohandessin Office" 
                {{ in_array('Mohandessin Office', request('shop_name', [])) ? 'checked' : '' }}>
            <span class="custom-checkbox-shop"></span> Mohandessin Office
        </label>
        <label>
            <input type="checkbox" name="shop_name[]" value="Mohandessin Shop" 
                {{ in_array('Mohandessin Shop', request('shop_name', [])) ? 'checked' : '' }}>
            <span class="custom-checkbox-shop"></span> Mohandessin Shop
        </label>
        <label>
            <input type="checkbox" name="shop_name[]" value="El Guezira Shop" 
                {{ in_array('El Guezira Shop', request('shop_name', [])) ? 'checked' : '' }}>
            <span class="custom-checkbox-shop"></span> El Guezira Shop
        </label>
        
            <div class="button-container">
                <button type="submit" class="reset-button">Set</button>
                <a href="{{ url()->current() }}" class="reset-button">Reset</a>

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