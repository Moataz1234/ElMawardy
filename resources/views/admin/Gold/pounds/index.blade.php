{{-- resources/views/pounds/index.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Shop Pounds Management</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
   @include('components.navbar')
</head>
<body>
    <div class="container mt-4">
        <h2>Shop Pounds Management</h2>

        <!-- Add New Pounds Form -->
        {{-- <div class="card mb-4">
            <div class="card-body">
                <h3>Add New Pounds</h3>
                
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('gold-pounds.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="pound_type" class="form-label">Pound Type</label>
                        <select name="pound_type" id="pound_type" class="form-select">
                            @foreach($poundTypes as $type)
                                <option value="{{ $type->kind }}">
                                    {{ ucfirst(str_replace('_', ' ', $type->kind)) }} - {{ $type->weight }}g
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" name="quantity" id="quantity" min="1" value="1" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-primary">Add Pounds</button>
                </form>
            </div>
        </div> --}}

        <!-- Current Inventory Table -->
        <div class="card">
            <div class="card-body">
                <h3>Current Inventory</h3>
                <form id="sellForm" action="{{ route('gold-pounds.create-sale-request') }}" method="POST">
                    @csrf
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Select</th>
                                    <th>Serial Number</th>
                                    <th>Type</th>
                                    <th>Weight (g)</th>
                                    <th>Linked Item</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($shopPounds as $pound)
                                    <tr>
                                        <td>
                                            <input type="checkbox" 
                                                name="selected_pounds[]" 
                                                value="{{ $pound->serial_number }}"
                                                class="pound-checkbox"
                                                data-serial="{{ $pound->serial_number }}">
                                        </td>
                                        <td>{{ $pound->serial_number }}</td>
                                        <td>{{ $pound->goldPound ? ucfirst(str_replace('_', ' ', $pound->goldPound->kind)) : 'N/A' }}</td>
                                        <td>{{ $pound->goldPound ? $pound->goldPound->weight : 'N/A' }}</td>
                                        <td>
                                            {{ $pound->goldItem ? 'Yes' : 'No' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <button type="button" id="sellSelectedBtn" class="btn btn-primary" disabled>
                        Sell Selected Pounds
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.pound-checkbox');
            const sellButton = document.getElementById('sellSelectedBtn');
            
            function updateSellButton() {
                const checkedBoxes = document.querySelectorAll('.pound-checkbox:checked');
                sellButton.disabled = checkedBoxes.length === 0;
                
                // Debug: Log selected items
                console.log('Selected items:', Array.from(checkedBoxes).map(cb => cb.dataset.serial));
            }

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateSellButton();
                    // Debug: Log when checkbox changes
                    console.log('Checkbox changed:', this.dataset.serial, 'Checked:', this.checked);
                });
            });

            sellButton.addEventListener('click', function() {
                const selectedPounds = Array.from(document.querySelectorAll('.pound-checkbox:checked'))
                    .map(cb => cb.dataset.serial);
                
                // Debug: Log selected pounds before redirect
                console.log('Selected pounds before redirect:', selectedPounds);
                
                if (selectedPounds.length > 0) {
                    const params = new URLSearchParams();
                    selectedPounds.forEach(serialNumber => {
                        params.append('selected_pounds[]', serialNumber);
                    });
                    
                    const url = '{{ route("gold-pounds.sell-form") }}?' + params.toString();
                    // Debug: Log the final URL
                    console.log('Redirect URL:', url);
                    
                    window.location.href = url;
                }
            });
        });
    </script>
</body>
</html>