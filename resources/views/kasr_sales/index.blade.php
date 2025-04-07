<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سجل مشتريات الكسر</title>
    
    <!-- Bootstrap RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            direction: rtl;
        }
        
        /* Card hover effect */
        .hover-shadow {
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .hover-shadow:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
        
        /* Status pills */
        .status-pill {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .status-pending {
            background-color: #fff8dd;
            color: #ffc107;
        }
        .status-accepted {
            background-color: #e8f7ee;
            color: #28a745;
        }
        .status-rejected {
            background-color: #feeeef;
            color: #dc3545;
        }
        
        /* Gradients for stat cards */
        .bg-gradient-primary-light {
            background: linear-gradient(45deg, #f8f9ff, #e6f0ff);
        }
        .bg-gradient-success-light {
            background: linear-gradient(45deg, #f8fff9, #e6fff0);
        }
        .bg-gradient-info-light {
            background: linear-gradient(45deg, #f9fdff, #e6f8ff);
        }
        
        /* Sale thumbnail */
        .sale-thumbnail {
            max-height: 150px;
            object-fit: cover;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .sale-thumbnail:hover {
            transform: scale(1.05);
        }
        
        /* No image placeholder */
        .no-image-placeholder {
            height: 150px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background-color: #f8f9fa;
            border-radius: 0.25rem;
        }
        
        /* Header section */
        .page-header {
            background-color: #ffffff;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            padding: 1.5rem 0;
            margin-bottom: 2rem;
        }
        
        /* Animated toggle icon */
        .toggle-items:focus {
            box-shadow: none;
        }
        
        /* Pagination styles */
        .pagination {
            --bs-pagination-active-bg: #0d6efd;
            --bs-pagination-active-border-color: #0d6efd;
        }
        
        /* Navbar Styles */
        .app-navbar {
            background-color: #0d6efd;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        .app-navbar .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .app-navbar .nav-link {
            color: rgba(255, 255, 255, 0.85);
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            transition: all 0.2s;
        }
        
        .app-navbar .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .app-navbar .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        /* User dropdown */
        .user-dropdown .dropdown-toggle::after {
            display: none;
        }
        
        .user-dropdown .dropdown-menu {
            min-width: 16rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border: none;
            border-radius: 0.5rem;
            margin-top: 0.5rem;
        }
        
        .user-dropdown .user-info {
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 0.5rem 0.5rem 0 0;
        }
        
        .user-dropdown .dropdown-item {
            padding: 0.75rem 1.5rem;
        }
        
        .user-dropdown .dropdown-item:hover {
            background-color: #f8f9fa;
        }
        
        .user-dropdown .dropdown-item i {
            width: 1.25rem;
            margin-left: 0.5rem;
            text-align: center;
        }
        
        .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            padding: 0.25rem 0.5rem;
            border-radius: 50%;
            font-size: 0.75rem;
        }
    </style>
</head>
<body>
    <!-- Include Navigation Bar Component -->
    @include('components.navbar')

    <!-- Main Content -->
    <div class="container py-4">
        <!-- Flash Messages -->
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-lg-8">
                <div class="d-flex align-items-center">
                    <div class="bg-primary rounded-circle p-3 me-3 text-white shadow-sm">
                        <i class="fas fa-gem fa-2x"></i>
                    </div>
                    <div>
                        <h1 class="h3 mb-0 fw-bold text-primary">سجل مشتريات الكسر</h1>
                        <p class="text-muted mb-0">عرض جميع عمليات شراء الكسر والمجوهرات</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-start">
                <a href="{{ route('kasr-sales.create') }}" class="btn btn-primary btn-lg shadow-sm">
                    <i class="fas fa-plus-circle me-2"></i> إضافة كسر جديد
                </a>
            </div>
        </div>

        <!-- Stats Section -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="card bg-gradient-primary-light h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-primary p-3 me-3 text-white">
                                <i class="fas fa-weight-hanging fa-lg"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 text-muted">إجمالي الوزن القائم</h6>
                                <h3 class="mb-0 fw-bold">{{ number_format($kasrSales->sum(function($sale) { return $sale->getTotalWeight(); }), 2) }} جرام</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="card bg-gradient-success-light h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-success p-3 me-3 text-white">
                                <i class="fas fa-balance-scale fa-lg"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 text-muted">إجمالي الوزن الصافي</h6>
                                <h3 class="mb-0 fw-bold">{{ number_format($kasrSales->sum(function($sale) { return $sale->getTotalNetWeight(); }), 2) }} جرام</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-gradient-info-light h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-info p-3 me-3 text-white">
                                <i class="fas fa-money-bill-wave fa-lg"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 text-muted">إجمالي المبالغ المدفوعة</h6>
                                <h3 class="mb-0 fw-bold">{{ number_format($kasrSales->sum('offered_price'), 2) }} ج.م</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Accordion -->
        <div class="accordion mb-4 shadow-sm" id="filterAccordion">
            <div class="accordion-item border-0 rounded">
                <h2 class="accordion-header" id="headingFilter">
                    <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFilter" aria-expanded="false" aria-controls="collapseFilter">
                        <i class="fas fa-filter me-2"></i> خيارات التصفية والبحث
                    </button>
                </h2>
                <div id="collapseFilter" class="accordion-collapse collapse" aria-labelledby="headingFilter" data-bs-parent="#filterAccordion">
                    <div class="accordion-body">
                        <form action="{{ route('kasr-sales.index') }}" method="GET">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">اسم العميل</label>
                                    <input type="text" class="form-control" name="customer_name" value="{{ request('customer_name') }}" placeholder="ابحث باسم العميل">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">تاريخ الطلب (من)</label>
                                    <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">تاريخ الطلب (إلى)</label>
                                    <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">الحالة</label>
                                    <select class="form-select" name="status">
                                        <option value="">جميع الحالات</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                        <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>مقبول</option>
                                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                                    </select>
                                </div>
                                <div class="col-12 text-center mt-3">
                                    <button type="submit" class="btn btn-primary px-4 me-2">
                                        <i class="fas fa-search me-2"></i> بحث
                                    </button>
                                    <a href="{{ route('kasr-sales.index') }}" class="btn btn-outline-secondary px-4">
                                        <i class="fas fa-redo me-2"></i> إعادة تعيين
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Cards -->
        <div class="row">
            @forelse($kasrSales as $index => $sale)
            <div class="col-lg-6 mb-4">
                <div class="card h-100 border-0 shadow-sm hover-shadow">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="fas fa-user-circle me-2"></i> {{ $sale->customer_name }}
                        </h5>
                        <span class="status-pill status-{{ $sale->status }}">
                            @if($sale->status == 'pending')
                                قيد الانتظار
                            @elseif($sale->status == 'accepted')
                                مقبول
                            @elseif($sale->status == 'rejected') 
                                مرفوض
                            @else
                                قيد الانتظار
                            @endif
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-7">
                                <div class="d-flex mb-2">
                                    <div class="text-muted" style="width: 120px;">
                                        <i class="fas fa-phone me-2"></i> رقم الهاتف:
                                    </div>
                                    <div class="fw-bold">{{ $sale->customer_phone ?? 'غير متوفر' }}</div>
                                </div>
                                <div class="d-flex mb-2">
                                    <div class="text-muted" style="width: 120px;">
                                        <i class="fas fa-calendar me-2"></i> تاريخ الطلب:
                                    </div>
                                    <div class="fw-bold">{{ $sale->order_date ? $sale->order_date->format('Y-m-d') : 'غير محدد' }}</div>
                                </div>
                                <div class="d-flex mb-2">
                                    <div class="text-muted" style="width: 120px;">
                                        <i class="fas fa-money-bill-alt me-2"></i> السعر:
                                    </div>
                                    <div class="fw-bold">{{ $sale->offered_price ? number_format($sale->offered_price, 2) . ' ج.م' : 'غير محدد' }}</div>
                                </div>
                                <div class="d-flex mb-2">
                                    <div class="text-muted" style="width: 120px;">
                                        <i class="fas fa-weight me-2"></i> الوزن الكلي:
                                    </div>
                                    <div class="fw-bold">{{ number_format($sale->getTotalWeight(), 2) }} جرام</div>
                                </div>
                                <div class="d-flex mb-2">
                                    <div class="text-muted" style="width: 120px;">
                                        <i class="fas fa-cubes me-2"></i> عدد القطع:
                                    </div>
                                    <div class="fw-bold">{{ $sale->items->count() }} قطعة</div>
                                </div>
                            </div>
                            <div class="col-md-5 text-center">
                                @if($sale->image_path)
                                    <img src="{{ asset('storage/' . $sale->image_path) }}" 
                                        class="img-thumbnail sale-thumbnail mb-2" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#imageModal" 
                                        data-img-src="{{ asset('storage/' . $sale->image_path) }}"
                                        alt="صورة الكسر">
                                @else
                                    <div class="no-image-placeholder">
                                        <i class="fas fa-image fa-4x text-muted"></i>
                                        <p class="small text-muted mt-2">لا توجد صورة</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Toggle Items Details -->
                        <div class="items-section">
                            <button class="btn btn-outline-secondary btn-sm w-100 toggle-items mb-3" data-sale-id="{{ $sale->id }}">
                                <i class="fas fa-chevron-down me-1 toggle-icon-{{ $sale->id }}"></i> عرض تفاصيل القطع
                            </button>
                            
                            <div class="items-details" id="items-{{ $sale->id }}" style="display:none;">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered border-light">
                                        <thead class="table-light">
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
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top-0 pt-0">
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('kasr-sales.show', $sale->id) }}" class="btn btn-info btn-sm me-2">
                                <i class="fas fa-eye me-1"></i> عرض
                            </a>
                            <a href="{{ route('kasr-sales.edit', $sale->id) }}" class="btn btn-warning btn-sm me-2">
                                <i class="fas fa-edit me-1"></i> تعديل
                            </a>
                            <form action="{{ route('kasr-sales.destroy', $sale->id) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash me-1"></i> حذف
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="alert alert-info text-center shadow-sm">
                    <i class="fas fa-info-circle fa-2x mb-3"></i>
                    <h4>لا توجد سجلات</h4>
                    <p class="mb-0">لم يتم العثور على أي سجلات لمشتريات الكسر. يمكنك إضافة سجل جديد من خلال الضغط على "إضافة كسر جديد".</p>
                    <a href="{{ route('kasr-sales.create') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-plus-circle me-1"></i> إضافة كسر جديد
                    </a>
                </div>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $kasrSales->appends(request()->query())->links() }}
        </div>
    </div>

    
    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">صورة الكسر</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="" class="img-fluid" id="modalImage" alt="صورة الكسر">
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">تأكيد الحذف</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-exclamation-triangle text-danger fa-3x"></i>
                    </div>
                    <p class="mb-0 text-center fs-5">هل أنت متأكد من حذف هذا العنصر؟ هذا الإجراء لا يمكن التراجع عنه.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">نعم، حذف</button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Scripts -->
    <script>
        $(document).ready(function() {
            // Image Modal
            $('#imageModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var imgSrc = button.data('img-src');
                var modal = $(this);
                modal.find('#modalImage').attr('src', imgSrc);
            });
            
            // Toggle item details
            $('.toggle-items').on('click', function() {
                var saleId = $(this).data('sale-id');
                var detailsRow = $('#items-' + saleId);
                var icon = $('.toggle-icon-' + saleId);
                
                detailsRow.slideToggle(300);
                
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