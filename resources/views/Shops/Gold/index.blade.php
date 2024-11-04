
<!DOCTYPE html>
<html lang="en">
<head>
    @include('dashboard')
    {{-- <link rel="stylesheet" href="{{ asset('CSS/first_page.css') }}"> --}}

   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Your Shop's Items</title>
   <link href="{{ asset('css/app.css') }}" rel="stylesheet">
   <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">   
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOM8kNq7/8z2zVw5U5NAuTp6WVsMSXJ1pO9aX1l" crossorigin="anonymous">
   <link href="{{ asset('css/first_page.css') }}" rel="stylesheet">
   {{-- <link href="{{ asset('css/style.css') }}" rel="stylesheet"> --}}
   <link href="{{ asset('css/pagination.css') }}" rel="stylesheet">

</head>
<body>
      <!-- Button to toggle the price table -->
      {{-- <button id="togglePriceTable" class="prices" >Show Updates</button> --}}

      <!-- Hidden price table section -->
      
      <div id="priceTable" style="display: none;">
          <table>
              <tbody>
                  @foreach ($latestPrices as $price)
                      <tr>
                          <td>{{ $price->gold_buy }}/{{ $price->gold_sell }}</td>
                          <td>{{ $price->gold_with_work }}</td>
                          <td>{{ $price->dollar_price }}</td>
                          <td>{{ $price->percent }}</td>
                          <td>{{ $price->gold_in_diamond }}</td>
                          <td>{{ $price->shoghl_agnaby }}</td>
                      </tr>
                  @endforeach
              </tbody>
          </table>
      </div>
  
      <div class="spreadsheet">
       <table>
           <thead>
               <tr>
                <th>Select</th>
                   <th>Image</th>
                   <th>
                           Serial Number
                   </th>
                   <th>
                           Shop Name
                   </th>
                   <th>
                           Kind
                   </th>
                   <th>
                           Model
                   </th>
                   <th>
                           Gold Color
                   </th>
                   {{-- <th>
                           Metal Purity
                   </th> --}}
                   <th>
                           Weight
                   </th>
                   <th>Actions</th>
               </tr>
           </thead>
       {{-- <tbody> --}}
        <tbody id="table-body">   
                 
        @foreach ($goldItems as $item)
           @php
           $isOuter = \App\Models\Outer::where('gold_serial_number', $item->serial_number)
                                       ->where('is_returned', false)
                                       ->exists();
            @endphp
        <tr style="{{ $isOuter ? 'background-color: yellow;' : '' }}">

            <td>
                <input type="checkbox" class="select-item" data-id="{{ $item->id }}">
            </td>
                <td>
                       @if($item->link)
                           <img src="{{ asset( $item->link) }}" alt="Image" width="50">
                       @else
                           No Image
                       @endif
                   </td>
                   <td>{{ $item->serial_number }}</td>
                   <td>{{ $item->shop->name }}</td>
                   <td>{{ $item->kind }}</td>
                   <td>{{ $item->model }}</td>
                   <td>{{ $item->gold_color }}</td>
                   {{-- <td>{{ $item->stones }}</td> --}}
                   {{-- <td>{{ $item->metal_type }}</td> --}}
                   {{-- <td>{{ $item->metal_purity }}</td> --}}
                   {{-- <td>{{ $item->quantity }}</td> --}}
                   <td>{{ $item->weight }}</td>
                   {{-- <td>{{ $item->source }}</td> --}}
                   {{-- <td>{{ $item->average_of_stones }}</td> --}}
                   {{-- <td>{{ $item->net_weight }}</td> --}}
                   <td>
                    <a class="action_button" href="{{ route('shop-items.edit', $item->id) }}" 
                        {{ $isOuter ? 'style=pointer-events:none;opacity:0.5;' : '' }}>
                        Sell
                     </a>
                     <a class="action_button" href="{{ route('gold-items.transferForm', $item->id) }}" 
                        {{ $isOuter ? 'style=pointer-events:none;opacity:0.5;' : '' }}>
                        Transfer
                     </a>
                     @include('Shops.Gold.outerForm') <!-- Include the form view -->

                    </div>
                    @if ($isOuter)
                    <form action="{{ route('gold-items.returnOuter', $item->serial_number) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="returned-btn">Returned</button>
                    </form>
                @else
                <form action="{{ route('gold-items.toggleReturn', $item->serial_number) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="outer-btn">
                        Outer
                    </button>
                </form>
                @endif
            
                   </td>
               </tr>
           @endforeach
       </tbody>
   </table>
   <div class="button-container">
           
    <button id="sellItemsButton" class="image-button" onclick="addImage(event)">Sell</button> 
    
    <button id="transferItemsButton" class="image-button" onclick="addImage(event)">Transfer</button>
