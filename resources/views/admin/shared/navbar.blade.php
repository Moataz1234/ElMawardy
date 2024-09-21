
<link href="{{ asset('css/style.css') }}" rel="stylesheet">
<div class="topnav">
 {{-- <a class="active" href="#home">Home</a> --}}
 
 <a href="{{ route('gold_catalog.3') }}" class="btn btn-primary">3*3 View</a>  
 <a href="{{ route('gold_catalog.4') }}" class="btn btn-primary">4*4 View</a>
  {{-- Add Button --}}
  <button id="addImageButton" class="addBtn">Add Image</button>
  <input type="file" id="imageInput" style="display: none;" accept="image/*">
    {{-- Searching --}}
    <form class="search" Type="get" action="{{ url('/gold/search') }}" >
      <input class="searchText" type="text" name="query" placeholder="Search ModelName">
      <button >Search</button>
  </form>
  </div>
  <script>
    document.getElementById('addImageButton').addEventListener('click', function() {
        window.location.href = "{{ route('gold_addImage') }}";
          
    });
</script>