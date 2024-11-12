<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Gold Prices</title>
    <style>
        .modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    justify-content: center;
    align-items: center;
}

.modal-content {
    background-color: white;
    padding: 20px;
    border-radius: 5px;
    max-width: 500px;
    width: 100%;
}
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f0f0f0;
        }
        form {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            margin: auto;
        }
        label, input, button {
            display: block;
            width: 100%;
            margin-bottom: 10px;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover{
            background-color: #48ff00;
        }
        /* Media query for mobile responsiveness */
        @media screen and (max-width: 768px) {
            body {
                padding: 10px;
            }
            form {
                width: 90%;
                max-width: 100%;
                margin: 20px auto;
            }
            button {
                padding: 12px;
            }
        }
    </style>
    
</head>
<body>
    {{-- <form  method="POST" action="{{ route('shopify.update-specific-products') }}">
        @csrf
 
        <button type="submit" >Update Shopify Prices</button>
    </form> --}}
    <h1>Update Gold Prices</h1>
    <form id="goldPriceForm" method="POST" action="{{ route('gold_prices.store') }}">
        @csrf
        <label>Gold Buy:</label>
        <input type="number" step="0.01" name="gold_buy" id="gold_buy" 
        value="{{ old('gold_buy', $latestGoldPrice->gold_buy ?? '') }}" required>

        <label>Gold Sell:</label>
        <input type="number" step="0.01" name="gold_sell" id="gold_sell"
        value="{{ old('gold_sell', $latestGoldPrice->gold_sell ?? '') }}" required>

        <label>Percent:</label>
        <input type="number" step="0.01" name="percent" id="percent"
        value="{{ old('percent', $latestGoldPrice->percent ?? '') }}" required>

        <label>Dollar Price:</label>
        <input type="number" step="0.01" name="dollar_price" id="dollar_price"
        value="{{ old('dollar_price', $latestGoldPrice->dollar_price ?? '') }}" required>

        <label>Gold with Work:</label>
        <input type="number" step="0.01" name="gold_with_work" id="gold_with_work"
        value="{{ old('gold_with_work', $latestGoldPrice->gold_with_work ?? '') }}" required>

        <label>Gold in Diamond:</label>
        <input type="number" step="0.01" name="gold_in_diamond" id="gold_in_diamond" 
        value="{{ old('gold_in_diamond', $latestGoldPrice->gold_in_diamond ?? '') }}" required>

        <label>Shoghl Ajnaby:</label>
        <input type="number" step="0.01" name="shoghl_agnaby" id="shoghl_agnaby"
        value="{{ old('shoghl_agnaby', $latestGoldPrice->shoghl_agnaby ?? '') }}" required>

        <button type="button" id="previewButton">Update Prices</button>
   
    <div id="confirmationModal" class="modal" style="display: none;">
        <div class="modal-content">
            <h2>Confirm Your Submission</h2>
            <p><strong>Gold Buy:</strong> <span id="confirm_gold_buy"></span></p>
            <p><strong>Gold Sell:</strong> <span id="confirm_gold_sell"></span></p>
            <p><strong>Percent:</strong> <span id="confirm_percent"></span></p>
            <p><strong>Dollar Price:</strong> <span id="confirm_dollar_price"></span></p>
            <p><strong>Gold with Work:</strong> <span id="confirm_gold_with_work"></span></p>
            <p><strong>Gold in Diamond:</strong> <span id="confirm_gold_in_diamond"></span></p>
            <p><strong>Shoghl Ajnaby:</strong> <span id="confirm_shoghl_agnaby"></span></p>
    
            <button id="confirmSubmit">Confirm</button>
            <button id="cancelButton" >Cancel</button>
        </div>
    </div>
</form>
   <script>
  
  document.addEventListener('DOMContentLoaded', function () {
            const previewButton = document.getElementById('previewButton');
            const confirmSubmit = document.getElementById('confirmSubmit');
            const cancelButton = document.getElementById('cancelButton');
            const confirmationModal = document.getElementById('confirmationModal');
            const goldPriceForm = document.getElementById('goldPriceForm');

            // Show the modal with filled values on "Preview" button click
            previewButton.addEventListener('click', function(event) {
                event.preventDefault(); // Prevent form submission
                // Populate modal with current input values
                document.getElementById('confirm_gold_buy').textContent = document.getElementById('gold_buy').value;
                document.getElementById('confirm_gold_sell').textContent = document.getElementById('gold_sell').value;
                document.getElementById('confirm_percent').textContent = document.getElementById('percent').value;
                document.getElementById('confirm_dollar_price').textContent = document.getElementById('dollar_price').value;
                document.getElementById('confirm_gold_with_work').textContent = document.getElementById('gold_with_work').value;
                document.getElementById('confirm_gold_in_diamond').textContent = document.getElementById('gold_in_diamond').value;
                document.getElementById('confirm_shoghl_agnaby').textContent = document.getElementById('shoghl_agnaby').value;
                // Show the modal
                confirmationModal.style.display = 'flex';
            });

            // Submit the form when "Confirm" button is clicked
            confirmSubmit.addEventListener('click', function(event) {
                event.preventDefault(); // Prevent accidental additional submissions
                confirmationModal.style.display = 'none';
                goldPriceForm.submit(); // Submit the form
            });

            // Close the modal without submitting when "Cancel" button is clicked
            cancelButton.addEventListener('click', function(event) {
                event.preventDefault(); // Prevent form submission
                confirmationModal.style.display = 'none'; // Hide the modal
            });
        }); </script>
</body>
</html>
