<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Requests</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link href="{{ url('css/addRequests.css') }}" rel="stylesheet">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
        .editable-cell {
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .editable-cell:hover {
            background-color: #f0f0f0;
        }
        .modal-lg {
            max-width: 90%;
        }
        .table-responsive {
            max-height: 600px;
            overflow-y: auto;
        }
    </style>
</head>

<body>
    <!-- Include Navbar -->
    @include('components.navbar')

    <div class="container-fluid mt-5">
        <h1 class="text-center mb-4">Add Requests</h1>

        <!-- Success and Error Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {!! session('success') !!}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <!-- Filters Container -->
        <div class="filters-container mt-4">
            <form action="{{ route('admin.add.requests') }}" method="GET" class="row align-items-end g-3">
                <div class="col-md-2">
                    <label for="status" class="form-label">Request Status</label>
                    <select name="status" id="status" class="form-control custom-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="shop_name" class="form-label">Shop Name</label>
                    <select name="shop_name" id="shop_name" class="form-control custom-select">
                        <option value="">All Shops</option>
                        @foreach ($shops as $shop)
                            <option value="{{ $shop }}" {{ request('shop_name') == $shop ? 'selected' : '' }}>
                                {{ $shop }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" class="form-control" id="date" name="date" value="{{ request('date', date('Y-m-d')) }}">
                </div>

                <div class="col-md-6 text-right">
                    <button type="submit" class="btn btn-primary mr-2">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <button type="reset" class="btn btn-secondary mr-2">
                        <i class="fas fa-redo"></i> Reset
                    </button>
                    <a href="{{ route('admin.add.requests.export') }}?{{ http_build_query(request()->all()) }}" 
                       class="btn btn-success mr-2">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                    <a href="{{ route('admin.add.requests.print') }}?{{ http_build_query(request()->all()) }}" 
                       class="btn btn-info" target="_blank">
                        <i class="fas fa-print"></i> Print
                    </a>
                </div>
            </form>
        </div>

        <!-- Totals Row -->
        <div class="row mt-3">
            <div class="col-md-6">
                <div class="badge badge-primary p-2">
                    Total Items: {{ count($requests) }}
                </div>
            </div>
            <div class="col-md-6 text-right">
                <div class="badge badge-info p-2">
                    Total Weight: {{ number_format($requests->sum('weight'), 2) }}
                </div>
            </div>
        </div>

        <!-- Requests Table -->
        <div class="table-responsive mt-4">
            <table class="table table-hover table-striped border">
                <thead class="thead-dark">
                    <tr>
                        <th>Serial Number</th>
                        <th>Model</th>
                        <th>Shop Name</th>
                        <th>Type</th>
                        <th>Weight</th>
                        <th>Stars</th>
                        <th>Gold Color</th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'date', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" 
                               class="text-white text-decoration-none">
                                Date
                                @if(request('sort') == 'date')
                                    <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fas fa-sort"></i>
                                @endif
                            </a>
                        </th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($requests as $request)
                        <tr data-id="{{ $request->id }}">
                            <td class="editable-cell" data-field="serial_number">{{ $request->serial_number }}</td>
                            <td class="editable-cell" data-field="model">{{ $request->model }}</td>
                            <td class="editable-cell" data-field="shop_name">{{ $request->shop_name }}</td>
                            <td class="editable-cell" data-field="kind">{{ $request->kind }}</td>
                            <td class="editable-cell" data-field="weight">{{ $request->weight }}</td>
                            <td>{{ $request->stars }}</td>
                            <td class="editable-cell" data-field="gold_color">{{ $request->gold_color }}</td>
                            <td class="editable-cell" data-field="rest_since">{{ $request->rest_since }}</td>
                            <td class="editable-cell" data-field="status">{{ $request->status }}</td>
                            <td>
                                <button class="btn btn-sm btn-primary edit-request" 
                                        data-id="{{ $request->id }}"
                                        data-toggle="modal" 
                                        data-target="#editRequestModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">No requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Edit Request Modal -->
    <div class="modal fade" id="editRequestModal" tabindex="-1" role="dialog" aria-labelledby="editRequestModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editRequestModalLabel">Edit Add Request</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editRequestForm">
                        @csrf
                        <input type="hidden" id="editRequestId" name="id">
                        
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="editSerialNumber">Serial Number</label>
                                <input type="text" class="form-control" id="editSerialNumber" name="serial_number" readonly>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="editModel">Model</label>
                                <input type="text" class="form-control" id="editModel" name="model">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="editShopName">Shop Name</label>
                                <select class="form-control" id="editShopName" name="shop_name">
                                    @foreach ($shops as $shop)
                                        <option value="{{ $shop }}">{{ $shop }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="editKind">Type</label>
                                <input type="text" class="form-control" id="editKind" name="kind">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="editWeight">Weight</label>
                                <input type="number" step="0.01" class="form-control" id="editWeight" name="weight">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="editGoldColor">Gold Color</label>
                                <input type="text" class="form-control" id="editGoldColor" name="gold_color">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="editRestSince">Date</label>
                                <input type="date" class="form-control" id="editRestSince" name="rest_since">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="editStatus">Status</label>
                                <select class="form-control" id="editStatus" name="status">
                                    <option value="pending">Pending</option>
                                    <option value="accepted">Accepted</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveEditRequest">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Edit Request Script -->
    <script>
        $(document).ready(function() {
            // Setup CSRF token for AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Open Edit Modal
            $('.edit-request').on('click', function() {
                const requestId = $(this).data('id');
                const row = $(this).closest('tr');

                // Populate modal with current row data
                $('#editRequestId').val(requestId);
                $('#editSerialNumber').val(row.find('td[data-field="serial_number"]').text());
                $('#editModel').val(row.find('td[data-field="model"]').text());
                $('#editShopName').val(row.find('td[data-field="shop_name"]').text());
                $('#editKind').val(row.find('td[data-field="kind"]').text());
                $('#editWeight').val(row.find('td[data-field="weight"]').text());
                $('#editGoldColor').val(row.find('td[data-field="gold_color"]').text());
                $('#editRestSince').val(row.find('td[data-field="rest_since"]').text());
                $('#editStatus').val(row.find('td[data-field="status"]').text());
            });

            // Save Edit Request
            $('#saveEditRequest').on('click', function() {
                const requestId = $('#editRequestId').val();
                const formData = $('#editRequestForm').serialize();

                $.ajax({
                    url: `/admin/add-requests/${requestId}/update`,
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            // Update the table row with new data
                            const row = $(`tr[data-id="${requestId}"]`);
                            row.find('td[data-field="serial_number"]').text(response.data.serial_number);
                            row.find('td[data-field="model"]').text(response.data.model);
                            row.find('td[data-field="shop_name"]').text(response.data.shop_name);
                            row.find('td[data-field="kind"]').text(response.data.kind);
                            row.find('td[data-field="weight"]').text(response.data.weight);
                            row.find('td[data-field="gold_color"]').text(response.data.gold_color);
                            row.find('td[data-field="rest_since"]').text(response.data.rest_since);
                            row.find('td[data-field="status"]').text(response.data.status);

                            // Close the modal
                            $('#editRequestModal').modal('hide');

                            // Show success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Request updated successfully!'
                            });
                        }
                    },
                    error: function(xhr) {
                        // Handle errors
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON.message || 'Failed to update request'
                        });
                    }
                });
            });

            // Optional: Add hover effect to editable cells
            $('.editable-cell').hover(
                function() { $(this).css('background-color', '#f0f0f0'); },
                function() { $(this).css('background-color', ''); }
            );
        });
    </script>
</body>
</html>