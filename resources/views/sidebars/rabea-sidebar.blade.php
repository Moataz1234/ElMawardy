{{-- <nav class="second-section">
        <form action="{{ url()->current() }}" method="GET" class="search-form">
               @foreach(request()->except(['search', 'page']) as $key => $value)
                   @if(is_array($value))
                       @foreach($value as $item)
                           <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                       @endforeach
                   @else
                       <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                   @endif
               @endforeach
               <input type="text" name="search" class="search-input" 
                      value="{{ request('search') }}" placeholder="Model Name">
               <button type="submit">Search</button>
           </form>
        </nav> --}}

        <div class="page-2">
    <div class="container">
        <div class="filter-search">
            <form method="GET" action="{{ route('orders.rabea.index') }}">
                <div class="radio-group">
                    <h3>Sort By</h3>
                    <div class="page-2">
                    
                        <label>
                            <input type="radio" name="sort" value="order_number" {{ request('sort') == 'order_number' ? 'checked' : '' }}>
                            <span class="custom-radio"></span> رقم الأوردر
                        </label>
                        <label>
                            <input type="radio" name="sort" value="customer_name" {{ request('sort') == 'customer_name' ? 'checked' : '' }}>
                            <span class="custom-radio"></span> اسم العميل
                        </label>
                        <label>
                            <input type="radio" name="sort" value="seller_name" {{ request('sort') == 'seller_name' ? 'checked' : '' }}>
                            <span class="custom-radio"></span> اسم البائع
                        </label>
                        <label>
                            <input type="radio" name="sort" value="status" {{ request('sort') == 'status' ? 'checked' : '' }}>
                            <span class="custom-radio"></span> الحالة
                        </label>
                    </div>
                </div>
                <div class="horizontal-line"></div>
                <div class="button-container">
                    <button type="submit" class="reset-button">Set</button>
                </div>
            </form>
        </div>
    </div>
</div>