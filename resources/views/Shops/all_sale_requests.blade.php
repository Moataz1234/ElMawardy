<div class="container mt-4">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <table class="table table-striped table-hover">
        <thead class="thead-dark">
            <tr>
                <th>Serial Number</th>
                <th>Shop Name</th>
                <th>Status</th>
                <th>Approver</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($soldItemRequests as $request)
            <tr>
                <td>
                    <a href="#" class="item-details text-primary" data-serial="{{ $request->item_serial_number }}">
                        {{ $request->item_serial_number }}
                    </a>
                </td>
                <td>{{ $request->shop_name }}</td>
                <td>
                    <span class="badge badge-{{ $request->status === 'approved' ? 'success' : ($request->status === 'rejected' ? 'danger' : 'warning') }}">
                        {{ $request->status }}
                    </span>
                </td>
                <td>{{ $request->approver_shop_name }}</td>
                <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Modal -->
    <div class="modal fade" id="itemModal" tabindex="-1" role="dialog" aria-labelledby="itemModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="itemModalLabel">Item Details</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="itemDetails" class="p-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.item-details').click(function(e) {
        e.preventDefault();
        let serial = $(this).data('serial');
        
        $.ajax({
            url: `/item-details/${serial}`,
            method: 'GET',
            success: function(data) {
                let details = `
                    <div class="row mb-2">
                        <div class="col-5 font-weight-bold">Serial Number:</div>
                        <div class="col-7">${data.serial_number}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 font-weight-bold">Kind:</div>
                        <div class="col-7">${data.kind}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 font-weight-bold">Model:</div>
                        <div class="col-7">${data.model}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 font-weight-bold">Gold Color:</div>
                        <div class="col-7">${data.gold_color}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 font-weight-bold">Weight:</div>
                        <div class="col-7">${data.weight}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 font-weight-bold">Price:</div>
                        <div class="col-7">${data.price}</div>
                    </div>
                `;
                $('#itemDetails').html(details);
                $('#itemModal').modal('show');
            }
        });
    });
});
</script>
