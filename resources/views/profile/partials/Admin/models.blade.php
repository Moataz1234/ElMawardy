<!DOCTYPE html>
<html>
    <head>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
<body>
    {{-- <form method="POST" action="{{ route('bulk-action') }}" id="bulkActionForm">
        @csrf
        <input type="hidden" name="action" value="delete"> --}}
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Model</th>
                    <th>SKU</th>
                    <th>Category</th>
                    <th> Serial Number</th>
                    <th> Weight</th>
                    <th>Avg Weight</th>
                </tr>
            </thead>
            <tbody>
                @foreach($models as $model)
                <tr>
                            <td>{{ $model->model }}</td>
                            <td>{{ $model->SKU }}</td>
                            <td>{{ $model->category }}</td>
                            {{-- <td>{{ $goldItem->serial_number }}</td> --}}
                            {{-- <td>{{ $goldItem->weight }}</td> --}}
                            <td>{{ $model->goldItemsAvg->stones_weight ?? 'N/A' }}</td>
                        </tr>

                @endforeach
            </tbody>
        </table>
    {{-- <div class="button-container">
        <button class="delete_btn"  type="button" name="action" value="delete" form="bulkActionForm">Delete </button>
        <!-- Rest of your form -->
    <button class="request_btn" type="submit" name="action" value="request">Request Item</button>
    </div> --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteForm = document.getElementById('bulkActionForm');
    const deleteBtn = deleteForm.querySelector('.delete_btn');
    
    console.log('DOM Content Loaded');
    console.log('Delete form found:', deleteForm);
    console.log('Delete button found:', deleteBtn);

    if (!deleteForm) {
    console.error('Delete form not found');
    return;
    }
    if (!deleteBtn) {
        console.error('Delete button not found!');
        return;
    }

    deleteBtn.addEventListener('click', function(e) {
        e.preventDefault();
        console.log('Delete button clicked');

        const selectedItems = Array.from(deleteForm.querySelectorAll('input[name="selected_items[]"]:checked'));
        console.log('Selected items count:', selectedItems.length);
            
        if (selectedItems.length === 0) {
            Swal.fire('Error', 'Please select items to delete', 'error');
            return;
        }

        const mappedItems = selectedItems.map(checkbox => {
            const row = checkbox.closest('tr');
            return {
                id: checkbox.value,
                serial: row.querySelector('td:nth-child(3)').textContent,
                model: row.querySelector('td:nth-child(6)').textContent
            };
        });

        Swal.fire({
            title: 'Confirm Deletion',
            html: `
                <p>Are you sure you want to delete these items?</p>
                <ul style="text-align: left;">${mappedItems.map(item => 
                    `<li>${item.serial} - ${item.model}</li>`
                ).join('')}</ul>
                <div class="form-group">
                    <label>Reason for deletion:</label>
                    <textarea id="deletion-reason" class="form-control"></textarea>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete them!'
        }).then((result) => {
            if (result.isConfirmed) {
                const reason = document.getElementById('deletion-reason').value;
                const reasonInput = document.createElement('input');
                reasonInput.type = 'hidden';
                reasonInput.name = 'deletion_reason';
                reasonInput.value = reason;
                deleteForm.appendChild(reasonInput);

                console.log('Submitting delete form...');
                deleteForm.submit();
            }
        });
    });
});
</script>
</body>
</html>
