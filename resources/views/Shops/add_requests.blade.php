<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طلبات الاضافة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="{{ url('css/addRequests.css') }}" rel="stylesheet">
    <style>
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
        .section-header {
            background-color: #f8f9fa;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border-left: 5px solid #0d6efd;
        }
    </style>
    @include('components.navbar')
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">طلبات الاضافة</h1>
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {!! session('success') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Combined Requests Section -->
        <div class="section-header d-flex justify-content-between align-items-center">
            <h3 class="mb-0">طلبات الاضافة</h3>
        </div>
        
        <form id="bulk-action-form" action="{{ route('add-requests.bulk-action') }}" method="POST">
            @csrf
            <input type="hidden" name="action" value="accept">
            
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="select-all"></th>
                            <th>نوع الطلب</th>
                            <th>الرقم التسلسلي</th>
                            <th>الموديل/نوع السبيكة</th>
                            <th>النوع</th>
                            <th>الوزن</th>
                            <th>لون الذهب/العيار</th>
                            <th>الكمية</th>
                            <th>الصورة</th>
                            <th>الحالة</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalWeight = 0;
                            $totalItems = count($itemRequests);
                            $totalPounds = count($poundRequests);
                        @endphp
                        
                        <!-- Item Requests -->
                        @forelse ($itemRequests as $request)
                            <tr>
                                <td>
                                    <input type="checkbox" name="selected_requests[]" value="{{ $request->id }}" class="item-checkbox">
                                </td>
                                <td><span class="badge bg-primary">قطعة</span></td>
                                <td>{{ $request->serial_number }}</td>
                                <td>{{ $request->model }}</td>
                                <td>{{ $request->kind }}</td>
                                <td>{{ $request->weight }}</td>
                                <td>{{ $request->gold_color }}</td>
                                <td>{{ $request->quantity ?? '1' }}</td>
                                <td>-</td>
                                <td>
                                    <span class="badge bg-{{ $request->status === 'pending' ? 'warning' : ($request->status === 'accepted' ? 'success' : 'danger') }}">
                                        {{ $request->status === 'pending' ? 'قيد الانتظار' : ($request->status === 'accepted' ? 'مقبول' : 'مرفوض') }}
                                    </span>
                                </td>
                                <td>{{ $request->rest_since }}</td>
                            </tr>
                            @php
                                $totalWeight += $request->weight;
                            @endphp
                        @empty
                            <!-- Empty state will be handled after all loops -->
                        @endforelse
                        
                        <!-- Pound Requests -->
                        @forelse ($poundRequests as $request)
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input pound-checkbox" 
                                            value="{{ $request->id }}"
                                            {{ $request->status !== 'pending' ? 'disabled' : '' }}>
                                    </div>
                                </td>
                                <td><span class="badge bg-warning">جنيه/تول</span></td>
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
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $request->status === 'pending' ? 'warning' : ($request->status === 'approved' ? 'success' : 'danger') }}">
                                        {{ $request->status === 'pending' ? 'قيد الانتظار' : ($request->status === 'approved' ? 'مقبول' : 'مرفوض') }}
                                    </span>
                                </td>
                                <td>{{ $request->created_at->format('Y-m-d') }}</td>
                            </tr>
                        @empty
                            <!-- Empty state will be handled below -->
                        @endforelse
                        
                        @if(count($itemRequests) == 0 && count($poundRequests) == 0)
                            <tr>
                                <td colspan="11" class="text-center">لا توجد طلبات حالياً</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-between mt-3">
                <div class="col-md-4">
                    <span class="badge bg-primary">قطع: {{ $totalItems }}</span>
                    <span class="ms-2 badge bg-warning">جنيهات: {{ $totalPounds }}</span>
                </div>
                <div class="col-md-4 text-center">
                    <span class="badge bg-info">إجمالي الوزن: {{ $totalWeight }}</span>
                </div>
                <div class="col-md-4 text-end">
                    <button type="button" id="accept-selected-items" class="btn btn-success">قبول القطع المحددة</button>
                    <button type="button" id="approveSelectedPounds" class="btn btn-warning" disabled>
                        <i class="fas fa-check me-1"></i> قبول الجنيهات المحددة
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Confirmation Modals -->
    @include('components.confirmation-modal')
    
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Add CSRF token to all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            
            // Common select all functionality
            $("#select-all").change(function() {
                const isChecked = $(this).prop('checked');
                
                // For item checkboxes
                $(".item-checkbox").prop('checked', isChecked);
                
                // For pound checkboxes - only check enabled ones
                $(".pound-checkbox:not(:disabled)").prop('checked', isChecked);
                
                updateButtonsState();
            });
            
            // Individual checkbox handling
            $(".item-checkbox, .pound-checkbox").change(function() {
                updateButtonsState();
                
                // Check if all are selected to update the select-all checkbox
                const allItemsChecked = $(".item-checkbox:not(:checked)").length === 0;
                const allPoundsChecked = $(".pound-checkbox:not(:disabled):not(:checked)").length === 0;
                
                // If all eligible checkboxes are checked, check the select-all
                $("#select-all").prop('checked', 
                    allItemsChecked && allPoundsChecked && 
                    ($(".item-checkbox").length > 0 || $(".pound-checkbox:not(:disabled)").length > 0)
                );
            });
            
            function updateButtonsState() {
                const itemCheckedCount = $(".item-checkbox:checked").length;
                const poundCheckedCount = $(".pound-checkbox:checked").length;
                
                // Enable/disable appropriate buttons
                $("#accept-selected-items").prop('disabled', itemCheckedCount === 0);
                $("#approveSelectedPounds").prop('disabled', poundCheckedCount === 0);
            }
            
            // Item approval
            $("#accept-selected-items").click(function() {
                const selectedRequests = $(".item-checkbox:checked").length;
                if (selectedRequests > 0) {
                    const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
                    modal.show();
                } else {
                    Swal.fire({
                        title: "عفوا",
                        text: "عليك اختيار قطعة على الاقل",
                        icon: "info"
                    });
                }
            });

            // Confirm action on modal
            document.getElementById('confirm-accept').addEventListener('click', function() {
                document.getElementById('bulk-action-form').submit();
            });
            
            // Pounds approval
            $("#approveSelectedPounds").click(function() {
                const selectedRequests = $('.pound-checkbox:checked').map(function() {
                    return this.value;
                }).get();

                if (selectedRequests.length === 0) {
                    Swal.fire({
                        title: 'تنبيه',
                        text: 'الرجاء اختيار طلب واحد على الأقل',
                        icon: 'warning'
                    });
                    return;
                }

                Swal.fire({
                    title: 'تأكيد',
                    text: 'هل أنت متأكد من قبول الطلبات المحددة؟',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'قبول',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route("pound-requests.bulk-approve") }}',
                            method: 'POST',
                            data: {
                                selected_requests: selectedRequests
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: 'تم!',
                                        text: response.message,
                                        icon: 'success'
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'خطأ!',
                                        text: response.message,
                                        icon: 'error'
                                    });
                                }
                            },
                            error: function(xhr) {
                                const errorMessage = xhr.responseJSON?.message || 'حدث خطأ أثناء معالجة الطلب';
                                Swal.fire({
                                    title: 'خطأ!',
                                    text: errorMessage,
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
