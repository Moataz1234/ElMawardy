// public/js/loader.js
document.addEventListener('DOMContentLoaded', function() {
    const preloader = document.getElementById('preloader');

    // Hide loader initially
    preloader.classList.add('hidden');

    // Show loader only on page refresh/reload
    window.addEventListener('beforeunload', function() {
        preloader.classList.remove('hidden');
    });

    // If it's a fresh page load (refresh), show the loader
    if (performance.navigation.type === 1) {
        preloader.classList.remove('hidden');

        // Hide loader after page is fully loaded
        window.addEventListener('load', function() {
            setTimeout(function() {
                preloader.classList.add('hidden');
            }, 500);
        });
    }
});