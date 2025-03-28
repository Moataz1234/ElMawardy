<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopify Orders API</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    @include('components.navbar')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Add SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .order-card {
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .order-header {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px 8px 0 0;
            border-bottom: 1px solid #dee2e6;
        }
        .order-body {
            padding: 15px;
        }
        .order-items {
            margin-top: 15px;
        }
        .item-row {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .item-row:last-child {
            border-bottom: none;
        }
        .pagination-container {
            margin-top: 30px;
        }
        .filter-container {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        #loading {
            display: none;
            text-align: center;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4">Shopify Orders</h1>
        
        <div class="filter-container">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select id="status" class="form-select">
                        <option value="any">All Orders</option>
                        <option value="unfulfilled" selected>Unfulfilled</option>
                        <option value="fulfilled">Fulfilled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="sort_direction" class="form-label">Sort Order</label>
                    <select id="sort_direction" class="form-select">
                        <option value="desc" selected>Newest First</option>
                        <option value="asc">Oldest First</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="per_page" class="form-label">Items Per Page</label>
                    <select id="per_page" class="form-select">
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button id="apply-filters" class="btn btn-primary w-100">Apply Filters</button>
                </div>
            </div>
        </div>
        
        <div id="error-container" class="alert alert-danger" style="display: none;"></div>
        
        <div id="loading">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading orders...</p>
        </div>
        
        <div id="orders-container"></div>
        
        <div id="pagination-container" class="d-flex justify-content-center mt-4"></div>
        
        <div id="pagination-info" class="text-center mt-3"></div>
    </div>

    <!-- Add this modal to the page -->
    <div class="modal fade" id="assignItemModal" tabindex="-1" aria-labelledby="assignItemModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignItemModalLabel">Assign Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="modal-loading" class="text-center my-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p>Searching for matching items...</p>
                    </div>
                    <div id="no-items-found" class="alert alert-warning" style="display: none;">
                        No matching items found for this SKU.
                    </div>
                    <div id="matching-items-container" style="display: none;">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Serial Number</th>
                                    <th>Model</th>
                                    <th>Weight</th>
                                    <th>Shop</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="matching-items-list">
                                <!-- Items will be populated here -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Current state
        let currentPage = 1;
        let currentStatus = 'unfulfilled';
        let currentSortDirection = 'desc';
        let currentPerPage = 10;
        
        // DOM elements
        const ordersContainer = document.getElementById('orders-container');
        const paginationContainer = document.getElementById('pagination-container');
        const paginationInfo = document.getElementById('pagination-info');
        const errorContainer = document.getElementById('error-container');
        const loadingIndicator = document.getElementById('loading');
        
        // Add these variables and functions to your script
        let currentOrderId = '';
        let currentItemId = '';
        let currentItemSku = '';
        let currentItemTitle = '';
        let currentItemPrice = '';
        
        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            // Set initial filter values
            document.getElementById('status').value = currentStatus;
            document.getElementById('sort_direction').value = currentSortDirection;
            document.getElementById('per_page').value = currentPerPage;
            
            // Load initial data
            fetchOrders();
            
            // Add event listener for filter button
            document.getElementById('apply-filters').addEventListener('click', () => {
                currentStatus = document.getElementById('status').value;
                currentSortDirection = document.getElementById('sort_direction').value;
                currentPerPage = document.getElementById('per_page').value;
                currentPage = 1; // Reset to first page when filters change
                fetchOrders();
            });
        });
        
        // Fetch orders from API
        function fetchOrders() {
            // Show loading indicator
            loadingIndicator.style.display = 'block';
            ordersContainer.innerHTML = '';
            paginationContainer.innerHTML = '';
            paginationInfo.innerHTML = '';
            errorContainer.style.display = 'none';
            
            // Build API URL with query parameters
            const apiUrl = `/api/shopify/orders?page=${currentPage}&per_page=${currentPerPage}&status=${currentStatus}&sort_direction=${currentSortDirection}`;
            
            // Fetch data from API
            fetch(apiUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    // Hide loading indicator
                    loadingIndicator.style.display = 'none';
                    
                    // Render orders
                    renderOrders(data.data);
                    
                    // Render pagination
                    renderPagination(data.meta);
                    
                    // Render pagination info
                    paginationInfo.innerHTML = `Showing ${data.meta.from} to ${data.meta.to} of ${data.meta.total} orders`;
                })
                .catch(error => {
                    // Hide loading indicator
                    loadingIndicator.style.display = 'none';
                    
                    // Show error
                    errorContainer.style.display = 'block';
                    errorContainer.textContent = `Error loading orders: ${error.message}`;
                });
        }
        
        // Render orders
        function renderOrders(orders) {
            if (orders.length === 0) {
                ordersContainer.innerHTML = '<div class="alert alert-info">No orders found matching your criteria.</div>';
                return;
            }
            
            let html = '';
            
            orders.forEach(order => {
                html += `
                    <div class="card order-card">
                        <div class="order-header">
                            <div class="row">
                                <div class="col-md-3">
                                    <h5 class="mb-0">${order.order_number}</h5>
                                    <small class="text-muted">${formatDate(order.created_at)}</small>
                                </div>
                                <div class="col-md-3">
                                    <strong>Customer:</strong> ${order.customer.name}
                                </div>
                                <div class="col-md-3">
                                    <strong>Total:</strong> ${parseFloat(order.total_price).toFixed(2)}
                                </div>
                                <div class="col-md-3">
                                    <span class="badge ${order.fulfillment_status === 'fulfilled' ? 'bg-success' : 'bg-warning'}">
                                        ${capitalizeFirstLetter(order.fulfillment_status)}
                                    </span>
                                    <span class="badge ${order.financial_status === 'paid' ? 'bg-success' : 'bg-secondary'}">
                                        ${capitalizeFirstLetter(order.financial_status)}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="order-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h6>Shipping Address</h6>
                                    ${renderAddress(order.shipping_address)}
                                </div>
                                <div class="col-md-6">
                                    <h6>Contact Information</h6>
                                    <p class="mb-0">Email: ${order.customer.email || 'N/A'}</p>
                                    <p class="mb-0">Phone: ${order.customer.phone || 'N/A'}</p>
                                </div>
                            </div>
                            
                            <div class="order-items">
                                <h6>Order Items</h6>
                                ${renderOrderItems(order.line_items)}
                            </div>
                        </div>
                    </div>
                `;
            });
            
            ordersContainer.innerHTML = html;
        }
        
        // Render address
        function renderAddress(address) {
            if (!address) return '<p>No address provided</p>';
            
            return `
                <p class="mb-0">${address.name || ''}</p>
                <p class="mb-0">${address.address1 || ''}</p>
                ${address.address2 ? `<p class="mb-0">${address.address2}</p>` : ''}
                <p class="mb-0">${address.city || ''}, ${address.province_code || ''} ${address.zip || ''}</p>
                <p class="mb-0">${address.country || ''}</p>
            `;
        }
        
        // Render order items
        function renderOrderItems(items) {
            let html = '';
            
            items.forEach(item => {
                html += `
                    <div class="row item-row">
                        <div class="col-md-2">
                            ${item.image_url 
                                ? `<img src="${item.image_url}" alt="${item.title}" class="img-fluid" style="max-height: 80px;">` 
                                : `<div class="bg-light text-center p-3">No Image</div>`
                            }
                        </div>
                        <div class="col-md-4">
                            <p class="mb-0"><strong>${item.title}</strong></p>
                            <p class="mb-0 text-muted">SKU: ${item.sku || 'N/A'}</p>
                        </div>
                        <div class="col-md-2 text-center">
                            <p class="mb-0">Qty: ${item.quantity}</p>
                        </div>
                        <div class="col-md-2 text-end">
                            <p class="mb-0">${parseFloat(item.price).toFixed(2)}</p>
                        </div>
                        <div class="col-md-2 text-end">
                            <button class="btn btn-primary btn-sm assign-item-btn" 
                                    data-item-id="${item.id}" 
                                    data-item-sku="${item.sku || ''}" 
                                    data-item-title="${item.title}"
                                    data-item-price="${item.price}"
                                    data-order-id="${currentOrderId}">
                                Assign Item
                            </button>
                        </div>
                    </div>
                `;
            });
            
            return html;
        }
        
        // Render pagination
        function renderPagination(meta) {
            if (meta.last_page <= 1) return;
            
            let html = `
                <nav>
                    <ul class="pagination">
                        <li class="page-item ${meta.current_page === 1 ? 'disabled' : ''}">
                            <a class="page-link" href="#" data-page="${meta.current_page - 1}" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
            `;
            
            for (let i = 1; i <= meta.last_page; i++) {
                html += `
                    <li class="page-item ${i === meta.current_page ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>
                `;
            }
            
            html += `
                        <li class="page-item ${meta.current_page === meta.last_page ? 'disabled' : ''}">
                            <a class="page-link" href="#" data-page="${meta.current_page + 1}" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            `;
            
            paginationContainer.innerHTML = html;
            
            // Add event listeners to pagination links
            document.querySelectorAll('.pagination .page-link').forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const page = parseInt(e.target.closest('.page-link').dataset.page);
                    if (page && page !== currentPage) {
                        currentPage = page;
                        fetchOrders();
                        // Scroll to top
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                });
            });
        }
        
        // Helper functions
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
        
        function capitalizeFirstLetter(string) {
            if (!string) return 'Unknown';
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        // Add event delegation for assign buttons
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('assign-item-btn') || 
                e.target.closest('.assign-item-btn')) {
                
                const button = e.target.classList.contains('assign-item-btn') ? 
                              e.target : e.target.closest('.assign-item-btn');
                
                currentOrderId = button.dataset.orderId;
                currentItemId = button.dataset.itemId;
                currentItemSku = button.dataset.itemSku;
                currentItemTitle = button.dataset.itemTitle;
                currentItemPrice = button.dataset.itemPrice;
                
                // Show the modal
                const modal = new bootstrap.Modal(document.getElementById('assignItemModal'));
                modal.show();
                
                // Reset modal state
                document.getElementById('modal-loading').style.display = 'block';
                document.getElementById('no-items-found').style.display = 'none';
                document.getElementById('matching-items-container').style.display = 'none';
                document.getElementById('matching-items-list').innerHTML = '';
                
                // Fetch matching items
                fetchMatchingItems(currentItemSku);
            }
            
            // Handle assign action
            if (e.target.classList.contains('assign-action-btn') || 
                e.target.closest('.assign-action-btn')) {
                
                const button = e.target.classList.contains('assign-action-btn') ? 
                              e.target : e.target.closest('.assign-action-btn');
                
                const serialNumber = button.dataset.serialNumber;
                assignItemToOrder(serialNumber);
            }
        });

        // Function to fetch matching items
        function fetchMatchingItems(sku) {
            if (!sku) {
                document.getElementById('modal-loading').style.display = 'none';
                document.getElementById('no-items-found').style.display = 'block';
                return;
            }
            
            fetch(`/api/gold-items/match-sku/${sku}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    document.getElementById('modal-loading').style.display = 'none';
                    
                    if (data.items.length === 0) {
                        document.getElementById('no-items-found').style.display = 'block';
                        return;
                    }
                    
                    document.getElementById('matching-items-container').style.display = 'block';
                    
                    let html = '';
                    data.items.forEach(item => {
                        html += `
                            <tr>
                                <td>${item.serial_number}</td>
                                <td>${item.model}</td>
                                <td>${item.weight}</td>
                                <td>${item.shop_name}</td>
                                <td>
                                    <button class="btn btn-success btn-sm assign-action-btn" 
                                            data-serial-number="${item.serial_number}">
                                        Assign
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    
                    document.getElementById('matching-items-list').innerHTML = html;
                })
                .catch(error => {
                    console.error('Error fetching matching items:', error);
                    document.getElementById('modal-loading').style.display = 'none';
                    document.getElementById('no-items-found').style.display = 'block';
                    document.getElementById('no-items-found').textContent = 'Error fetching matching items: ' + error.message;
                });
        }

        // Function to assign item to order
        function assignItemToOrder(serialNumber) {
            const data = {
                order_id: currentOrderId,
                item_id: currentItemId,
                serial_number: serialNumber,
                item_title: currentItemTitle,
                item_price: currentItemPrice
            };
            
            console.log('Sending data:', data);
            
            fetch('/api/shopify/assign-item', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                console.log('Response status:', response.status);
                
                // First check if the response is JSON
                const contentType = response.headers.get('content-type');
                console.log('Content-Type:', contentType);
                
                if (contentType && contentType.includes('application/json')) {
                    return response.json().then(data => {
                        console.log('Response data:', data);
                        
                        if (!response.ok) {
                            if (typeof data.message === 'string') {
                                throw new Error(data.message);
                            } else if (typeof data.error === 'string') {
                                throw new Error(data.error);
                            } else if (data.message) {
                                // If message is an object (validation errors)
                                const errorMessages = Object.values(data.message).flat().join(', ');
                                throw new Error(errorMessages);
                            } else {
                                throw new Error('Error assigning item');
                            }
                        }
                        return data;
                    });
                } else {
                    // If not JSON, get the text and throw an error
                    return response.text().then(text => {
                        console.error('Non-JSON response:', text);
                        throw new Error('Server returned an invalid response format');
                    });
                }
            })
            .then(data => {
                // Close the modal
                bootstrap.Modal.getInstance(document.getElementById('assignItemModal')).hide();
                
                // Show success message
                Swal.fire({
                    title: 'Success!',
                    text: data.message || 'Item assigned successfully',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Refresh the orders list to reflect the changes
                    fetchOrders();
                });
            })
            .catch(error => {
                console.error('Error assigning item:', error);
                
                let errorMessage = 'Failed to assign item';
                
                if (error.message) {
                    errorMessage += ': ' + error.message;
                }
                
                Swal.fire({
                    title: 'Error!',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        }
    </script>
</body>
</html> 