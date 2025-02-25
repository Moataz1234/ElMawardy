<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> طلبات الاضافة</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ url('css/addRequests.css') }}" rel="stylesheet">

</head>

<body>
    @include('components.navbar')
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
        <div class="container mt-4">
            <form action="{{ route('admin.add.requests') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="status" class="form-label">Filter by Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">-- Select Status --</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted
                        </option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="shop_name" class="form-label">Filter by Shop Name</label>
                    <select name="shop_name" id="shop_name" class="form-select">
                        <option value="">-- Select Shop --</option>
                        @foreach ($shops as $shop)
                            <option value="{{ $shop }}" {{ request('shop_name') == $shop ? 'selected' : '' }}>
                                {{ $shop }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </form>
        </div>

        <table class="table table-bordered  mt-4">
            <thead>
                <tr>
                    <th>Model</th>
                    <th>Serial Number</th>
                    <th>Shop Name</th>
                    <th>Kind</th>
                    <th>Weight</th>
                    <th>Stars</th>
                    <th>Gold Color</th>
                    <th>Created At</th>
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
                <strong class="total_items badge bg-danger col-4 fs-6">Total Items: <span
                        class="fs-6 ">{{ $totalItems }}</span> </strong>
                <strong class="total_weight badge bg-warning col-4 fs-6">Total Weight: <span
                        class="fs-6 ">{{ $totalWeight }}</span></strong>
            </div>
        </table>

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
