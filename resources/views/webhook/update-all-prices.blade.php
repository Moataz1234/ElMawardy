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
    <h1>Update Gold Prices</h1>
    <form id="goldPriceForm" method="POST" action="{{ route('prices.update') }}">
        @csrf
        <label>Gold Buy:</label>
        <input type="number" step="0.01" name="gold_buy" id="gold_buy" required>

        <label>Gold Sell:</label>
        <input type="number" step="0.01" name="gold_sell" id="gold_sell" required>

        <label>Percent:</label>
        <input type="number" step="0.01" name="percent" id="percent" required>

        <label>Dollar Price:</label>
        <input type="number" step="0.01" name="dollar_price" id="dollar_price" required>

        <label>Gold with Work:</label>
        <input type="number" step="0.01" name="gold_with_work" id="gold_with_work" required>

        <label>Gold in Diamond:</label>
        <input type="number" step="0.01" name="gold_in_diamond" id="gold_in_diamond" required>

        <label>Shoghl Ajnaby:</label>
        <input type="number" step="0.01" name="shoghl_agnaby" id="shoghl_agnaby" required>

        <button type="submit" id="previewButton">Update Prices</button>
    </form>
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
            <button id="cancelSubmit">Cancel</button>
        </div>
    </div>
    <script>
document.getElementById('previewButton').addEventListener('click', function(event) {
    // Get the values from the form
    const goldBuy = document.getElementById('gold_buy').value;
    const goldSell = document.getElementById('gold_sell').value;
    const percent = document.getElementById('percent').value;
    const dollarPrice = document.getElementById('dollar_price').value;
    const goldWithWork = document.getElementById('gold_with_work').value;
    const goldInDiamond = document.getElementById('gold_in_diamond').value;
    const shoghlAjnaby = document.getElementById('shoghl_agnaby').value;

    // Populate the modal with the form data
    document.getElementById('confirm_gold_buy').innerText = goldBuy;
    document.getElementById('confirm_gold_sell').innerText = goldSell;
    document.getElementById('confirm_percent').innerText = percent;
    document.getElementById('confirm_dollar_price').innerText = dollarPrice;
    document.getElementById('confirm_gold_with_work').innerText = goldWithWork;
    document.getElementById('confirm_gold_in_diamond').innerText = goldInDiamond;
    document.getElementById('confirm_shoghl_agnaby').innerText = shoghlAjnaby;

    // Show the confirmation modal
    document.getElementById('confirmationModal').style.display = 'block';
});

document.getElementById('confirmSubmit').addEventListener('click', function(event) {
    // Submit the form
    document.getElementById('goldPriceForm').submit();
});

document.getElementById('cancelSubmit').addEventListener('click', function(event) {
    // Hide the modal if user cancels
    document.getElementById('confirmationModal').style.display = 'none';
});

    </script>
</body>
</html>
