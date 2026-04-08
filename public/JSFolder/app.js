// Logout Modal Functionality

document.addEventListener('DOMContentLoaded', function() {
    console.log('Logout modal script loaded');
    
    // Open Logout Modal
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        console.log('Logout button found');
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Logout button clicked');
            openLogoutModal();
        });
    } else {
        console.warn('Logout button not found');
    }

    // Close modal when clicking overlay
    const modalOverlay = document.querySelector('.modal-overlay');
    if (modalOverlay) {
        modalOverlay.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal && modal.id === 'logoutModal') {
                closeLogoutModal();
            }
        });
    }
});

function openLogoutModal() {
    const modal = document.getElementById('logoutModal');
    console.log('Opening logout modal', modal);
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
        console.log('Modal opened successfully');
    } else {
        console.error('Logout modal element not found');
    }
}

function closeLogoutModal() {
    const modal = document.getElementById('logoutModal');
    console.log('Closing logout modal', modal);
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = '';
        console.log('Modal closed successfully');
    } else {
        console.error('Logout modal element not found');
    }
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('logoutModal');
        if (modal && modal.classList.contains('show')) {
            closeLogoutModal();
        }
    }
});
