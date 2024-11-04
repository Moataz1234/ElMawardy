document.addEventListener('DOMContentLoaded', function() {
    // Get the modal
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    const closeBtn = document.querySelector('.close-modal');

    // Add click event to all table images
    document.querySelectorAll('table img').forEach(img => {
        img.style.cursor = 'pointer';
        img.addEventListener('click', function() {
            const row = this.closest('tr');

            // Update modal content
            modalImage.src = this.src;
            document.getElementById('modalCode').textContent = row.querySelector('td:nth-child(3)').textContent.trim(); // Serial number
            document.getElementById('modalModel').textContent = row.querySelector('td:nth-child(6)').textContent.trim(); // Model
            document.getElementById('modalWeight').textContent = row.querySelector('td:nth-child(8)').textContent.trim(); // Weight
            document.getElementById('modalSource').textContent = row.querySelector('td:nth-child(4)').textContent.trim(); // Shop name
            document.getElementById('modalPurity').textContent = row.querySelector('td:nth-child(7)').textContent.trim(); // Gold color
            document.getElementById('modalKind').textContent = row.querySelector('td:nth-child(5)').textContent.trim(); // Kind

            // Show modal with animation
            modal.style.display = 'flex';
            setTimeout(() => {
                modal.classList.add('show');
            }, 10);
        });
    });

    // Close modal when clicking the close button
    closeBtn.addEventListener('click', function() {
        modal.classList.remove('show');
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300);
    });

    // Close modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.classList.remove('show');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.style.display === 'flex') {
            modal.classList.remove('show');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }
    });
});