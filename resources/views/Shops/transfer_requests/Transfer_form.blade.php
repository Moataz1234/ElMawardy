<!DOCTYPE html>
<html lang="en">
<head>
    @include('components.navbar')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Gold Item</title>
    <link href="{{ asset('css/transferForm.css') }}" rel="stylesheet">
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
            <button type="submit" class="transfer-button">Transfer Items</button>
        </form>
    </div>
</body>
</html>