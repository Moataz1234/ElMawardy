<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة مبيعات الكسر</title>
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
    </style>
</head>
<body>
    <div class="container">
        <div class="content-container">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">إدارة مبيعات الكسر</h3>
                    {{-- <a href="{{ route('kasr-sales.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-1"></i> إضافة كسر جديد
                    </a> --}}
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
                        {{-- <div class="col-md-3 mb-3">
                            <label for="shop_name" class="form-label">اسم المحل</label>
                            <select class="form-select" id="shop_name" name="shop_name">
                                <option value="">الكل</option>
                                @foreach($shops as $shop)
                                    <option value="{{ $shop->shop_name }}" {{ request('shop_name') == $shop->shop_name ? 'selected' : '' }}>
                                        {{ $shop->shop_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div> --}}
                        {{-- <div class="col-md-3 mb-3">
                            <label for="kind" class="form-label">نوع القطعة</label>
                            <select class="form-select" id="kind" name="kind">
                                <option value="">الكل</option>
                                @foreach($kinds as $kind)
                                    <option value="{{ $kind }}" {{ request('kind') == $kind ? 'selected' : '' }}>
                                        {{ $kind }}
                                    </option>
                                @endforeach
                            </select>
                        </div> --}}
                        {{-- <div class="col-md-3 mb-3">
                            <label for="metal_purity" class="form-label">عيار الذهب</label>
                            <select class="form-select" id="metal_purity" name="metal_purity">
                                <option value="">الكل</option>
                                <option value="24K" {{ request('metal_purity') == '24K' ? 'selected' : '' }}>عيار 24</option>
                                <option value="22K" {{ request('metal_purity') == '22K' ? 'selected' : '' }}>عيار 22</option>
                                <option value="21K" {{ request('metal_purity') == '21K' ? 'selected' : '' }}>عيار 21</option>
                                <option value="18K" {{ request('metal_purity') == '18K' ? 'selected' : '' }}>عيار 18</option>
                                <option value="14K" {{ request('metal_purity') == '14K' ? 'selected' : '' }}>عيار 14</option>
                            </select>
                        </div> --}}
                        <div class="col-md-3 mb-3">
                            <label for="price_min" class="form-label">السعر (من)</label>
                            <input type="number" class="form-control" id="price_min" name="price_min" value="{{ request('price_min') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="price_max" class="form-label">السعر (إلى)</label>
                            <input type="number" class="form-control" id="price_max" name="price_max" value="{{ request('price_max') }}">
                        </div>
                        {{-- <div class="col-md-3 mb-3">
                            <label for="status" class="form-label">الحالة</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">الكل</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>مقبول</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                            </select>
                        </div> --}}
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
                            <h6>إجمالي الوزن الأصلي</h6>
                            <h3 class="weight-original">{{ number_format($totalOriginalWeight, 2) }} جرام</h3>
                        </div>
                        <div class="col-md-4 text-center">
                            <h6>إجمالي الوزن عيار 24</h6>
                            <h3 class="weight-24k">{{ number_format($total24kWeight, 2) }} جرام</h3>
                        </div>
                        <div class="col-md-4 text-center">
                            <h6>إجمالي الوزن عيار 18</h6>
                            <h3 class="weight-18k">{{ number_format($total18kWeight, 2) }} جرام</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="table-responsive">
                <table id="kasrSalesTable" class="table table-striped table-hover table-sm">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>صورة</th>
                            <th>اسم العميل</th>
                            <th>المحل</th>
                            <th>النوع</th>
                            <th>العيار</th>
                            <th>الوزن الأصلي</th>
                            <th>وزن عيار 24</th>
                            <th>وزن عيار 18</th>
                            <th>السعر المعروض</th>
                            <th>تاريخ الطلب</th>
                            {{-- <th>الحالة</th>
                            <th>الإجراءات</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kasrSales as $index => $sale)
                        @php
                            // Extract the numeric value from the purity string (e.g., "21K" -> 21)
                            $purityValue = intval(str_replace('K', '', $sale->metal_purity));
                            
                            // Calculate weights
                            $weight24k = ($purityValue / 24) * $sale->weight;
                            $weight18k = ($purityValue / 18) * $sale->weight;
                        @endphp
                        <tr>
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
                            <td>{{ $sale->shop_name }}</td>
                            <td>{{ $sale->kind ?? 'غير محدد' }}</td>
                            <td>{{ $sale->metal_purity }}</td>
                            <td class="weight-cell weight-original">{{ number_format($sale->weight, 2) }}</td>
                            <td class="weight-cell weight-24k">{{ number_format($weight24k, 2) }}</td>
                            <td class="weight-cell weight-18k">{{ number_format($weight18k, 2) }}</td>
                            <td>{{ $sale->offered_price ? number_format($sale->offered_price, 2) : 'غير محدد' }}</td>
                            <td>{{ $sale->order_date ? $sale->order_date->format('Y-m-d') : 'غير محدد' }}</td>
                            {{-- <td>
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
                                <a href="{{ route('kasr-sales.edit', $sale->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('kasr-sales.destroy', $sale->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من حذف هذا العنصر؟')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td> --}}
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

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
        });
    </script>
</body>
</html> 