<!DOCTYPE html>
<html>
<head>
    <style>
        #outerFormModal {
    position: fixed; /* Stay in place */
    z-index: 1000; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    background-color: rgba(0, 0, 0, 0.5); /* Black with opacity */
    display: flex; /* Flexbox for centering */
    justify-content: center; /* Center horizontally */
    align-items: center; /* Center vertically */
    
}

#outerForm {
    background-color: white; /* White background */
    padding: 20px; /* Some padding */
    border-radius: 5px; /* Rounded corners */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Shadow effect */
    width: 500px; /* Set a fixed width for the form */
    margin: 100px 400px;
}

/* Form group styles */
.form-group {
    margin-bottom: 15px; /* Spacing between fields */
    
}

/* Label styles */
.form-group label {
    display: block; /* Ensure labels are on their own line */
    margin-bottom: 5px; /* Space between label and input */
}

/* Input styles */
.form-group input {
    width: 100%; /* Full width for inputs */
    padding: 8px; /* Padding inside inputs */
    border: 1px solid #ccc; /* Light border */
    border-radius: 4px; /* Rounded corners for inputs */
}

/* Button styles */
.form-actions {
    display: flex; /* Flexbox for button alignment */
    justify-content: space-between; /* Space between buttons */
}

.form-actions button {
    padding: 10px 15px; /* Padding for buttons */
    border: none; /* Remove default border */
    border-radius: 4px; /* Rounded corners for buttons */
    cursor: pointer; /* Pointer cursor for buttons */
}

.form-actions button[type="submit"] {
    background-color: #28a745; /* Green background for submit button */
    color: white; /* White text for submit button */
}

.form-actions button[type="button"] {
    background-color: #dc3545; /* Red background for cancel button */
    color: white; /* White text for cancel button */
}
    </style>
</head>
<body>
<div id="outerFormModal" style="display:none;">
    <form id="outerForm" action="{{ route('gold-items.storeOuter') }}" method="POST">
        @csrf
        <div class="form-group">
            <input type="hidden" name="gold_serial_number" id="gold_serial_number">
        </div>
        <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" name="first_name" id="first_name" placeholder="First Name" required>
        </div>
        <div class="form-group">
            <label for="last_name">Last Name</label>
            <input type="text" name="last_name" id="last_name" placeholder="Last Name" required>
        </div>
        <div class="form-group">
            <label for="phone_number">Phone Number</label>
            <input type="text" name="phone_number" id="phone_number" placeholder="Phone Number" required>
        </div>
        <div class="form-group">
            <label for="reason">Reason</label>
            <input type="text" name="reason" id="reason" placeholder="Reason" required>
        </div>
        <div class="form-actions">
            <button type="submit">Save</button>
            <button type="button" onclick="closeOuterForm()">Cancel</button>
        </div>
    </form>
</div>
</body>
</html>