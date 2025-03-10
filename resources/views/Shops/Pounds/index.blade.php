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
                <a href="{{ route('gold-pounds.export') }}" class="btn btn-outline-primary">
                    <i class="fas fa-file-export me-1"></i> Export
                </a>
                <button class="btn btn-outline-success" onclick="window.location.reload()">
                    <i class="fas fa-sync-alt me-1"></i> Refresh
                </button>
            </div>
        </div>

        <!-- Update the search input -->
        {{-- <div class="input-group mb-3" style="width: 300px;">
            <input type="text" class="form-control" id="searchInput" placeholder="Search inventory...">
            <button class="btn btn-outline-secondary" type="button" id="searchButton">
                <i class="fas fa-search"></i>
            </button>
        </div> --}}

        <!-- Add this right after your table -->
        <div id="searchError" class="alert alert-danger mt-3" style="display: none;"></div>

        <!-- Inventory Card -->
        <div class="card">
            <div class="card-header py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-box-open me-2"></i>Current Inventory
                    </h5>
                </div>
            </div>
            
            <div class="card-body">
                <form id="sellForm" action="{{ route('gold-pounds.create-sale-request') }}" method="POST">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="5%">Select</th>
                                    <th>Serial Number</th>
                                    <th>Related Item Serial</th>
                                    <th>Type</th>
                                    <th>Weight (g)</th>
                                    <th>Linked Item</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($shopPounds as $pound)
                                    <tr>
                                        <td>
                                            @if ($pound->status === 'pending_sale' || $pound->status === 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @else
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input pound-checkbox"
                                                        name="selected_pounds[]"
                                                        value="{{ $pound->serial_number }}"
                                                        data-serial="{{ $pound->serial_number }}">
                                                </div>
                                            @endif
                                        </td>
                                        <td><span class="fw-medium">{{ $pound->serial_number }}</span></td>
                                        <td>{{ $pound->related_item_serial ?? 'N/A' }}</td>
                                        <td>{{ $pound->goldPound ? ucfirst(str_replace('_', ' ', $pound->goldPound->kind)) : 'N/A' }}</td>
                                        <td>{{ $pound->goldPound ? $pound->goldPound->weight : 'N/A' }}</td>
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
                    
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div>
                            <span class="text-muted">Selected items: </span>
                            <span id="selectedCount" class="badge bg-primary">0</span>
                        </div>
                        <button type="button" id="sellSelectedBtn" class="btn btn-primary" disabled>
                            <i class="fas fa-shopping-cart me-1"></i> Sell Selected Pounds
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.pound-checkbox');
            const sellButton = document.getElementById('sellSelectedBtn');
            const selectedCount = document.getElementById('selectedCount');

            function updateSellButton() {
                const checkedBoxes = document.querySelectorAll('.pound-checkbox:checked');
                sellButton.disabled = checkedBoxes.length === 0;
                selectedCount.textContent = checkedBoxes.length;
            }

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateSellButton();
                    // Debug: Log when checkbox changes
                    console.log('Checkbox changed:', this.dataset.serial, 'Checked:', this.checked);
                });
            });

            sellButton.addEventListener('click', function() {
                const selectedPounds = Array.from(document.querySelectorAll('.pound-checkbox:checked'))
                    .map(cb => cb.dataset.serial);

                // Debug: Log selected pounds before redirect
                console.log('Selected pounds before redirect:', selectedPounds);

                if (selectedPounds.length > 0) {
                    const params = new URLSearchParams();
                    selectedPounds.forEach(serialNumber => {
                        params.append('selected_pounds[]', serialNumber);
                    });

                    const url = '{{ route('gold-pounds.sell-form') }}?' + params.toString();
                    // Debug: Log the final URL
                    console.log('Redirect URL:', url);

                    window.location.href = url;
                }
            });

            // Search functionality
            const searchInput = document.getElementById('searchInput');
            const searchButton = document.getElementById('searchButton');
            const tableBody = document.querySelector('tbody');
            const searchError = document.getElementById('searchError');

            function performSearch() {
                const searchTerm = searchInput.value;
                searchError.style.display = 'none';
                
                // Show loading state
                tableBody.innerHTML = '<tr><td colspan="7" class="text-center">Loading...</td></tr>';
                
                fetch(`{{ route('gold-pounds.search') }}?search=${encodeURIComponent(searchTerm)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        tableBody.innerHTML = data.html;
                        initializeCheckboxes();
                        
                        // If no results found
                        if (data.count === 0) {
                            tableBody.innerHTML = '<tr><td colspan="7" class="text-center">No results found</td></tr>';
                        }
                    } else {
                        throw new Error(data.message || 'An error occurred while searching');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    searchError.textContent = error.message || 'An error occurred while performing the search';
                    searchError.style.display = 'block';
                    tableBody.innerHTML = ''; // Clear loading state
                });
            }

            // Search on button click
            searchButton.addEventListener('click', performSearch);

            // Search on enter key
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    performSearch();
                }
            });

            // Initialize checkbox listeners
            function initializeCheckboxes() {
                const checkboxes = document.querySelectorAll('.pound-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', updateSellButton);
                });
            }

            // Initial checkbox setup
            initializeCheckboxes();

            const shopFilter = document.getElementById('shopFilter');
            const kindFilter = document.getElementById('kindFilter');
            const applyFiltersBtn = document.getElementById('applyFilters');

            applyFiltersBtn.addEventListener('click', function() {
                const shop = shopFilter.value;
                const kind = kindFilter.value;
                
                // Build the URL with filter parameters
                let url = new URL(window.location.href);
                url.searchParams.set('shop', shop);
                url.searchParams.set('kind', kind);
                
                // Redirect to the filtered URL
                window.location.href = url.toString();
            });
        });
    </script>
</body>

</html>
