<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Gold Prices</title>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
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
/* .modal {
      background-color: #333;
      border-radius: 15px;
      padding: 20px;
      width: 400px;
      color: white;
      text-align: center;
    } */

    .modal h2 {
      font-size: 1.5rem;
      margin-bottom: 20px;
      font-weight: bold;
    }

    .table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }

    .table th, .table td {
      padding: 10px;
      border: 1px solid #555;
    }

    .table th {
      text-align: left;
      background-color: #444;
      font-weight: bold;
    }

    .table td {
      background-color: #eaeaea;
      color: #333;
      text-align: right;
    }

.modal-content {
    background-color: white;
    padding: 20px;
    border-radius: 5px;
    max-width: 500px;
    width: 100%;
}
 * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    font-family: 'Roboto', sans-serif;
  }

  body {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    background-color: #f3f4f6;
  }

  .container {
    background-color: #333;
    width: 640px;
    padding: 20px;
    border-radius: 15px;
    color: white;
  }

  .container h1 {
    font-size: 24px;
    font-weight: 700;
    text-align: center;
    margin-bottom: 10px;
  }

  .divider {
    border-top: 1px solid #555;
    margin: 10px 0 20px;
  }

  .form-group {
    display: flex;
    flex-direction: column;
    margin-bottom: 15px;
  }

  .form-group label {
    font-size: 14px;
    margin-bottom: 10px;
  }

  .form-group input {
    padding: 5px;
    border: none;
    border-radius: 20px;
    background-color: #eee;
    font-size: 14px;
    color: #333;
    text-align: left;
  }
  .buttons {
      display: flex;
      justify-content: space-between;
    }
  .btn {
    margin-left: 170px;  
    width: 40%;
    padding: 10px;
    background-color: #32cd32;
    border: none;
    border-radius: 20px;
    color: white;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    transition: background-color 0.3s;
  }

  .btn-confirmation {
      width: 48%;
      padding: 10px;
      font-size: 1rem;
      border-radius: 5px;
      cursor: pointer;
      border: none;
    }

    .btn-cancel {
      background-color: #555;
      color: white;
    }

    .btn-confirm {
      background-color: #28a745;
      color: white;
    }

    .btn:hover {
      opacity: 0.9;
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

    <form id="goldPriceForm" method="POST" action="{{ route('gold_prices.store') }}">
        @csrf
        <div class="container">
  <h1>Gold Price</h1>
  <div class="divider"></div>
  
  <div class="form-group">
    <label for="goldBuy">Gold Buy</label>
    <input type="number" step="0.01" name="gold_buy" id="gold_buy" 
    value="{{ old('gold_buy', $latestGoldPrice->gold_buy ?? '') }}" required>  </div>
  
  <div class="form-group">
    <label for="goldSell">Gold Sell</label>
    <input type="number" step="0.01" name="gold_sell" id="gold_sell"
    value="{{ old('gold_sell', $latestGoldPrice->gold_sell ?? '') }}" required>
  </div>
  
  <div class="form-group">
    <label for="percent">Percent</label>
    <input type="number" step="0.01" name="percent" id="percent"
    value="{{ old('percent', $latestGoldPrice->percent ?? '') }}" required>
  </div>
  
  <div class="form-group">
    <label for="dollarPrice">Dollar Price</label>
    <input type="number" step="0.01" name="dollar_price" id="dollar_price"
    value="{{ old('dollar_price', $latestGoldPrice->dollar_price ?? '') }}" required>
  </div>
  
  <div class="form-group">
    <label for="goldWithWork">Gold With Work</label>
    <input type="number" step="0.01" name="gold_with_work" id="gold_with_work"
    value="{{ old('gold_with_work', $latestGoldPrice->gold_with_work ?? '') }}" required>  </div>
  
  <div class="form-group">
    <label for="goldInDiamond">Gold In Diamond</label>
    <input type="number" step="0.01" name="gold_in_diamond" id="gold_in_diamond" 
    value="{{ old('gold_in_diamond', $latestGoldPrice->gold_in_diamond ?? '') }}" required>
</div>
  
  <div class="form-group">
    <label for="shoghiAjnaby">Shoghi Ajnaby</label>
    <input type="number" step="0.01" name="shoghl_agnaby" id="shoghl_agnaby"
    value="{{ old('shoghl_agnaby', $latestGoldPrice->shoghl_agnaby ?? '') }}" required>  </div>
  
  {{-- <button class="btn">Update Price</button>
</div> --}}

        <button class="btn" type="button" id="previewButton">Update Prices</button>
<div>   
    <div id="confirmationModal" class="modal" style="display: none;">
        <div class="modal-content">         
            <h2>Confirm Your Submission</h2>
            <table class="table">
              <tr >
                <th>Gold Buy</th>
                <td id="confirm_gold_buy"></td>
              </tr>
              <tr >
                <th>Gold Sell</th>
                <td id="confirm_gold_sell"></td>
              </tr>
              <tr >
                <th>Percent</th>
                <td id="confirm_percent"></td>
              </tr>
              <tr >
                <th>Dollar Price</th>
                <td id="confirm_dollar_price"></td>
              </tr>
              <tr >
                <th>Gold With Work</th>
                <td id="confirm_gold_with_work"></td>
              </tr>
              <tr >
                <th>Gold In Diamond</th>
                <td id="confirm_gold_in_diamond"></td>
              </tr>
              <tr >
                <th>Shoghl Ajnaby</th>
                <td id="confirm_shoghl_agnaby"></td>
              </tr>
            </table>
            <div class="buttons">
 
                <button class="btn-confirmation  btn-cancel" id="cancelButton">Cancel</button>
                <button class="btn-confirmation btn-confirm" id="confirmSubmit" >Confirm</button>
              </div>
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
