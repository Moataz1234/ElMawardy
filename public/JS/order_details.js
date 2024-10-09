let itemIndex = 0;

document.getElementById('add-item').addEventListener('click', function() {
    const template = document.getElementById('order-item-template').cloneNode(true);
    template.id = `order-item-${itemIndex}`;
    template.style.display = 'block';

    // Update the name attributes for dynamic fields
    template.querySelectorAll('input, select').forEach((input) => {
        const name = input.getAttribute('name');
        if (name) {
            const newName = name.replace('[]', `[${itemIndex}]`);
            input.setAttribute('name', newName);
        }
    });

    // Append the new item
    document.getElementById('order-items').appendChild(template);

    // Increase item index
    itemIndex++;

    // Add event listener for toggle label visibility in the new item
    template.querySelector('.toggleLabel').addEventListener('click', function() {
        toggleLabelVisibility(this);
    });

    // Remove item on clicking the remove button
    template.querySelector('.remove-item').addEventListener('click', function() {
        template.remove();
    });
});

function toggleLabelVisibility(checkbox) {
    const itemContainer = checkbox.closest('.order-item');
    const weight_field = itemContainer.querySelector('.weight_field');
    const image_field = itemContainer.querySelector('.image_field');

    if (checkbox.checked) {
        weight_field.style.display = "inline";
        image_field.style.display = "inline";
    } else {
        weight_field.style.display = "none";
        image_field.style.display = "none";
    }
}

function toggleCustomerDetails() {
    var textArea = document.getElementById("order_detail");
    var byCustomer = document.getElementById("by_customer").checked;
    var byShop = document.getElementById("by_shop").checked;
    var byTwo = document.getElementById("by_two").checked;

    // Show textarea if either radio button is selected
    if (byCustomer || byShop || by_two) {
        textArea.style.display = "block";
    }
}
document.addEventListener('DOMContentLoaded', function() {
    const orderCheckboxes = document.querySelectorAll('.order-checkbox');
    const maxSelections = 5;

    orderCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const selectedCount = document.querySelectorAll('.order-checkbox:checked').length;

            if (selectedCount > maxSelections) {
                alert('You can select up to 5 orders only.');
                this.checked = false;
            }
        });
    });
});

// JavaScript for light-up effect and badge updating
document.addEventListener('DOMContentLoaded', function() {
    const badge = document.querySelector('.badge');
    const link = document.querySelector('.nav-link');
    // Logic to handle updates (simulated for demonstration)
});