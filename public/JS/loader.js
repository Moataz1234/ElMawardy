// public/js/loader.js
class LoaderManager {
    constructor() {
        this.loader = document.getElementById('global-loader');
    }

    show() {
        this.loader.classList.remove('hidden');
    }

    hide() {
        this.loader.classList.add('hidden');
    }
}

const loader = new LoaderManager();

// Handle form submissions
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // Prevent double submission
            if (this.dataset.submitting === 'true') {
                e.preventDefault();
                return;
            }

            this.dataset.submitting = 'true';
            loader.show();

            // Add submit handler
            this.addEventListener('submit', async function(e) {
                try {
                    // Form submission will proceed
                    await Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Operation completed successfully',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong!'
                    });
                } finally {
                    loader.hide();
                    this.dataset.submitting = 'false';
                }
            });
        });
    });
});

// Handle AJAX requests
let originalFetch = window.fetch;
window.fetch = function() {
    loader.show();
    return originalFetch.apply(this, arguments)
        .finally(() => {
            loader.hide();
        });
};

// Handle Axios requests
if (window.axios) {
    axios.interceptors.request.use(function (config) {
        loader.show();
        return config;
    });

    axios.interceptors.response.use(
        function (response) {
            loader.hide();
            return response;
        },
        function (error) {
            loader.hide();
            return Promise.reject(error);
        }
    );
}