<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طلبات الجنيهات</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    @include('components.navbar')
    <style>
        .card {
            border-radius: 15px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            direction: rtl;
        }

        .card-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0098ff 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
        }

        .btn-check-all {
            background-color: #e9ecef;
            border-color: #dee2e6;
        }

        .table th {
            background-color: #f8f9fa;
        }

        .pound-image {
            max-width: 100px;
            max-height: 100px;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .pound-image:hover {
            transform: scale(1.1);
        }

        .modal-image {
            max-width: 100%;
            max-height: 80vh;
        }
    </style>
</head>

<body>

    <body>
        <div class="container py-4">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Pound Requests</h3>
                        <div>
                            <button id="approveSelected" class="btn btn-success" disabled>
                                <i class="fas fa-check me-1"></i> Approve Selected
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" class="form-check-input" id="selectAll"></th>
                                    <th>Serial Number</th>
                                    <th>Pound Kind</th>
                                    <th>Type</th>
                                    <th>Weight</th>
                                    <th>Purity</th>
                                    <th>Quantity</th>
                                    <th>Image</th>
                                    <th>Request Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($requests as $request)
                                    <tr>
                                        <td><input type="checkbox" class="form-check-input request-checkbox"
                                                value="{{ $request->id }}"
                                                {{ $request->status !== 'pending' ? 'disabled' : '' }}></td>
                                        <td>{{ $request->serial_number }}</td>
                                        <td>{{ $request->goldPound->kind }}</td>
                                        <td>{{ $request->type === 'standalone' ? 'Standalone' : 'In Item' }}</td>
                                        <td>
                                            @if (in_array($request->goldPound->kind, ['pound_varient', 'bar_varient']) || $request->custom_weight)
                                                {{ $request->custom_weight ?? $request->weight }}g
                                            @else
                                                {{ $request->weight }}g
                                            @endif
                                        </td>
                                        <td>
                                            @if (in_array($request->goldPound->kind, ['pound_varient', 'bar_varient']) || $request->custom_purity)
                                                {{ $request->custom_purity ?? $request->goldPound->purity }} karat
                                            @else
                                                {{ $request->goldPound->purity }} karat
                                            @endif
                                        </td>
                                        <td>{{ $request->quantity }}</td>
                                        <td>
                                            @if ($request->image_path)
                                                <img src="{{ asset('storage/' . $request->image_path) }}"
                                                    alt="Pound Image" class="pound-image"
                                                    onclick="showImageModal(this.src)">
                                            @else
                                                <span class="text-muted">No Image</span>
                                            @endif
                                        </td>
                                        <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $request->status === 'pending' ? 'warning' : ($request->status === 'approved' ? 'success' : 'danger') }}">
                                                {{ $request->status === 'pending' ? 'Pending' : ($request->status === 'approved' ? 'Approved' : 'Rejected') }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">No requests available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="imageModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Pound Image</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="" alt="Pound Image" class="modal-image">
                    </div>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            $(document).ready(function() {
                const selectAll = $('#selectAll');
                const requestCheckboxes = $('.request-checkbox');
                const approveBtn = $('#approveSelected');

                function updateButtonsState() {
                    const checkedBoxes = $('.request-checkbox:checked').length;
                    approveBtn.prop('disabled', checkedBoxes === 0);
                }

                selectAll.change(function() {
                    requestCheckboxes.not(':disabled').prop('checked', this.checked);
                    updateButtonsState();
                });

                requestCheckboxes.change(function() {
                    updateButtonsState();
                    selectAll.prop('checked', $('.request-checkbox:checked').length === $(
                        '.request-checkbox:not(:disabled)').length);
                });

                approveBtn.click(function() {
                    const selectedRequests = $('.request-checkbox:checked').map(function() {
                        return this.value;
                    }).get();

                    if (selectedRequests.length === 0) return;

                    Swal.fire({
                        title: 'Confirm',
                        text: 'Are you sure you want to approve the selected requests?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#28a745',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Approve',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '/pound-requests/bulk-approve',
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    selected_requests: selectedRequests
                                },
                                success: function(response) {
                                    Swal.fire({
                                        title: 'Success!',
                                        text: response.message,
                                        icon: 'success'
                                    }).then(() => {
                                        location.reload();
                                    });
                                },
                                error: function(xhr) {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: xhr.responseJSON?.message ||
                                            'An error occurred',
                                        icon: 'error'
                                    });
                                }
                            });
                        }
                    });
                });
            });

            function showImageModal(imageSrc) {
                const modal = new bootstrap.Modal(document.getElementById('imageModal'));
                document.querySelector('#imageModal .modal-image').src = imageSrc;
                modal.show();
            }
        </script>
    </body>

</html>
