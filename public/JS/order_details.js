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

    // Update radio button names and IDs
    template.querySelectorAll('.order-type-radio').forEach((radio) => {
        radio.name = `order_type[${itemIndex}]`;
        radio.id = `${radio.value}_${itemIndex}`;
        // Update associated label's 'for' attribute
        const label = radio.nextElementSibling.nextElementSibling;
        if (label) {
            label.setAttribute('for', radio.id);
        }
    });

    // Add event listeners for the new item
    initializeItemEventListeners(template);

    // Append the new item
    document.getElementById('order-items').appendChild(template);

    // Increase item index
    itemIndex++;
});

// Function to initialize event listeners for an item
function initializeItemEventListeners(item) {
    // Add radio button change event for item type
    const typeRadios = item.querySelectorAll('.item-type-radio');
    typeRadios.forEach(function(radio) {
        radio.addEventListener('change', function() {
            const hiddenFields = item.querySelector('.hidden-fields');
            const shopSpecificFields = item.querySelector('.shop-specific-fields');
            const modelLabel = shopSpecificFields.querySelector('.model-label');
            const modelInput = shopSpecificFields.querySelector('.model-input');

            if (this.checked) {
                hiddenFields.style.display = 'block';
                shopSpecificFields.style.display = 'block';

                // Change label and placeholder based on item type
                if (this.value === 'الماظ') {
                    modelLabel.textContent = 'رقم القطعة';
                    // modelInput.placeholder = 'ادخل رقم القطعة';
                } else {
                    modelLabel.textContent = 'الموديل';
                    modelInput.placeholder = '';
                }
            }
        });
    });

    // Add radio button change event for order type
    // const orderTypeRadios = item.querySelectorAll('.order-type-radio');
    // orderTypeRadios.forEach(function(radio) {
    //     radio.addEventListener('change', function() {
    //         const shopSpecificFields = item.querySelector('.shop-specific-fields');
    //         if (this.checked) {
    //             shopSpecificFields.style.display = this.value === 'by_shop' ? 'block' : 'none';
    //         }
    //     });
    // });

    // Add radio-circle click events
    const radioCircles = item.querySelectorAll('.radio-circle');
    radioCircles.forEach(function(circle) {
        circle.addEventListener('click', function() {
            const radio = this.previousElementSibling;
            radio.checked = true;
            radio.dispatchEvent(new Event('change'));
        });
    });

    // Add remove item event
    const removeButton = item.querySelector('.remove-item');
    if (removeButton) {
        removeButton.addEventListener('click', function() {
            item.remove();
        });
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
        
        // Validation logic...
        const customerName = form.querySelector('input[name="customer_name"]').value.trim();
        const sellerName = form.querySelector('input[name="seller_name"]').value.trim();
        const orderItems = document.querySelectorAll('#order-items .order-item').length;

        if (!customerName || !sellerName) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ في البيانات',
                text: 'برجاء ملء جميع الحقول المطلوبة (اسم العميل، البائع)',
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

        // Form submission logic...
        const formData = new FormData(form);

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