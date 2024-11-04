<nav class="second-section">
    <div class="profile-card relative">
        <!-- Hidden Checkbox Toggle -->
        <input type="checkbox" id="dropdown-toggle" class="hidden" />
    
        <!-- Label for Checkbox (User's Name) -->
        <label for="dropdown-toggle" class="profile-info cursor-pointer font-medium text-gray-700 hover:text-gray-900">
            <h4 class="name">{{ Auth::user()->name }}</h4>
        </label>
    
        <!-- Dropdown Menu (Logout option) -->
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
    </div>
    </div><div class="second-section-item search-container">
        <form action="{{ url()->current() }}" method="GET">
            <input type="text" name="search" class="search-input" placeholder="Model Name" value="{{ request('search') }}">
            <button type="submit">Search</button>
        </form>
    </div>
    
  {{-- <div class="controls">
    <div id="grid-3x3" class="grid-icon">
        <i class="fas fa-th-large"></i>
    </div>
    <div id="grid-4x4" class="grid-icon">
        <i class="fas fa-th"></i>
    </div>
</div> --}}

   
  
</nav>
