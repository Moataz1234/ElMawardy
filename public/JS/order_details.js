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
document.addEventListener('DOMContentLoaded', function() {
    // Add event listener to show ring size input when "ring" is selected
    const typeDropdown = template.querySelector('select[name="order_kind[]"]');
    typeDropdown.addEventListener('change', function() {
        toggleRingSizeVisibility(this);
    });
});

function toggleRingSizeVisibility(dropdown) {
    const itemContainer = dropdown.closest('.order-item');
    const ringSizeField = itemContainer.querySelector('.ring-size');

    if (dropdown.value === 'Ring') { // Replace 'ring' with the exact value for "ring" option in your dropdown
        ringSizeField.style.display = 'block';
    } else {
        ringSizeField.style.display = 'none';
    }
}

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
});

// Add this code after the form initialization
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.custom-form');
    
    // Prevent form submission on enter key
    form.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            return false;
        }
    });

    // Add at least one order item when the page loads
    document.getElementById('add-item').click();

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get required fields
        const customerName = form.querySelector('input[name="customer_name"]').value.trim();
        const sellerName = form.querySelector('input[name="seller_name"]').value.trim();
        const orderDetails = form.querySelector('textarea[name="order_details"]').value.trim();
        const orderItems = document.querySelectorAll('#order-items .order-item').length;

        // Validate required fields
        if (!customerName || !sellerName || !orderDetails) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ في البيانات',
                text: 'برجاء ملء جميع الحقول المطلوبة (اسم العميل، البائع، موضوع الطلب)',
                confirmButtonText: 'حسناً'
            });
            return;
        }

        if (orderItems === 0) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ في البيانات',
                text: 'يجب إضافة منتج واحد على الأقل',
                confirmButtonText: 'حسناً'
            });
            return;
        }

        // Create a new FormData object
        const formData = new FormData(form);

        // Remove template item data from FormData
        const templateInputs = document.getElementById('order-item-template').querySelectorAll('input, select');
        templateInputs.forEach(input => {
            if (input.name) {
                // Get all values for this input name
                const values = formData.getAll(input.name);
                // Remove the last value (template value)
                values.pop();
                // Remove all values for this name
                formData.delete(input.name);
                // Add back all values except the template
                values.forEach(value => formData.append(input.name, value));
            }
        });

        // If validation passes, submit the form
        fetch(form.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            Swal.fire({
                icon: 'success',
                title: 'تم الحفظ بنجاح',
                text: 'تم حفظ الطلب بنجاح',
                confirmButtonText: 'حسناً'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Optionally redirect or reset form
                    form.reset();
                    window.location.reload();
                }
            });
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'حدث خطأ أثناء حفظ البيانات',
                confirmButtonText: 'حسناً'
            });
        });
    });
});