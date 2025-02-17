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
    <div class="container py-4 ">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">طلبات السبائك</h3>
                    <div>
                        <button id="approveSelected" class="btn btn-success" disabled>
                            <i class="fas fa-check me-1"></i> قبول المحدد
                        </button>
                        {{-- <button id="rejectSelected" class="btn btn-danger" disabled>
                            <i class="fas fa-times me-1"></i> رفض المحدد
                        </button> --}}
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="selectAll">
                                    </div>
                                </th>
                                <th>الرقم التسلسلي</th>
                                <th>نوع السبيكة</th>
                                <th>النوع</th>
                                <th>الوزن</th>
                                <th>العيار</th>
                                <th>الكمية</th>
                                <th>الصورة</th>
                                <th>تاريخ الطلب</th>
                                <th>الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $request)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input request-checkbox" 
                                                value="{{ $request->id }}"
                                                {{ $request->status !== 'pending' ? 'disabled' : '' }}>
                                        </div>
                                    </td>
                                    <td>{{ $request->serial_number }}</td>
                                    <td>{{ $request->goldPound->kind }}</td>
                                    <td>{{ $request->type === 'standalone' ? 'منفرد' : 'في قطعة' }}</td>
                                    <td>
                                        @if(in_array($request->goldPound->kind, ['pound_varient', 'bar_varient']))
                                            {{ $request->custom_weight ?? $request->weight }}g
                                        @else
                                            {{ $request->weight }}g
                                        @endif
                                    </td>
                                    <td>
                                        @if(in_array($request->goldPound->kind, ['pound_varient', 'bar_varient']))
                                            {{ $request->custom_purity ?? $request->goldPound->purity }} قيراط
                                        @else
                                            {{ $request->goldPound->purity }} قيراط
                                        @endif
                                    </td>
                                    <td>{{ $request->quantity }}</td>
                                    <td>
                                        @if($request->image_path)
                                            <img src="{{ asset('storage/' . $request->image_path) }}" 
                                                 alt="صورة السبيكة" 
                                                 class="pound-image"
                                                 onclick="showImageModal(this.src)">
                                        @else
                                            <span class="text-muted">لا توجد صورة</span>
                                        @endif
                                    </td>
                                    <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $request->status === 'pending' ? 'warning' : ($request->status === 'approved' ? 'success' : 'danger') }}">
                                            {{ $request->status === 'pending' ? 'قيد الانتظار' : ($request->status === 'approved' ? 'مقبول' : 'مرفوض') }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">لا توجد طلبات حالياً</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">صورة السبيكة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="" alt="صورة السبيكة" class="modal-image">
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            const selectAll = $('#selectAll');
            const requestCheckboxes = $('.request-checkbox');
            const approveBtn = $('#approveSelected');
            const rejectBtn = $('#rejectSelected');

            function updateButtonsState() {
                const checkedBoxes = $('.request-checkbox:checked').length;
                approveBtn.prop('disabled', checkedBoxes === 0);
                rejectBtn.prop('disabled', checkedBoxes === 0);
            }

            selectAll.change(function() {
                requestCheckboxes.not(':disabled').prop('checked', this.checked);
                updateButtonsState();
            });

            requestCheckboxes.change(function() {
                updateButtonsState();
                selectAll.prop('checked', 
                    $('.request-checkbox:checked').length === $('.request-checkbox:not(:disabled)').length
                );
            });

            function handleBulkAction(action) {
                const selectedRequests = $('.request-checkbox:checked').map(function() {
                    return this.value;
                }).get();

                if (selectedRequests.length === 0) return;

                const isApprove = action === 'approve';
                const confirmMessage = isApprove ? 
                    'هل أنت متأكد من قبول الطلبات المحددة؟' : 
                    'هل أنت متأكد من رفض الطلبات المحددة؟';

                Swal.fire({
                    title: 'تأكيد',
                    text: confirmMessage,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: isApprove ? '#28a745' : '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: isApprove ? 'قبول' : 'رفض',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const url = isApprove ? '/pound-requests/bulk-approve' : '/pound-requests/bulk-reject';
                        
                        $.ajax({
                            url: url,
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                selected_requests: selectedRequests
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: 'نجاح!',
                                    text: response.message,
                                    icon: 'success'
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: 'خطأ!',
                                    text: xhr.responseJSON?.message || 'حدث خطأ ما',
                                    icon: 'error'
                                });
                            }
                        });
                    }
                });
            }

            approveBtn.click(() => handleBulkAction('approve'));
            rejectBtn.click(() => handleBulkAction('reject'));
        });

        // Add this new function for image modal
        function showImageModal(imageSrc) {
            const modal = new bootstrap.Modal(document.getElementById('imageModal'));
            document.querySelector('#imageModal .modal-image').src = imageSrc;
            modal.show();
        }
    </script>
</body>
</html> 