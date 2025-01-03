<link href="{{ asset('css/pagination.css') }}" rel="stylesheet">

    <div class="pagination-wrapper">
        <div class="pagination">
            <div class="nav-group">
                <button class="prev-btn" onclick="goToPage('prev')" id="prevButton">←</button>
                <button class="next-btn" onclick="goToPage('next')" id="nextButton">
                    Next Page →
                </button>
            </div>
            
            <div class="page-counter">
                <span>Page</span>
                <input type="number" class="page-number-input" id="pageInput" min="1">
                <span>of <span id="totalPages">0</span></span>
            </div>
        </div>
    </div>
        
    <script>
        document.addEventListener('DOMContentLoaded', function() {
           // Get current page from URL or default to 1
           const urlParams = new URLSearchParams(window.location.search);
           const currentPage = parseInt(urlParams.get('page')) || 1;
           
           // Get total pages from your Laravel data
           // You can pass this from your controller using a data attribute
           const totalPages = parseInt(document.querySelector('#totalPages').textContent);
           
           // Update input with current page
           const pageInput = document.querySelector('#pageInput');
           pageInput.value = currentPage;
           pageInput.max = totalPages;
           
           // Update total pages display
           document.querySelector('#totalPages').textContent = totalPages;
           
           // Disable prev button if on first page
           document.querySelector('#prevButton').disabled = currentPage <= 1;
           
           // Disable next button if on last page
           document.querySelector('#nextButton').disabled = currentPage >= totalPages;
       });
       
       function goToPage(direction) {
           const currentPage = parseInt(document.querySelector('#pageInput').value);
           const totalPages = parseInt(document.querySelector('#totalPages').textContent);
           let newPage;
       
           if (direction === 'next') {
               newPage = currentPage + 1;
               if (newPage <= totalPages) {
                   window.location.href = `?page=${newPage}`;
               }
           } else if (direction === 'prev') {
               newPage = currentPage - 1;
               if (newPage > 0) {
                   window.location.href = `?page=${newPage}`;
               }
           }
       }
       
       // Handle manual page input
       document.querySelector('#pageInput').addEventListener('change', function(e) {
           const page = parseInt(e.target.value);
           const totalPages = parseInt(document.querySelector('#totalPages').textContent);
           
           if (page > 0 && page <= totalPages) {
               window.location.href = `?page=${page}`;
           } else {
               // Reset to current page if invalid input
               const currentPage = new URLSearchParams(window.location.search).get('page') || 1;
               e.target.value = currentPage;
           }
       });
       document.querySelector('#totalPages').textContent = {{ $goldItems->lastPage() }};
       function goToPage(direction) {
           const currentPage = parseInt(document.querySelector('#pageInput').value);
           const totalPages = parseInt(document.querySelector('#totalPages').textContent);
           const currentUrl = new URL(window.location.href);
           const searchParams = currentUrl.searchParams;
           
           let newPage;
           if (direction === 'next') {
               newPage = currentPage + 1;
           } else {
               newPage = currentPage - 1;
           }
           
           if (newPage > 0 && newPage <= totalPages) {
               searchParams.set('page', newPage);
               window.location.href = currentUrl.toString();
           }
       }
       
       document.querySelector('#pageInput').addEventListener('change', function(e) {
           const page = parseInt(e.target.value);
           const totalPages = parseInt(document.querySelector('#totalPages').textContent);
           const currentUrl = new URL(window.location.href);
           
           if (page > 0 && page <= totalPages) {
               currentUrl.searchParams.set('page', page);
               window.location.href = currentUrl.toString();
           } else {
               e.target.value = currentUrl.searchParams.get('page') || 1;
           }
       });
           </script>
