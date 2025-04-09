<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة شراء الكسر</title>
    @include('components.navbar')
    <!-- Bootstrap RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.rtl.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <!-- DateRangePicker CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .content-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            padding: 25px;
            margin-top: 20px;
            margin-bottom: 20px;
            direction: rtl;
        }

        .page-header {
            background-color: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            border-right: 5px solid #0d6efd;
        }

        .filter-card {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
        }

        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
        }

        .table th {
            background-color: #0d6efd;
            color: white;
            font-weight: 600;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-pending {
            background-color: #ffc107;
            color: #212529;
        }

        .status-accepted {
            background-color: #198754;
            color: white;
        }

        .status-rejected {
            background-color: #dc3545;
            color: white;
        }

        .thumbnail {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .thumbnail:hover {
            transform: scale(1.1);
        }

        .modal-img {
            max-width: 100%;
            max-height: 80vh;
        }

        .action-buttons .btn {
            margin-right: 5px;
            padding: 5px 10px;
            font-size: 0.8rem;
        }

        .daterangepicker {
            direction: ltr;
        }

        .weight-cell {
            font-weight: 600;
        }

        .weight-24k {
            color: #198754;
        }

        .weight-18k {
            color: #0d6efd;
        }

        .weight-original {
            color: #6c757d;
        }

        .table-sm td,
        .table-sm th {
            padding: 0.5rem;
        }

        .item-details-row {
            background-color: #f8f9fa;
        }

        .toggle-items {
            color: #0d6efd;
            padding: 0 5px;
            cursor: pointer;
        }

        .toggle-items:hover {
            color: #0a58ca;
        }

        .main-row:hover {
            background-color: #e9ecef;
        }

        .select-all-container {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f0f8ff;
            border-radius: 5px;
            border-right: 3px solid #0d6efd;
        }

        .form-check-input.order-checkbox {
            width: 1.2em;
            height: 1.2em;
        }

        .batch-actions {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #e8f4fd;
            border-radius: 5px;
            border: 1px solid #b8daff;
        }

        /* SweetAlert2 RTL Support */
        .rtl-alert {
            direction: rtl;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .rtl-alert .swal2-title,
        .rtl-alert .swal2-content {
            text-align: right;
        }
    </style>
    <!-- Add this in the head section -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>

<body>
    <div class="container">
        <div class="content-container">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">إدارة مبيعات الكسر</h3>
                    <a href="{{ route('kasr-sales.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-1"></i> إضافة كسر جديد
                    </a>
                </div>
            </div>

            <!-- Filters -->
            <div class="filter-card">
                <form id="filter-form" method="GET" action="{{ route('kasr-sales.index') }}">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="date_range" class="form-label">نطاق التاريخ</label>
                            <input type="text" class="form-control" id="date_range" name="date_range"
                                value="{{ request('date_range') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="price_min" class="form-label">السعر (من)</label>
                            <input type="number" class="form-control" id="price_min" name="price_min"
                                value="{{ request('price_min') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="price_max" class="form-label">السعر (إلى)</label>
                            <input type="number" class="form-control" id="price_max" name="price_max"
                                value="{{ request('price_max') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="status" class="form-label">الحالة</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">الكل</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد
                                    الانتظار</option>
                                <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>مقبول
                                </option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-1"></i> تصفية
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tabs -->
            <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="sales-tab" data-bs-toggle="tab" data-bs-target="#sales" type="button" role="tab" aria-controls="sales" aria-selected="true">
                        المبيعات
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="items-tab" data-bs-toggle="tab" data-bs-target="#items" type="button" role="tab" aria-controls="items" aria-selected="false">
                        ملخص الأوزان
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="myTabContent">
                <!-- Sales Tab -->
                <div class="tab-pane fade show active" id="sales" role="tabpanel" aria-labelledby="sales-tab">
                    <!-- Summary Card -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">ملخص الأوزان</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 text-center">
                                    <h6>إجمالي الوزن القائم</h6>
                                    <h3 class="weight-original">{{ number_format($totalOriginalWeight, 2) }} جرام</h3>
                                </div>
                                <div class="col-md-4 text-center">
                                    <h6>إجمالي الوزن الصافي</h6>
                                    <h3 class="weight-24k">{{ number_format($totalNetWeight, 2) }} جرام</h3>
                                </div>
                                <div class="col-md-4 text-center">
                                    <h6>عدد الطلبات المعلقة</h6>
                                    <h3 class="text-warning">{{ $pendingCount }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Batch Actions Form -->
                    <form id="batch-actions-form" action="{{ route('kasr-sales.batch-update') }}" method="POST">
                        @csrf

                        <!-- Batch Actions Buttons -->
                        <div class="batch-actions">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="select-all-container d-inline-block me-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="select-all">
                                            <label class="form-check-label" for="select-all">
                                                تحديد الكل
                                            </label>
                                        </div>
                                    </div>
                                    <span id="selected-count" class="badge bg-primary me-2">0</span> طلب محدد
                                </div>
                                <div>
                                    <button type="submit" name="action" value="accept" class="btn btn-success me-2">
                                        <i class="fas fa-check me-1"></i> قبول المحدد
                                    </button>
                                    <button type="submit" name="action" value="reject" class="btn btn-danger">
                                        <i class="fas fa-times me-1"></i> رفض المحدد
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Data Table -->
                        <div class="table-responsive">
                            <table id="kasrSalesTable" class="table table-striped table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th width="3%"></th>
                                        <th width="5%">#</th>
                                        <th width="7%">صورة</th>
                                        <th width="15%">اسم العميل</th>
                                        <th width="10%">رقم الهاتف</th>
                                        <th width="10%">المحل</th>
                                        <th width="10%">عدد القطع</th>
                                        <th width="10%">الوزن القائم</th>
                                        {{-- <th width="10%">الوزن الكلي</th> --}}
                                        <th width="10%">الوزن الصافي</th>
                                        <th width="10%">السعر المعروض</th>
                                        <th width="10%">تاريخ الطلب</th>
                                        <th width="5%">الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kasrSales as $index => $sale)
                                        <tr class="main-row">
                                            <td>
                                                @if ($sale->status == 'pending')
                                                    <div class="form-check">
                                                        <input class="form-check-input order-checkbox" type="checkbox"
                                                            name="selected_orders[]" value="{{ $sale->id }}">
                                                    </div>
                                                @endif
                                            </td>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                @if ($sale->image_path)
                                                    <img src="{{ asset('storage/' . $sale->image_path) }}" class="thumbnail"
                                                        data-bs-toggle="modal" data-bs-target="#imageModal"
                                                        data-img-src="{{ asset('storage/' . $sale->image_path) }}"
                                                        alt="صورة الكسر">
                                                @else
                                                    <span class="text-muted">لا توجد صورة</span>
                                                @endif
                                            </td>
                                            <td>{{ $sale->customer_name }}</td>
                                            <td>{{ $sale->customer_phone ?? 'غير متوفر' }}</td>
                                            <td>{{ $sale->shop_name }}</td>
                                            <td>
                                                <a href="#" class="toggle-items" data-bs-toggle="modal"
                                                    data-bs-target="#itemsModal" data-sale-id="{{ $sale->id }}">
                                                    <span class="badge bg-info">{{ $sale->items->count() }}</span>
                                                    <i class="fas fa-eye ms-1"></i>
                                                </a>
                                            </td>
                                            <td class="weight-cell weight-original">
                                                {{ number_format($sale->getTotalWeight(), 2) }}</td>
                                            <td class="weight-cell weight-24k">
                                                {{ number_format($sale->getTotalNetWeight(), 2) }}</td>

                                            <td>{{ $sale->offered_price ? number_format($sale->offered_price, 2) : 'غير محدد' }}
                                            </td>
                                            <td>{{ $sale->order_date ? $sale->order_date->format('Y-m-d') : 'غير محدد' }}</td>
                                            <td>
                                                @if ($sale->status == 'pending')
                                                    <span class="status-badge status-pending">قيد الانتظار</span>
                                                @elseif($sale->status == 'accepted')
                                                    <span class="status-badge status-accepted">مقبول</span>
                                                @elseif($sale->status == 'rejected')
                                                    <span class="status-badge status-rejected">مرفوض</span>
                                                @else
                                                    <span class="status-badge status-pending">قيد الانتظار</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </form>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $kasrSales->appends(request()->query())->links() }}
                    </div>
                </div>

                <!-- Items Tab -->
                <div class="tab-pane fade" id="items" role="tabpanel" aria-labelledby="items-tab">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">ملخص الأوزان حسب المحلات والعيارات</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>المحل</th>
                                            @foreach($purities as $purity)
                                                <th class="text-center">{{ $purity }}K</th>
                                            @endforeach
                                            <th class="text-center">المجموع</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($shopNames as $shopName)
                                            <tr>
                                                <td>{{ $shopName }}</td>
                                                @php
                                                    $rowTotal = 0;
                                                @endphp
                                                @foreach($purities as $purity)
                                                    @php
                                                        $weight = $structuredData[$shopName][$purity] ?? 0;
                                                        $rowTotal += $weight;
                                                    @endphp
                                                    <td class="text-center">{{ number_format($weight, 2) }}</td>
                                                @endforeach
                                                <td class="text-center fw-bold">{{ number_format($rowTotal, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-primary">
                                            <td class="fw-bold">المجموع</td>
                                            @php
                                                $columnTotals = array_fill_keys($purities, 0);
                                            @endphp
                                            @foreach($shopNames as $shopName)
                                                @foreach($purities as $purity)
                                                    @php
                                                        $columnTotals[$purity] += $structuredData[$shopName][$purity] ?? 0;
                                                    @endphp
                                                @endforeach
                                            @endforeach
                                            @foreach($purities as $purity)
                                                <td class="text-center fw-bold">{{ number_format($columnTotals[$purity], 2) }}</td>
                                            @endforeach
                                            <td class="text-center fw-bold">{{ number_format(array_sum($columnTotals), 2) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">صورة الكسر</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="" class="modal-img" id="modalImage" alt="صورة الكسر">
                </div>
            </div>
        </div>
    </div>

    <!-- Items Modal -->
    <div class="modal fade" id="itemsModal" tabindex="-1" aria-labelledby="itemsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="itemsModalLabel">تفاصيل القطع</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="direction: rtl;">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>النوع</th>
                                    <th>عيار الذهب</th>
                                    <th>الوزن القائم</th>
                                    <th>الوزن الصافي</th>
                                    <th>الصنع</th>
                                </tr>
                            </thead>
                            <tbody id="modal-items-content">
                                <!-- Items will be loaded here via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <!-- Moment.js -->
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <!-- DateRangePicker -->
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <!-- Add this before your custom script -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        var itemsUrl = "{{ route('admin.kasr-sales.items', ['kasrSale' => ':saleId']) }}";
    </script>
    
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#kasrSalesTable').DataTable({
                "paging": false,
                "ordering": true,
                "info": false,
                "searching": false,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/ar.json"
                }
            });

            // Initialize DateRangePicker
            $('#date_range').daterangepicker({
                opens: 'left',
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'مسح',
                    applyLabel: 'تطبيق',
                    fromLabel: 'من',
                    toLabel: 'إلى',
                    customRangeLabel: 'نطاق مخصص',
                    daysOfWeek: ['أحد', 'إثنين', 'ثلاثاء', 'أربعاء', 'خميس', 'جمعة', 'سبت'],
                    monthNames: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس',
                        'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'
                    ],
                    firstDay: 6
                }
            });

            $('#date_range').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format(
                    'YYYY-MM-DD'));
            });

            $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });

            // Image Modal
            $('#imageModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var imgSrc = button.data('img-src');
                var modal = $(this);
                modal.find('#modalImage').attr('src', imgSrc);
            });

            // Handle items modal
            $('.toggle-items').on('click', function(e) {
                e.preventDefault();
                var saleId = $(this).data('sale-id');

                // Show loading indicator
                $('#modal-items-content').html(
                    '<tr><td colspan="6" class="text-center">جاري التحميل...</td></tr>');

                // Show the modal
                $('#itemsModal').modal('show');

                // Fetch items via AJAX
                $.ajax({
                    url: itemsUrl.replace(':saleId', saleId),
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        console.log('AJAX Success:', response); // Log the response

                        var html = '';

                        if (response.items && response.items.length > 0) {
                            $.each(response.items, function(index, item) {
                                html += '<tr>';
                                html += '<td>' + (index + 1) + '</td>';
                                html += '<td>' + item.kind + '</td>';
                                html += '<td>' + item.metal_purity + '</td>';
                                html += '<td>' + item.weight + '</td>';
                                html += '<td>' + item.net_weight + '</td>';
                                html += '<td>';

                                if (item.item_type == 'shop') {
                                    html +=
                                        '<span class="badge bg-primary">من صنعنا</span>';
                                } else {
                                    html +=
                                        '<span class="badge bg-secondary">من العميل</span>';
                                }

                                html += '</td>';
                                html += '</tr>';
                            });
                        } else {
                            html =
                                '<tr><td colspan="6" class="text-center">لا توجد قطع لهذا الطلب</td></tr>';
                        }

                        $('#modal-items-content').html(html);
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                        console.log('Response:', xhr.responseText);
                        $('#modal-items-content').html(
                            '<tr><td colspan="6" class="text-center text-danger">حدث خطأ أثناء تحميل البيانات: ' +
                            error + '</td></tr>');
                    }
                });
            });
            // Remove the disabled attribute from accept and reject buttons
            $('#accept-btn, #reject-btn').removeAttr('disabled');

            // Handle form submission
            $('#batch-actions-form').on('submit', function(e) {
                e.preventDefault(); // Prevent default form submission

                const selectedCount = $('.order-checkbox:checked').length;

                if (selectedCount === 0) {
                    Swal.fire({
                        title: 'تنبيه!',
                        text: 'الرجاء تحديد طلب واحد على الأقل',
                        icon: 'warning',
                        confirmButtonText: 'حسناً',
                        confirmButtonColor: '#3085d6',
                        customClass: {
                            popup: 'rtl-alert'
                        }
                    });
                    return false;
                }

                // Get the action (accept/reject)
                const action = $(document.activeElement).val();
                const actionText = action === 'accept' ? 'قبول' : 'رفض';

                // Show confirmation dialog
                Swal.fire({
                    title: `تأكيد ${actionText} الطلبات`,
                    text: `هل أنت متأكد من ${actionText} ${selectedCount} طلب؟`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: action === 'accept' ? '#28a745' : '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: `نعم، ${actionText}`,
                    cancelButtonText: 'إلغاء',
                    customClass: {
                        popup: 'rtl-alert'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Submit the form
                        this.submit();
                    }
                });
            });

            // Checkbox selection logic
            $('#select-all').change(function() {
                $('.order-checkbox').prop('checked', this.checked);
                updateSelectedCount();
            });

            $('.order-checkbox').change(function() {
                updateSelectedCount();

                // If not all checkboxes are checked, uncheck "select all"
                if (!$(this).prop('checked')) {
                    $('#select-all').prop('checked', false);
                }

                // If all checkboxes are checked, check "select all"
                if ($('.order-checkbox:checked').length === $('.order-checkbox').length) {
                    $('#select-all').prop('checked', true);
                }
            });

            function updateSelectedCount() {
                const count = $('.order-checkbox:checked').length;
                $('#selected-count').text(count);
            }
        });
    </script>
</body>

</html>