</div>
</div>
   {{ $goldItems->links('pagination::bootstrap-4') }}

   <script>
document.addEventListener('DOMContentLoaded', function() {
    // Retrieve selected IDs from local storage
    let selectedIds = JSON.parse(localStorage.getItem('selectedItems')) || [];

    // Check the checkboxes based on local storage
    document.querySelectorAll('.select-item').forEach(checkbox => {
        const itemId = parseInt(checkbox.dataset.id);

        if (selectedIds.includes(itemId)) {
            checkbox.checked = true; // Mark as checked if ID is in local storage
        }

        // Update selected IDs in local storage when checkbox state changes
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                // Add ID if checkbox is checked
                if (!selectedIds.includes(itemId)) {
                    selectedIds.push(itemId);
                }
            } else {
                // Remove ID if checkbox is unchecked
                selectedIds = selectedIds.filter(id => id !== itemId);
            }

            // Save the updated array to local storage
            localStorage.setItem('selectedItems', JSON.stringify(selectedIds));
        });
    });

    let selectedItems = [...selectedIds]; // Initialize with any pre-selected items

    // Handle the Sell button click
    document.getElementById('sellItemsButton').addEventListener('click', function() {
        if (selectedItems.length === 0) {
            alert('Please select at least one item to sell.');
            return;
        }

        // Redirect to the sell form with selected item IDs
        const url = "{{ route('shop-items.bulkSellForm') }}?ids=" + selectedItems.join(',');
        window.location.href = url;

        // Clear checkboxes and local storage after initiating sell
        clearSelections();
    });

    // Handle the Transfer button click
    document.getElementById('transferItemsButton').addEventListener('click', function() {
        if (selectedItems.length === 0) {
            alert('Please select at least one item to transfer.');
            return;
        }

        // Redirect to the transfer form with selected item IDs
        const url = "{{ route('shop-items.bulkTransferForm') }}?ids=" + selectedItems.join(',');
        window.location.href = url;

        // Clear checkboxes and local storage after initiating transfer
        clearSelections();
    });

    // Function to clear selections and update local storage
    function clearSelections() {
        document.querySelectorAll('.select-item').forEach(checkbox => {
            checkbox.checked = false;
        });
        selectedItems = [];
        selectedIds = [];
        localStorage.removeItem('selectedItems');
    }
});
  function openOuterForm(serialNumber) {
    document.getElementById('gold_serial_number').value = serialNumber;
    document.getElementById('outerFormModal').style.display = 'block';
}
function closeOuterForm() {
    document.getElementById('outerFormModal').style.display = 'none';
}
function confirmReturn() {
    // Optional: Add confirmation dialog if you want to confirm the return action
    if (confirm('Are you sure you want to mark this item as returned?')) {
        document.querySelector('#returnOuterForm').submit(); // Submit the return form
    }
}
    // JavaScript to toggle the visibility of the price table
    document.getElementById('togglePriceTable').addEventListener('click', function() {
        const priceTable = document.getElementById('priceTable');
        if (priceTable.style.display === 'none') {
            priceTable.style.display = 'block';
            this.innerText = 'Hide Prices';
        } else {
            priceTable.style.display = 'none';
            this.innerText = 'Show Prices';
        }
    });
</script>
</body>
</html>
