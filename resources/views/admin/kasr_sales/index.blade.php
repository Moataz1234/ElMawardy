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
        .table-sm td, .table-sm th {
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
    </style>
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
                            <input type="text" class="form-control" id="date_range" name="date_range" value="{{ request('date_range') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="price_min" class="form-label">السعر (من)</label>
                            <input type="number" class="form-control" id="price_min" name="price_min" value="{{ request('price_min') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="price_max" class="form-label">السعر (إلى)</label>
                            <input type="number" class="form-control" id="price_max" name="price_max" value="{{ request('price_max') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="status" class="form-label">الحالة</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">الكل</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>مقبول</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
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
                            <button type="submit" name="action" value="accept" class="btn btn-success me-2" disabled id="accept-btn">
                                <i class="fas fa-check me-1"></i> قبول المحدد
                            </button>
                            <button type="submit" name="action" value="reject" class="btn btn-danger" disabled id="reject-btn">
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
                                <th width="10%">الوزن الكلي</th>
                                <th width="10%">السعر المعروض</th>
                                <th width="10%">تاريخ الطلب</th>
                                <th width="5%">الحالة</th>
                                <th width="5%">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kasrSales as $index => $sale)
                            <tr class="main-row">
                                <td>
                                    @if($sale->status == 'pending')
                                    <div class="form-check">
                                        <input class="form-check-input order-checkbox" type="checkbox" name="selected_orders[]" value="{{ $sale->id }}">
                                    </div>
                                    @endif
                                </td>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    @if($sale->image_path)
                                    <img src="{{ asset('storage/' . $sale->image_path) }}" 
                                        class="thumbnail" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#imageModal" 
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
                                    <span class="badge bg-info">{{ $sale->items->count() }}</span>
                                    <a href="#" class="toggle-items" data-sale-id="{{ $sale->id }}">
                                        <i class="fas fa-chevron-down"></i>
                                    </a>
                                </td>
                                <td class="weight-cell weight-original">{{ number_format($sale->getTotalWeight(), 2) }}</td>
                                <td>{{ $sale->offered_price ? number_format($sale->offered_price, 2) : 'غير محدد' }}</td>
                                <td>{{ $sale->order_date ? $sale->order_date->format('Y-m-d') : 'غير محدد' }}</td>
                                <td>
                                    @if($sale->status == 'pending')
                                        <span class="status-badge status-pending">قيد الانتظار</span>
                                    @elseif($sale->status == 'accepted')
                                        <span class="status-badge status-accepted">مقبول</span>
                                    @elseif($sale->status == 'rejected')
                                        <span class="status-badge status-rejected">مرفوض</span>
                                    @else
                                        <span class="status-badge status-pending">قيد الانتظار</span>
                                    @endif
                                </td>
                                <td class="action-buttons">
                                    <a href="{{ route('kasr-sales.show', $sale->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <div class="dropdown d-inline">
                                        <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-cog"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="{{ route('kasr-sales.edit', $sale->id) }}" class="dropdown-item">
                                                    <i class="fas fa-edit me-1"></i> تعديل
                                                </a>
                                            </li>
                                    @if($sale->status == 'pending')
                                    <li>
                                        <form action="{{ route('kasr-sales.update-status', $sale->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="accepted">
                                            <button type="submit" class="dropdown-item text-success">
                                                <i class="fas fa-check-circle me-1"></i> قبول
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <form action="{{ route('kasr-sales.update-status', $sale->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fas fa-times-circle me-1"></i> رفض
                                            </button>
                                        </form>
                                    </li>
                                    @endif
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('kasr-sales.destroy', $sale->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('هل أنت متأكد من حذف هذا العنصر؟')">
                                                <i class="fas fa-trash-alt me-1"></i> حذف
                                            </button>
                                        </form>
                                    </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <!-- Items detail row (initially hidden) -->
                            <tr class="item-details-row" id="items-{{ $sale->id }}" style="display:none;">
                                <td colspan="12">
                                    <div class="card m-2">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">تفاصيل القطع</h6>
                                        </div>
                                        <div class="card-body p-0">
                                            <table class="table table-sm mb-0">
                                                <thead class="table-secondary">
                                                    <tr>
                                                        <th>#</th>
                                                        <th>نوع القطعة</th>
                                                        <th>عيار الذهب</th>
                                                        <th>الوزن القائم</th>
                                                        <th>الوزن الصافي</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($sale->items as $itemIndex => $item)
                                                    <tr>
                                                        <td>{{ $itemIndex + 1 }}</td>
                                                        <td>{{ $item->kind }}</td>
                                                        <td>{{ $item->metal_purity }}</td>
                                                        <td>{{ number_format($item->weight, 2) }}</td>
                                                        <td>{{ number_format($item->net_weight, 2) }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
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
                    monthNames: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'],
                    firstDay: 6
                }
            });

            $('#date_range').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
            });

            $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });

            // Image Modal
            $('#imageModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var imgSrc = button.data('img-src');
                var modal = $(this);
                modal.find('#modalImage').attr('src', imgSrc);
            });
            
            // Toggle item details
            $('.toggle-items').on('click', function(e) {
                e.preventDefault();
                var saleId = $(this).data('sale-id');
                var detailsRow = $('#items-' + saleId);
                var icon = $(this).find('i');
                
                detailsRow.toggle();
                
                if (detailsRow.is(':visible')) {
                    icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
                } else {
                    icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
                }
            });
            
            // Checkbox selection logic
            $('#select-all').change(function() {
                $('.order-checkbox').prop('checked', this.checked);
                updateSelectedCount();
                updateActionButtons();
            });
            
            $('.order-checkbox').change(function() {
                updateSelectedCount();
                updateActionButtons();
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
            
            function updateActionButtons() {
                const anySelected = $('.order-checkbox:checked').length > 0;
                $('#accept-btn, #reject-btn').prop('disabled', !anySelected);
            }
        });
    </script>
</body>
</html> 