<!DOCTYPE html>
<html lang="en">
<head>
    @include('components.navbar')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Gold Item</title>
    <link href="{{ asset('css/transferForm.css') }}" rel="stylesheet">
    <style>
        /* Add styles for disabled button */
        .transfer-button:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
            opacity: 0.7;
        }
    </style>
</head>
<body>
    <div class="transfer-container">
        <form action="{{ route('gold-items.bulk-transfer') }}" method="POST">
            @csrf
            <div class="shop-select-container">
                <div >
                    <label class="code-label">From Shop:</label>
                        <input type="text" value="{{ Auth::user()->shop_name }}" disabled 
                               style="width: 100%;  background-color:#0D3B66 ; color:#ffffff">
                </div>

                <div >
                    <label class="code-label">To Shop:</label>
                    <select name="shop_name" id="shop_name" required>
                        <option value="">Select Shop</option>
                        @foreach($shops as $shopName)
                            <option value="{{ $shopName }}">{{ $shopName }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
    
            <div class="selected-items">
                <label class="code-label">Selected Items:</label>
                <table class="details-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Serial Number</th>
                            <th>Model</th>
                            <th>Kind</th>
                            <th>Weight</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($goldItems as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    {{ $item->serial_number }}
                                    <input type="hidden" name="item_ids[]" value="{{ $item->id }}">
                                </td>
                                <td>{{ $item->model }}</td>
                                <td>{{ $item->kind }}</td>
                                <td>{{ $item->weight }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <button type="submit" class="transfer-button" id="submitButton">Transfer Items</button>
        </form>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const transferForm = document.querySelector('form[action="{{ route("gold-items.bulk-transfer") }}"]');
            const submitButton = document.getElementById('submitButton');
    
            transferForm.addEventListener('submit', function(event) {
                event.preventDefault();
                
                // Disable the button immediately
                submitButton.disabled = true;
                submitButton.textContent = 'Processing...';

                // Submit the form using fetch
                fetch(transferForm.action, {
                    method: 'POST',
                    body: new FormData(transferForm),
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => {
                    if (response.ok) {
                        localStorage.removeItem('selectedItems');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Items transferred successfully!',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.href = '{{ route("dashboard") }}';
                        });
                    } else {
                        throw new Error('Form submission failed');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while submitting the form.',
                    });
                    // Re-enable the button on error
                    submitButton.disabled = false;
                    submitButton.textContent = 'Transfer Items';
                });
            });
        });
    </script>
</body>
</html>