<!-- resources/views/Rabea/transfer_form.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    @include('components.navbar')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Rabea - Transfer Items</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .transfer-container {
            max-width: 1000px;
            margin: 30px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            border-radius: 8px 8px 0 0 !important;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 30px;
        }
        .details-table th, .details-table td {
            border: 1px solid #dee2e6;
            padding: 10px;
            text-align: left;
        }
        .details-table th {
            background-color: #f8f9fa;
            font-weight: 500;
        }
        .shop-select-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            gap: 20px;
        }
        .shop-select-container > div {
            flex: 1;
        }
        .code-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #495057;
        }
        .transfer-button {
            background-color: #3490dc;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.2s;
            display: block;
            margin-left: auto;
        }
        .transfer-button:hover {
            background-color: #2779bd;
        }
        .transfer-button:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
            opacity: 0.7;
        }
        select, input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="transfer-container">
            <div class="card-header bg-primary text-white mb-4 p-3">
                <h4><i class="bi bi-arrow-right-circle me-2"></i>Transfer Items from Rabea</h4>
            </div>
            
            <div class="alert alert-info mb-4">
                <i class="bi bi-info-circle me-2"></i>
                Please select the destination shop for the selected items
            </div>
            
            <form action="{{ route('rabea.process.transfer') }}" method="POST" id="rabeaTransferForm">
                @csrf
                <div class="shop-select-container">
                    <div>
                        <label class="code-label">From Shop:</label>
                        <input type="text" value="Rabea" disabled 
                               style="background-color:#0D3B66; color:#ffffff">
                    </div>

                    <div>
                        <label class="code-label">To Shop:</label>
                        <select name="shop_name" id="shop_name" required>
                            <option value="">Select Destination Shop</option>
                            @foreach($shops as $shopName)
                                <option value="{{ $shopName }}">{{ $shopName }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
        
                <div class="selected-items">
                    <label class="code-label">Selected Items ({{ $goldItems->count() }}):</label>
                    <table class="details-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Serial Number</th>
                                <th>Model</th>
                                <th>Kind</th>
                                <th>Gold Color</th>
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
                                    <td>
                                        @php
                                            $colorClass = '';
                                            if ($item->gold_color === 'Yellow') $colorClass = 'text-warning';
                                            elseif ($item->gold_color === 'White') $colorClass = 'text-secondary';
                                            elseif ($item->gold_color === 'Rose') $colorClass = 'text-danger';
                                        @endphp
                                        <span class="{{ $colorClass }}"><strong>{{ $item->gold_color }}</strong></span>
                                    </td>
                                    <td>{{ $item->weight }} g</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('rabea.items') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back to Items
                    </a>
                    <button type="submit" class="btn btn-primary transfer-button" id="submitButton">
                        <i class="bi bi-send me-1"></i> Transfer Items
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const transferForm = document.getElementById('rabeaTransferForm');
            const submitButton = document.getElementById('submitButton');
    
            transferForm.addEventListener('submit', function(event) {
                event.preventDefault();
                
                const shopSelect = document.getElementById('shop_name');
                if (!shopSelect.value) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Please select a destination shop'
                    });
                    return;
                }
                
                // Disable the button immediately
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Processing...';

                // Submit the form using fetch
                fetch(transferForm.action, {
                    method: 'POST',
                    body: new FormData(transferForm),
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.href = '{{ route("rabea.items") }}';
                        });
                    } else {
                        throw new Error(data.message || 'Error processing transfer');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'An error occurred while processing the transfer.',
                    });
                    // Re-enable the button on error
                    submitButton.disabled = false;
                    submitButton.innerHTML = '<i class="bi bi-send me-1"></i> Transfer Items';
                });
            });
        });
    </script>
</body>
</html>