/**
 * User Management Modals JavaScript
 * Handles all modal functionality for User Management page
 */

// Delete User Modal Functions
function openDeleteModal(userId, username, csrfToken) {
    const modal = document.getElementById('deleteUserModal');
    const userNameElement = document.getElementById('deleteUserName');
    const deleteForm = document.getElementById('deleteUserForm');
    const deleteToken = document.getElementById('deleteToken');
    const confirmationCheckbox = document.getElementById('deleteConfirmation');
    const deleteSubmitBtn = document.getElementById('deleteSubmitBtn');
    
    // Set user information
    userNameElement.textContent = username;
    
    // Set form action using the correct Symfony route
    deleteForm.action = '/admin/user-management/' + userId + '/delete';
    
    // Set the CSRF token
    deleteToken.value = csrfToken;
    
    // Reset confirmation checkbox and disable button
    if (confirmationCheckbox) {
        confirmationCheckbox.checked = false;
    }
    if (deleteSubmitBtn) {
        deleteSubmitBtn.disabled = true;
    }
    
    // Show modal
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
    
    // Focus on close button for accessibility
    setTimeout(() => {
        modal.querySelector('.modal-close').focus();
    }, 100);
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteUserModal');
    modal.classList.remove('show');
    document.body.style.overflow = '';
}

function toggleDeleteButton() {
    const confirmationCheckbox = document.getElementById('deleteConfirmation');
    const deleteSubmitBtn = document.getElementById('deleteSubmitBtn');
    
    if (confirmationCheckbox && deleteSubmitBtn) {
        deleteSubmitBtn.disabled = !confirmationCheckbox.checked;
    }
}

function toggleToggleButton() {
    const confirmationCheckbox = document.getElementById('toggleConfirmation');
    const toggleSubmitBtn = document.getElementById('toggleSubmitBtn');
    
    if (confirmationCheckbox && toggleSubmitBtn) {
        toggleSubmitBtn.disabled = !confirmationCheckbox.checked;
    }
}

// Toggle User Status Modal Functions
function openToggleModal(userId, username, currentStatus, csrfToken) {
    const modal = document.getElementById('toggleUserModal');
    const userNameElement = document.getElementById('toggleUserName');
    const toggleForm = document.getElementById('toggleUserForm');
    const toggleToken = document.getElementById('toggleToken');
    const modalTitle = document.getElementById('toggleModalTitle');
    const toggleInfoTitle = document.getElementById('toggleInfoTitle');
    const toggleInfoMessage = document.getElementById('toggleInfoMessage');
    const toggleInfoIcon = document.getElementById('toggleInfoIcon');
    const submitBtn = document.getElementById('toggleSubmitBtn');
    const submitText = document.getElementById('toggleSubmitText');
    const toggleConfirmation = document.getElementById('toggleConfirmation');
    
    // Set user information
    userNameElement.textContent = username;
    
    // Reset confirmation checkbox and disable button
    if (toggleConfirmation) {
        toggleConfirmation.checked = false;
    }
    if (submitBtn) {
        submitBtn.disabled = true;
    }
    
    // Configure modal based on current status
    if (currentStatus === 'active') {
        // User is currently active, will be deactivated
        modalTitle.textContent = 'Deactivate User';
        toggleInfoTitle.textContent = 'Warning: Access Will Be Revoked';
        toggleInfoMessage.textContent = 'This user will lose access to the system and will not be able to log in until reactivated.';
        toggleInfoIcon.className = 'fas fa-user-slash';
        submitBtn.className = 'btn-danger';
        submitText.textContent = 'Deactivate User';
        toggleForm.action = '/admin/user-management/' + userId + '/toggle-status';
    } else {
        // User is currently inactive, will be activated
        modalTitle.textContent = 'Activate User';
        toggleInfoTitle.textContent = 'Access Will Be Restored';
        toggleInfoMessage.textContent = 'This user will regain access to the system and will be able to log in immediately.';
        toggleInfoIcon.className = 'fas fa-user-check';
        submitBtn.className = 'btn-success';
        submitText.textContent = 'Activate User';
        toggleForm.action = '/admin/user-management/' + userId + '/toggle-status';
    }
    
    // Set the CSRF token
    toggleToken.value = csrfToken;
    
    // Show modal
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
    
    // Focus on close button for accessibility
    setTimeout(() => {
        modal.querySelector('.modal-close').focus();
    }, 100);
}

function closeToggleModal() {
    const modal = document.getElementById('toggleUserModal');
    modal.classList.remove('show');
    document.body.style.overflow = '';
}

// Success Modal Functions
function openSuccessModal(message) {
    const modal = document.getElementById('successModal');
    const messageElement = document.getElementById('successMessage');
    
    // Set success message
    if (message) {
        messageElement.textContent = message;
    }
    
    // Show modal
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
    
    // Auto-close after 3 seconds
    setTimeout(function() {
        closeSuccessModal();
    }, 3000);
    
    // Focus on close button for accessibility
    setTimeout(() => {
        modal.querySelector('.modal-close').focus();
    }, 100);
}

function closeSuccessModal() {
    const modal = document.getElementById('successModal');
    modal.classList.remove('show');
    document.body.style.overflow = '';
}

