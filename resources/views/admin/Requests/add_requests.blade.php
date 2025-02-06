<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> طلبات الاضافة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    @include('components.navbar')
    <div class="container mt-5">
        <h1 class="text-center mb-4">طلبات الاضافة</h1>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
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

        <form id="bulk-action-form" action="{{ route('add-requests.bulk-action') }}" method="POST">
            @csrf
            <input type="hidden" name="action" value="accept">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all"></th>
                        <th>Model</th>
                        <th>Serial Number</th>
                        <th>Kind</th>
                        <th>Weight</th>
                        <th>Gold Color</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalWeight = 0;
                        $totalItems = count($requests);
                    @endphp
                    @foreach ($requests as $request)
                        <tr>
                            <td>
                                <input type="checkbox" name="selected_requests[]" value="{{ $request->id }}">
                            </td>
                            <td>{{ $request->model }}</td>
                            <td>{{ $request->serial_number }}</td>
                            <td>{{ $request->kind }}</td>
                            <td>{{ $request->weight }}</td>
                            <td>{{ $request->gold_color }}</td>
                            <td>{{ $request->status }}</td>

                        </tr>
                        @php
                            $totalWeight += $request->weight;
                        @endphp
                    @endforeach
                    <strong class="badge bg-black ">Total Items:</strong> <span>{{ $totalItems }}</span> 
                    <strong class="badge bg-warning ">Total Weight:</strong> <span>{{ $totalWeight }}</span> 
                </tbody>
            </table>

            <div class="mt-3">
                <button type="button" id="accept-selected" class="btn btn-success">Accept Selected</button>
            </div>
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
