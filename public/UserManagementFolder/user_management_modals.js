/**
 * User Management Functions
 * Handles user management actions without modals
 */

// Delete User Function - Direct confirmation
function deleteUser(userId, username, csrfToken) {
    if (confirm(`Are you sure you want to delete user "${username}"? This action cannot be undone.`)) {
        // Create a form and submit it directly
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/user-management/${userId}/delete`;

        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = csrfToken;

        form.appendChild(tokenInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// Toggle User Status Function - Direct action
function toggleUserStatus(userId, username, currentStatus, csrfToken) {
    const action = currentStatus === 'active' ? 'deactivate' : 'activate';
    if (confirm(`Are you sure you want to ${action} user "${username}"?`)) {
        // Create a form and submit it directly
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/user-management/${userId}/toggle-status`;

        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = csrfToken;

        form.appendChild(tokenInput);
        document.body.appendChild(form);
        form.submit();
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
