<!DOCTYPE html>
<html>
    <head>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
<body>
    <form method="POST" action="{{ route('bulk-action') }}" id="bulkActionForm">
        @csrf
        <input type="hidden" name="action" value="delete">
    <table class="table">
        <thead>
            <tr>
                <th><input type="checkbox" id="select-all" /></th>
                <th>Image</th>
                    @php
                    // Array of columns with their display names
                    $columns = [
                        'serial_number' => 'Serial Number',
                        'shop_name' => 'Shop Name',
                        'kind' => 'Kind',
                        'model' => 'Model',
                        'gold_color' => 'Gold Color',
                        // 'stones' => 'Stones',
                        // 'metal_type' => 'Metal Type',
                        'metal_purity' => 'Metal Purity',
                        // 'quantity' => 'Quantity',
                        'weight' => 'Weight',
                        'category' =>'Category',
                        // 'source' => 'Source',
                        // 'average_of_stones' => 'Average of Stones',
                        // 'net_weight' => 'Net Weight',
                    ];
                @endphp

                @foreach ($columns as $field => $label)
                    <th>
                            {{ $label }}
                    </th>
                @endforeach
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($goldItems as $item)
                <tr>
                    <td><input type="checkbox" name="selected_items[]" value="{{ $item->id }}" /></td>
                    <td>   @if($item->modelCategory)
                        @if($item->modelCategory->scanned_image)
                            <img src="{{ asset( $item->modelCategory->scanned_image) }}" alt="Scanned Image" width="50">
                        @endif
                    @else
                        No matching model found
                    @endif
                    </td>
                    <td>{{ $item->serial_number }}</td>
                    <td>{{ $item->shop_name ?? 'Admin' }}</td>
                    <td>{{ $item->kind }}</td>
                    <td>{{ $item->model }}</td>
                    <td>{{ $item->gold_color }}</td>
                    {{-- <td>{{ $item->stones }}</td> --}}
                    {{-- <td>{{ $item->metal_type }}</td> --}}
                    <td>{{ $item->metal_purity }}</td>
                    {{-- <td>{{ $item->quantity }}</td> --}}
                    <td>{{ $item->weight }}</td>
                    <td>{{ $item->modelCategory->category ?? 'No Category' }}</td>
                    <td>
                        <a class="action_button" href="{{ route('gold-items.edit', $item->id) }}">Edit</a>
                    </td>
                    {{-- <td>{{ $item->source }}</td> --}}
                    {{-- <td>{{ $item->average_of_stones }}</td> --}}
                    {{-- <td>{{ $item->net_weight }}</td> --}}
                    {{-- <td>
                        <a class="action_button" href="{{ route('gold-items.edit', $item->id) }}" >Edit</a> --}}
                </tr>
            @endforeach 
        </tbody>
    </table>
    <div class="button-container">
        <button class="delete_btn"  type="button" name="action" value="delete" form="bulkActionForm">Delete </button>
        <!-- Rest of your form -->
    <button class="request_btn" type="submit" name="action" value="request">Request Item</button>
    </div>
</form>
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
