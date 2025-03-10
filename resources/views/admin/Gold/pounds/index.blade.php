{{-- resources/views/pounds/index.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <title>Shop Pounds Management</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @include('components.navbar')
    <style>
        .table th { 
            background-color: #f8f9fa;
            font-weight: 600;
        }
        .table td {
            vertical-align: middle;
        }
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0,0,0,.125);
        }
        .badge {
            padding: 0.5em 0.75em;
        }

        /* Reset and override all pagination styles */
        .custom-pagination-container nav,
        .custom-pagination-container .pagination,
        .custom-pagination-container .page-item,
        .custom-pagination-container .page-link {
            all: unset !important;
        }

        .custom-pagination-container {
            width: 100% !important;
            display: flex !important;
            justify-content: center !important;
            margin: 20px 0 !important;
        }

        .custom-pagination-container nav {
            display: block !important;
            width: auto !important;
        }

        .custom-pagination-container .pagination {
            display: flex !important;
            gap: 5px !important;
            background: white !important;
            padding: 10px !important;
            border-radius: 8px !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
        }

        .custom-pagination-container .page-item {
            display: block !important;
        }

        .custom-pagination-container .page-link {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-width: 40px !important;
            height: 40px !important;
            padding: 0 15px !important;
            border-radius: 6px !important;
            background-color: white !important;
            border: 1px solid #e2e8f0 !important;
            color: #4a5568 !important;
            font-weight: 500 !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
        }

        .custom-pagination-container .page-link:hover {
            background-color: #f7fafc !important;
            border-color: #3182ce !important;
            color: #3182ce !important;
        }

        .custom-pagination-container .page-item.active .page-link {
            background-color: #3182ce !important;
            border-color: #3182ce !important;
            color: white !important;
        }

        .custom-pagination-container .page-item.disabled .page-link {
            background-color: #f7fafc !important;
            border-color: #e2e8f0 !important;
            color: #a0aec0 !important;
            cursor: not-allowed !important;
        }

        /* Navigation arrows */
        .custom-pagination-container .page-item:first-child .page-link,
        .custom-pagination-container .page-item:last-child .page-link {
            font-size: 20px !important;
            font-weight: bold !important;
        }

        /* Remove focus outline */
        .custom-pagination-container .page-link:focus {
            outline: none !important;
            box-shadow: none !important;
        }

        /* Responsive adjustments */
        @media (max-width: 640px) {
            .custom-pagination-container .page-link {
                min-width: 35px !important;
                height: 35px !important;
                padding: 0 10px !important;
                font-size: 14px !important;
            }
        }
    </style>
</head>

