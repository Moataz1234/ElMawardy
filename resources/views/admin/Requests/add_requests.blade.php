<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> طلبات الاضافة</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ url('css/addRequests.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

</head>

<body>
    @include('components.navbar')
    <div class="container mt-5">
        <h1 class="text-center mb-4">Add Requests</h1>

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

        <div class="filters-container mt-4">
            <form action="{{ route('admin.add.requests') }}" method="GET" class="row align-items-end g-3">
                <div class="col">
                    <label for="status" class="form-label">Request Status</label>
                    <select name="status" id="status" class="form-select custom-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                    </select>
                </div>

                <div class="col">
                    <label for="shop_name" class="form-label">Shop Name</label>
                    <select name="shop_name" id="shop_name" class="form-select custom-select">
                        <option value="">All Shops</option>
                        @foreach ($shops as $shop)
                            <option value="{{ $shop }}" {{ request('shop_name') == $shop ? 'selected' : '' }}>
                                {{ $shop }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" class="form-control custom-date" id="date" name="date" value="{{ request('date', date('Y-m-d')) }}">
                </div>

                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <button type="reset" class="btn btn-secondary">Reset</button>
                </div>
            </form>
        </div>

        <div class="table-responsive mt-4">
            <table class="table table-hover table-striped border">
                <thead class="table-dark">
                    <tr>
                        <th>Model</th>
                        <th>Serial Number</th>
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
                    </tr>
                </thead>
                <tbody class="">
                    @php
                        $totalWeight = 0;
                        $totalItems = count($requests);
                    @endphp
                    @forelse ($requests as $request)
                        <tr>
                            <td>{{ $request->model }}</td>
                            <td>{{ $request->serial_number }}</td>
                            <td>{{ $request->shop_name }}</td>
                            <td>{{ $request->kind }}</td>
                            <td>{{ $request->weight }}</td>
                            <td>{{ $request->stars }}</td>
                            <td>{{ $request->gold_color }}</td>
                            <td>{{ $request->created_at->format('Y-m-d') }}</td>
                            <td>{{ $request->status }}</td>
                        </tr>
                        @php
                            $totalWeight += $request->weight;
                        @endphp
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
                <div class="d-flex justify-content-between mt-4">
                    <div class="total_items badge">Total Items: <span>{{ $totalItems }}</span></div>
                    <div class="total_weight badge">Total Weight: <span>{{ $totalWeight }}</span></div>
                </div>
            </table>
        </div>

        {{-- <div class="mt-3">
                <button type="button" id="accept-selected" class="btn btn-success">Accept Selected</button>
            </div> --}}
        </form>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">تأكيد الاضافة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>
                <div class="modal-body">
                    هل أنت متأكد أنك تريد إضافة القطع المحددة إلى الجرد؟
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" id="confirm-accept" class="btn btn-success">تأكيد القبول</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Select All checkbox functionality
        document.getElementById('select-all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('input[name="selected_requests[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Show confirmation modal when "Accept Selected" is clicked
        document.getElementById('accept-selected').addEventListener('click', function() {
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

        // Handle confirmation
        document.getElementById('confirm-accept').addEventListener('click', function() {
            const modal = bootstrap.Modal.getInstance(document.getElementById('confirmationModal'));
            modal.hide();
            document.getElementById('bulk-action-form').submit();
        });
    </script>
</body>

</html>
