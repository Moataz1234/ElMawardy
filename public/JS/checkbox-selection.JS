
document.addEventListener('DOMContentLoaded', function() {
    // Initialize arrays for tracking selections
    let selectedIds = JSON.parse(localStorage.getItem('selectedItems')) || [];

    // Function to update checkbox states and local storage
    function updateSelections() {
        document.querySelectorAll('.select-item').forEach(checkbox => {
            const itemId = parseInt(checkbox.dataset.id);
            checkbox.checked = selectedIds.includes(itemId);
        });
        localStorage.setItem('selectedItems', JSON.stringify(selectedIds));
    }

    // Function to clear selections
    function clearSelections() {
        document.querySelectorAll('.select-item').forEach(checkbox => {
            checkbox.checked = false;
        });
        selectedIds = [];
        localStorage.removeItem('selectedItems');
    }

    // Initialize checkboxes on page load
    updateSelections();

    // Add event listeners to checkboxes
    document.querySelectorAll('.select-item').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const itemId = parseInt(this.dataset.id);
            if (this.checked) {
                if (!selectedIds.includes(itemId)) selectedIds.push(itemId);
            } else {
                selectedIds = selectedIds.filter(id => id !== itemId);
            }
            updateSelections();
        });
    });

    // Handle button clicks for Sell and Transfer actions
    // function handleButtonClick(buttonId, actionUrl) {
    //     const button = document.getElementById(buttonId);
    //     if (button) {
    //         button.removeAttribute('onclick');
    //         button.addEventListener('click', function(e) {
    //             e.preventDefault();
    //             if (selectedIds.length === 0) {
    //                 alert(`Please select at least one item to ${button.innerText.toLowerCase()}.`);
    //                 return;
    //             }
    //             window.location.href = `${actionUrl}?ids=${selectedIds.join(',')}`;
    //         });
    //     }
    // }
    function handleButtonClick(buttonId, actionUrl) {
        const button = document.getElementById(buttonId);
        if (button) {
            button.removeAttribute('onclick'); // Remove inline onclick if any
            button.addEventListener('click', function(e) {
                e.preventDefault();

                if (selectedIds.length === 0) {
                    Swal.fire({ // Use SweetAlert here
                        icon: 'warning',
                        title: 'No Items Selected',
                        text: `Please select at least one item to ${button.innerText.toLowerCase()}.`,
                    });
                    return;
                }

                // If items are selected, proceed with the redirect
                Swal.fire({
                    title: 'Confirm Action',
                    text: `Are you sure you want to ${button.innerText.toLowerCase()} these items?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `${actionUrl}?ids=${selectedIds.join(',')}`;
                    }
                })
            });
        }
    }


    // Sell and Transfer URLs defined in Blade template
    handleButtonClick('sellItemsButton', sellRouteUrl);
    handleButtonClick('transferItemsButton', transferRouteUrl);

    // Clear selections when returning from form submission
    if (window.performance && window.performance.navigation.type === window.performance.navigation.TYPE_BACK_FORWARD) {
        clearSelections();
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