<body class="bg-light">
    <div class="container-fluid py-4 px-4">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="text-primary mb-0">
                    <i class="fas fa-coins me-2"></i>Shop Pounds Management
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item active">Pounds Management</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <a href="#" id="exportButton" class="btn btn-outline-primary">
                    <i class="fas fa-file-export me-1"></i> Export
                </a>
                <button class="btn btn-outline-success" onclick="window.location.reload()">
                    <i class="fas fa-sync-alt me-1"></i> Refresh
                </button>
            </div>
        </div>

        <!-- Inventory Card -->
        <div class="card">
            <div class="card-header py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-box-open me-2"></i>Current Inventory
                    </h5>
                    <!-- Add filters -->
                    <div class="d-flex gap-3">
                        <select class="form-select" id="shopFilter" style="width: auto;">
                            <option value="">All Shops</option>
                            @foreach($shops as $shop)
                                <option value="{{ $shop->name }}">{{ $shop->name }}</option>
                            @endforeach
                        </select>
                        <select class="form-select" id="kindFilter" style="width: auto;">
                            <option value="">All Types</option>
                            @foreach($poundTypes as $type)
                                <option value="{{ $type->kind }}">{{ ucfirst(str_replace('_', ' ', $type->kind)) }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-primary" id="applyFilters">
                            <i class="fas fa-filter me-1"></i> Apply Filters
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <form id="sellForm" action="{{ route('gold-pounds.create-sale-request') }}" method="POST">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Serial Number</th>
                                    <th>Related Item Serial</th>
                                    <th>Type</th>
                                    <th>Weight (g)</th>
                                    <th>Shop Name</th>
                                    <th>Linked Item</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($shopPounds as $pound)
                                    <tr>
                                        <td><span class="fw-medium">{{ $pound->serial_number }}</span></td>
                                        <td>{{ $pound->related_item_serial ?? 'N/A' }}</td>
                                        <td>{{ $pound->goldPound ? ucfirst(str_replace('_', ' ', $pound->goldPound->kind)) : 'N/A' }}</td>
                                        <td>{{ $pound->goldPound ? $pound->goldPound->weight : 'N/A' }}</td>
                                        <td>{{ $pound->shop_name }}</td>
                                        <td>
                                            @if($pound->goldItem)
                                                <span class="badge bg-success">Yes</span>
                                            @else
                                                <span class="badge bg-secondary">No</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($pound->status === 'pending_sale' || $pound->status === 'pending')
                                                <span class="badge bg-warning">Pending Sale</span>
                                            @else
                                                <span class="badge bg-success">Available</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>

        <!-- Manual Pagination Section -->
        <div class="custom-pagination-container">
            <nav>
                <ul class="pagination">
                    {{-- Previous Page Link --}}
                    <li class="page-item {{ ($shopPounds->currentPage() == 1) ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $shopPounds->url($shopPounds->currentPage() - 1) }}" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>

                    {{-- First Page --}}
                    @if($shopPounds->currentPage() > 3)
                        <li class="page-item">
                            <a class="page-link" href="{{ $shopPounds->url(1) }}">1</a>
                        </li>
                        @if($shopPounds->currentPage() > 4)
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        @endif
                    @endif

                    {{-- Pagination Elements --}}
                    @for($i = max(1, $shopPounds->currentPage() - 2); $i <= min($shopPounds->lastPage(), $shopPounds->currentPage() + 2); $i++)
                        <li class="page-item {{ ($shopPounds->currentPage() == $i) ? 'active' : '' }}">
                            <a class="page-link" href="{{ $shopPounds->url($i) }}">{{ $i }}</a>
                        </li>
                    @endfor

                    {{-- Last Page --}}
                    @if($shopPounds->currentPage() < $shopPounds->lastPage() - 2)
                        @if($shopPounds->currentPage() < $shopPounds->lastPage() - 3)
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        @endif
                        <li class="page-item">
                            <a class="page-link" href="{{ $shopPounds->url($shopPounds->lastPage()) }}">{{ $shopPounds->lastPage() }}</a>
                        </li>
                    @endif

                    {{-- Next Page Link --}}
                    <li class="page-item {{ ($shopPounds->currentPage() == $shopPounds->lastPage()) ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $shopPounds->url($shopPounds->currentPage() + 1) }}" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const shopFilter = document.getElementById('shopFilter');
        const kindFilter = document.getElementById('kindFilter');
        const applyFiltersBtn = document.getElementById('applyFilters');
        const exportButton = document.getElementById('exportButton');

        // Function to handle pagination and filters
        function handlePagination(page) {
            const shop = shopFilter.value;
            const kind = kindFilter.value;
            
            const url = new URL(window.location.href);
            
            // Set the page parameter
            url.searchParams.set('page', page);
            
            // Set or remove filter parameters
            if (shop) url.searchParams.set('shop', shop);
            else url.searchParams.delete('shop');
            
            if (kind) url.searchParams.set('kind', kind);
            else url.searchParams.delete('kind');
            
            // Navigate to the new URL
            window.location.href = url.toString();
        }

        // Initialize pagination click handlers
        function initializePagination() {
            document.querySelectorAll('.pagination a').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const page = this.href.split('page=')[1] || 1;
                    handlePagination(page);
                });
            });
        }

        // Apply filters function
        function applyFilters() {
            handlePagination(1); // Reset to page 1 when applying filters
        }

        // Update export URL function
        function updateExportUrl() {
            const shop = shopFilter.value;
            const kind = kindFilter.value;
            const baseUrl = '{{ route('gold-pounds.export') }}';
            const params = new URLSearchParams();
            
            if (shop) params.append('shop', shop);
            if (kind) params.append('kind', kind);
            
            exportButton.href = `${baseUrl}?${params.toString()}`;
        }

        // Set initial filter values from URL
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('shop')) {
            shopFilter.value = urlParams.get('shop');
        }
        if (urlParams.has('kind')) {
            kindFilter.value = urlParams.get('kind');
        }

        // Event listeners
        if (applyFiltersBtn) {
            applyFiltersBtn.addEventListener('click', applyFilters);
        }

        shopFilter.addEventListener('change', updateExportUrl);
        kindFilter.addEventListener('change', updateExportUrl);

        // Initialize
        updateExportUrl();
        initializePagination();
    });
    </script>
</body>

</html>