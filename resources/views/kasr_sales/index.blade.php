<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سجل مشتريات الكسر</title>
    @include('components.navbar')
    <!-- Bootstrap RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.rtl.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
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
    </style>
</head>
<body>
    <div class="container">
        <div class="content-container">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">سجل مشتريات الكسر</h3>
                    <a href="{{ route('kasr-sales.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-1"></i> إضافة كسر جديد
                    </a>
                </div>
            </div>

            <!-- Data Table -->
            <div class="table-responsive">
                <table id="kasrSalesTable" class="table table-striped table-hover table-sm">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="7%">صورة</th>
                            <th width="15%">اسم العميل</th>
                            <th width="10%">رقم الهاتف</th>
                            <th width="10%">عدد القطع</th>
                            <th width="10%">الوزن الكلي</th>
                            <th width="10%">السعر المعروض</th>
                            <th width="10%">تاريخ الطلب</th>
                            <th width="5%">الحالة</th>
                            {{-- <th width="5%">الإجراءات</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kasrSales as $index => $sale)
                        <tr class="main-row">
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
                            <td>
                                <span class="badge bg-info">{{ $sale->items->count() }}</span>
                                <a href="#" class="toggle-items" data-sale-id="{{ $sale->id }}">
                                    <i class="fas fa-chevron-down"></i>
                                </a>
                            </td>
                            <td>{{ number_format($sale->getTotalWeight(), 2) }}</td>
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
                            {{-- <td class="action-buttons">
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
                        <!-- Items detail row (initially hidden) -->
                        <tr class="item-details-row" id="items-{{ $sale->id }}" style="display:none;">
                            <td colspan="10">
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
                                                    <th>نوع القطعة</th>
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
                                                    <td>
                                                        @if($item->item_type == 'shop')
                                                            <span class="badge bg-primary">من صنعنا</span>
                                                        @else
                                                            <span class="badge bg-secondary">من العميل</span>
                                                        @endif
                                                    </td>
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

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $kasrSales->links() }}
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
        });
    </script>
</body>
</html> 