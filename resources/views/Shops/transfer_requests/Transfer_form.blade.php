<!DOCTYPE html>
<html lang="en">
<head>
    @include('dashboard')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Gold Item</title>
    {{-- <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ url('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/navbar.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet"> --}}
    <style>
        /* Container for the whole view */
        .transfer-container {
            width: 750px;
            margin:20px 350px ;
            background-color: #0D3B66;
            color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            font-family: Arial, sans-serif;
            
        }
        
        /* Section for shop selection */
        .shop-select-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            background-color: #e2e1e0;
            border-radius: 20px;
        }
        
        .shop-select-container div {
            width: 60%;
        }
        
        select {
            width: 100%;
            padding: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
            color: #fafafa;
        }

        /* Code label styling */
        .code-label {
            font-size: 1.2em;
            font-weight: bold;
            margin-bottom: 10px;
            color:#0D3B66;
        }
        
        /* Table for item details */
        .details-table {
            width: 100%;
            border-spacing: 0;
            margin-bottom: 15px;
        }
        
        .details-table th, .details-table td {
            padding: 10px;
            background-color: #e0e0e0;
            text-align: left;
            color: #333;
            font-weight: bold;
        }
        
        .details-table th {
            background-color: #0D3B66;
            color: white;
        }
        
        /* Button styling */
        .transfer-button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-align: center;
        }

        .transfer-button:hover {
            background-color: #218838;
        }
        .header{
        }
        .shop_label{

        }
        #shop_name{
            background-color: #0D3B66
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
    
            <button type="submit" class="transfer-button">Transfer Items</button>
        </form>
    </div>
      <script>
  
    </script>
</body>
</html>