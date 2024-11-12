<nav class="second-section">

    <div class="second-section-item search-container">
        <form action="{{ url()->current() }}" method="GET">
            <input type="text" name="search" class="search-input" placeholder="Model Name" value="{{ request('search') }}">
            <button type="submit">Search</button>
        </form>
    </div>
   
  
</nav>
