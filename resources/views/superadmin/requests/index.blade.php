<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Root - Add Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .nav-tabs .nav-link.active {
            font-weight: bold;
            border-bottom: 3px solid #0d6efd;
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
    <div class="container mt-5">
        <h1 class="text-center mb-4">Root Add Requests</h1>
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

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-4" id="requestTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="items-tab" data-bs-toggle="tab" data-bs-target="#items" type="button">
                    Item Requests
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pounds-tab" data-bs-toggle="tab" data-bs-target="#pounds" type="button">
                    Pound Requests
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="requestTabsContent">
            <!-- Items Requests Tab -->
            <div class="tab-pane fade show active" id="items" role="tabpanel">
                <form id="bulk-action-form" action="{{ route('superadmin.requests.bulk-action') }}" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="accept">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="select-all-items"></th>
                                <th>Shop</th>
                                <th>Model</th>
                                <th>Serial Number</th>
                                <th>Kind</th>
                                <th>Weight</th>
                                <th>Gold Color</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalWeight = 0;
                                $totalItems = count($itemRequests);
                            @endphp
                            @foreach ($itemRequests as $request)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="selected_requests[]" value="{{ $request->id }}">
                                    </td>
                                    <td>{{ $request->shop_name }}</td>
                                    <td>{{ $request->model }}</td>
                                    <td>{{ $request->serial_number }}</td>
                                    <td>{{ $request->kind }}</td>
                                    <td>{{ $request->weight }}</td>
                                    <td>{{ $request->gold_color }}</td>
                                    <td>{{ $request->status }}</td>
                                    <td>{{ $request->rest_since }}</td>
                                </tr>
                                @php
                                    $totalWeight += $request->weight;
                                @endphp
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-between">
                        <strong class="total_items badge bg-danger col-4 fs-6">Total Items: <span class="fs-6">{{ $totalItems }}</span></strong>
                        <strong class="total_weight badge bg-warning col-4 fs-6">Total Weight: <span class="fs-6">{{ $totalWeight }}</span></strong>
                    </div>
                    <div class="mt-3">
                        <button type="button" id="accept-selected-items" class="btn btn-success">Accept Selected Items</button>
                        <button type="button" id="reject-selected-items" class="btn btn-danger">Reject Selected Items</button>
                    </div>
                </form>
            </div>
  <!-- Pounds Requests Tab -->
  <div class="tab-pane fade" id="pounds" role="tabpanel">
    <!-- Add Filters -->
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text">المتجر</span>
                <select class="form-select" id="shopFilter">
                    <option value="">الكل</option>
                    @php
                        $uniqueShops = $poundRequests->pluck('shop_name')->unique();
                    @endphp
                    @foreach($uniqueShops as $shop)
                        <option value="{{ $shop }}">{{ $shop }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text">نوع السبيكة</span>
                <select class="form-select" id="kindFilter">
                    <option value="">الكل</option>
                    @php
                        $uniqueKinds = $poundRequests->pluck('goldPound.kind')->unique();
                    @endphp
                    @foreach($uniqueKinds as $kind)
                        <option value="{{ $kind }}">{{ $kind }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="selectAllPounds">
                        </div>
                    </th>
                    <th>المتجر</th>
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
                @forelse($poundRequests as $request)
                    <tr>
                        <td>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input pound-checkbox" 
                                    value="{{ $request->id }}"
                                    {{ $request->status !== 'pending' ? 'disabled' : '' }}>
                            </div>
                        </td>
                        <td>{{ $request->shop_name }}</td>
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
                        <td colspan="11" class="text-center">لا توجد طلبات حالياً</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        <button id="approveSelectedPounds" class="btn btn-success" disabled>
            <i class="fas fa-check me-1"></i> قبول المحدد
        </button>
    </div>
</div>
</div>
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

    <!-- Include the same modals and scripts as in the original view -->
    <!-- ... -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Items tab functionality
        document.getElementById('select-all-items').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('input[name="selected_requests[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        document.getElementById('accept-selected-items').addEventListener('click', function() {
            const selectedRequests = document.querySelectorAll('input[name="selected_requests[]"]:checked');
            if (selectedRequests.length > 0) {
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

        // Add this new event listener
        document.getElementById('confirm-accept').addEventListener('click', function() {
            document.getElementById('bulk-action-form').submit();
        });

        // Pounds tab functionality
        $(document).ready(function() {
            // Add CSRF token to all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const selectAllPounds = $('#selectAllPounds');
            const poundCheckboxes = $('.pound-checkbox');
            const approveBtn = $('#approveSelectedPounds');

            function updateButtonsState() {
                const checkedBoxes = $('.pound-checkbox:checked').length;
                approveBtn.prop('disabled', checkedBoxes === 0);
            }

            selectAllPounds.change(function() {
                poundCheckboxes.not(':disabled').prop('checked', this.checked);
                updateButtonsState();
            });

            poundCheckboxes.change(function() {
                updateButtonsState();
                selectAllPounds.prop('checked', 
                    $('.pound-checkbox:checked').length === $('.pound-checkbox:not(:disabled)').length
                );
            });

            approveBtn.click(function() {
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
                            url: '{{ route("superadmin.requests.bulk-approve-pounds") }}',
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

            // Add this new filtering functionality
            const shopFilter = $('#shopFilter');
            const kindFilter = $('#kindFilter');
            const poundRows = $('#pounds tbody tr');

            function filterTable() {
                const selectedShop = shopFilter.val();
                const selectedKind = kindFilter.val();

                poundRows.each(function() {
                    const row = $(this);
                    const shopName = row.find('td:eq(1)').text(); // Shop name is in second column
                    const kind = row.find('td:eq(3)').text(); // Kind is in fourth column

                    const shopMatch = !selectedShop || shopName === selectedShop;
                    const kindMatch = !selectedKind || kind === selectedKind;

                    if (shopMatch && kindMatch) {
                        row.show();
                    } else {
                        row.hide();
                    }
                });

                // Update "No requests" message visibility
                const visibleRows = $('#pounds tbody tr:visible').length;
                if (visibleRows === 0) {
                    if ($('#no-results-message').length === 0) {
                        $('#pounds tbody').append(
                            '<tr id="no-results-message"><td colspan="11" class="text-center">لا توجد نتائج للبحث</td></tr>'
                        );
                    }
                } else {
                    $('#no-results-message').remove();
                }
            }

            // Add event listeners for filters
            shopFilter.on('change', filterTable);
            kindFilter.on('change', filterTable);

            // Add reset filters button functionality
            $('#resetFilters').on('click', function() {
                shopFilter.val('');
                kindFilter.val('');
                filterTable();
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