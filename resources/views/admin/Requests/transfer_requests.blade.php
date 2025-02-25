<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Requests Management</title>
    @include('components.navbar')
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f5f7fb;
        }
        
        .status-badge {
            padding: 8px 12px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 500;
            letter-spacing: 0.3px;
            text-transform: capitalize;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .status-pending {
            background-color: #fff8ec;
            color: #f59e0b;
            border: 1px solid #fcd34d;
        }
        
        .status-accepted {
            background-color: #ecfdf5;
            color: #059669;
            border: 1px solid #6ee7b7;
        }
        
        /* .status-rejected {
            background-color: #fef2f2;
            color: #dc2626;
            border: 1px solid #fca5a5;
        } */

        .status-completed {
            background-color: #e0f2f1;
            color: #00796b;
            border: 1px solid #80cbc4;
        }

        .timeline-info {
            font-size: 0.875rem;
            color: #6b7280;
        }

        .card {
            border: none;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
            border-radius: 0.75rem;
        }

        .filter-section {
            background-color: #ffffff;
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background-color: #f8fafc;
            color: #1f2937;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            padding: 1rem;
            border-bottom: 2px solid #e5e7eb;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background-color: #f8fafc;
        }

        .btn-filter {
            padding: 0.5rem 1rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .btn-filter:hover {
            transform: translateY(-1px);
        }

        .form-label {
            font-weight: 500;
            color: #4b5563;
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border-radius: 0.5rem;
            border: 1px solid #e5e7eb;
            padding: 0.625rem 1rem;
            font-size: 0.875rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
        }

        .select2-container .select2-selection--single {
            height: 42px;
            border-radius: 0.5rem;
            border: 1px solid #e5e7eb;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 42px;
            padding-left: 1rem;
            color: #1f2937;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
            right: 8px;
        }

        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            padding: 1.25rem 1.5rem;
        }

        .card-header h4 {
            color: #1f2937;
            font-weight: 600;
            margin: 0;
        }

        .btn-export {
            background-color: #059669;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .btn-export:hover {
            background-color: #047857;
            transform: translateY(-1px);
        }

        .timeline-info i {
            width: 16px;
            color: #6b7280;
            margin-right: 0.5rem;
        }

        .item-details {
            padding: 0.25rem 0;
        }

        .item-details .serial {
            color: #1f2937;
            font-weight: 600;
            font-size: 0.925rem;
        }

        .item-details .model, .item-details .weight {
            color: #6b7280;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .shop-name {
            font-weight: 500;
            color: #1f2937;
            font-size: 0.925rem;
        }

        /* Loading spinner for AJAX requests */
        .loading {
            position: relative;
            opacity: 0.6;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 2rem;
            height: 2rem;
            margin: -1rem 0 0 -1rem;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-12">
                <!-- Filters Section -->
                <div class="card mb-4">
                    <div class="card-body filter-section">
                        <form id="filterForm" class="row g-3">
                            <!-- Date Filter -->
                            <div class="col-md-3">
                                <label class="form-label">Date</label>
                                <input type="date" class="form-control" id="date" name="date">
                            </div>

                            <!-- Status Filter -->
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select class="form-select" id="statusFilter" name="status">
                                    {{-- <option value="">All Statuses</option> --}}
                                    <option value="pending">Pending</option>
                                    {{-- <option value="accepted">Accepted</option>
                                    <option value="rejected">Rejected</option> --}}
                                    <option value="completed">Completed</option>
                                </select>
                            </div>

                            <!-- From Shop Filter -->
                            <div class="col-md-2">
                                <label class="form-label">From Shop</label>
                                <select class="form-select select2" id="fromShopFilter" name="from_shop">
                                    <option value="">All Shops</option>
                                    @foreach($shops as $shop)
                                        <option value="{{ $shop }}">{{ $shop }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- To Shop Filter -->
                            <div class="col-md-2">
                                <label class="form-label">To Shop</label>
                                <select class="form-select select2" id="toShopFilter" name="to_shop">
                                    <option value="">All Shops</option>
                                    @foreach($shops as $shop)
                                        <option value="{{ $shop }}">{{ $shop }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Search Input -->
                            <div class="col-md-3">
                                <label class="form-label">Search Items</label>
                                <input type="text" class="form-control" id="searchInput" name="search" 
                                       placeholder="Search serial, model...">
                            </div>

                            <!-- Filter Buttons -->
                            <div class="col-md-12 text-end mt-4">
                                <button type="reset" class="btn btn-light btn-filter me-2">
                                    <i class="bi bi-x-circle me-1"></i> Clear
                                </button>
                                <button type="submit" class="btn btn-primary btn-filter">
                                    <i class="bi bi-filter me-1"></i> Apply Filters
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Main Content Card -->
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4>Transfer Requests Management</h4>
                            </div>
                            {{-- <div class="col text-end">
                                <button class="btn btn-export" id="exportExcel">
                                    <i class="bi bi-file-excel me-1"></i> Export to Excel
                                </button>
                            </div> --}}
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" id="tableContainer">
                            @include('admin.Requests.transfer_requests_table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });

            // Handle form submission
            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                const tableContainer = $('#tableContainer');
                tableContainer.addClass('loading');
                
                filterResults();
            });

            // Handle form reset
            $('#filterForm').on('reset', function() {
                setTimeout(function() {
                    $('.select2').val('').trigger('change');
                    filterResults();
                });
            });

            // Export to Excel functionality
            $('#exportExcel').click(function() {
                const btn = $(this);
                const originalText = btn.html();
                btn.html('<i class="bi bi-hourglass-split me-1"></i> Exporting...').prop('disabled', true);

                const formData = new FormData($('#filterForm')[0]);
                formData.append('export', true);

                $.ajax({
                    url: window.location.href,
                    type: 'GET',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.file) {
                            window.location.href = '/storage/' + response.file;
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Export error:', error);
                        alert('Error exporting data: ' + error);
                    },
                    complete: function() {
                        btn.html(originalText).prop('disabled', false);
                    }
                });
            });

            function filterResults() {
                const tableContainer = $('#tableContainer');
                
                $.ajax({
                    url: window.location.href,
                    type: 'GET',
                    data: $('#filterForm').serialize(),
                    success: function(response) {
                        if (response.html) {
                            tableContainer.html(response.html);
                        } else {
                            console.error('Invalid response format:', response);
                            alert('Invalid response from server');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Filter error:', error);
                        alert('Error filtering data: ' + error);
                    },
                    complete: function() {
                        tableContainer.removeClass('loading');
                    }
                });
            }
        });
    </script>
</body>
</html>