// Deactivate Success Modal Functions
function openDeactivateSuccessModal(message) {
    const modal = document.getElementById('deactivateSuccessModal');
    const messageElement = document.getElementById('deactivateSuccessMessage');
    
    // Set success message
    if (message) {
        messageElement.textContent = message;
    }
    
    // Show modal
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
    
    // Auto-close after 4 seconds (longer for important info)
    setTimeout(function() {
        closeDeactivateSuccessModal();
    }, 4000);
    
    // Focus on close button for accessibility
    setTimeout(() => {
        modal.querySelector('.modal-close').focus();
    }, 100);
}

function closeDeactivateSuccessModal() {
    const modal = document.getElementById('deactivateSuccessModal');
    modal.classList.remove('show');
    document.body.style.overflow = '';
}

// Activate Success Modal Functions
function openActivateSuccessModal(message) {
    const modal = document.getElementById('activateSuccessModal');
    const messageElement = document.getElementById('activateSuccessMessage');
    
    // Set success message
    if (message) {
        messageElement.textContent = message;
    }
    
    // Show modal
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
    
    // Auto-close after 3 seconds
    setTimeout(function() {
        closeActivateSuccessModal();
    }, 3000);
    
    // Focus on close button for accessibility
    setTimeout(() => {
        modal.querySelector('.modal-close').focus();
    }, 100);
}

function closeActivateSuccessModal() {
    const modal = document.getElementById('activateSuccessModal');
    modal.classList.remove('show');
    document.body.style.overflow = '';
}

// Initialize Modal System
document.addEventListener('DOMContentLoaded', function() {
    // Get all modals
    const deleteModal = document.getElementById('deleteUserModal');
    const toggleModal = document.getElementById('toggleUserModal');
    const successModal = document.getElementById('successModal');
    const deactivateSuccessModal = document.getElementById('deactivateSuccessModal');
    const activateSuccessModal = document.getElementById('activateSuccessModal');
    
    // Get all overlays
    const deleteOverlay = deleteModal?.querySelector('.modal-overlay');
    const toggleOverlay = toggleModal?.querySelector('.modal-overlay');
    const successOverlay = successModal?.querySelector('.modal-overlay');
    const deactivateOverlay = deactivateSuccessModal?.querySelector('.modal-overlay');
    const activateOverlay = activateSuccessModal?.querySelector('.modal-overlay');
    
    // Add overlay click handlers
    if (deleteOverlay) {
        deleteOverlay.addEventListener('click', closeDeleteModal);
    }
    
    if (toggleOverlay) {
        toggleOverlay.addEventListener('click', closeToggleModal);
    }
    
    if (successOverlay) {
        successOverlay.addEventListener('click', closeSuccessModal);
    }
    
    if (deactivateOverlay) {
        deactivateOverlay.addEventListener('click', closeDeactivateSuccessModal);
    }
    
    if (activateOverlay) {
        activateOverlay.addEventListener('click', closeActivateSuccessModal);
    }
    
    // Show appropriate success modal based on message content
    const successAlerts = document.querySelectorAll('.alert-success');
    if (successAlerts.length > 0) {
        // Get the success message from the first alert
        const successMessage = successAlerts[0].querySelector('span').textContent.toLowerCase();
        
        // Hide the original alert since we're showing a modal
        successAlerts.forEach(alert => alert.style.display = 'none');
        
        // Show appropriate modal based on message content
        if (successMessage.includes('deactivat')) {
            openDeactivateSuccessModal(successAlerts[0].querySelector('span').textContent);
        } else if (successMessage.includes('activat')) {
            openActivateSuccessModal(successAlerts[0].querySelector('span').textContent);
        } else {
            // General success for other operations (edit, delete, etc.)
            openSuccessModal(successAlerts[0].querySelector('span').textContent);
        }
    }
    
    // Keyboard navigation support
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            // Close any open modal
            const openModals = document.querySelectorAll('.modal.show');
            openModals.forEach(modal => {
                if (modal.id === 'deleteUserModal') closeDeleteModal();
                else if (modal.id === 'toggleUserModal') closeToggleModal();
                else if (modal.id === 'successModal') closeSuccessModal();
                else if (modal.id === 'deactivateSuccessModal') closeDeactivateSuccessModal();
                else if (modal.id === 'activateSuccessModal') closeActivateSuccessModal();
            });
        }
        
        // Tab trap within modals
        const openModal = document.querySelector('.modal.show');
        if (openModal && e.key === 'Tab') {
            const focusableElements = openModal.querySelectorAll(
                'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
            );
            const firstElement = focusableElements[0];
            const lastElement = focusableElements[focusableElements.length - 1];
            
            if (e.shiftKey) {
                if (document.activeElement === firstElement) {
                    e.preventDefault();
                    lastElement.focus();
                }
            } else {
                if (document.activeElement === lastElement) {
                    e.preventDefault();
                    firstElement.focus();
                }
            }
        }
    });
    
    // Prevent body scroll when modal is open
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'class') {
                const hasOpenModal = document.querySelectorAll('.modal.show').length > 0;
                document.body.style.overflow = hasOpenModal ? 'hidden' : '';
            }
        });
    });
    
    // Observe all modals for class changes
    document.querySelectorAll('.modal').forEach(modal => {
        observer.observe(modal, { attributes: true });
    });
});

// Export functions for global access
window.UserManagementModals = {
    openDeleteModal,
    closeDeleteModal,
    openToggleModal,
    closeToggleModal,
    openSuccessModal,
    closeSuccessModal,
    openDeactivateSuccessModal,
    closeDeactivateSuccessModal,
    openActivateSuccessModal,
    closeActivateSuccessModal
};
