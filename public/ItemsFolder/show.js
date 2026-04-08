// Add to Cart functionality - Open Modal
document.querySelectorAll('.add-to-cart-btn:not(.disabled)').forEach(btn => {
    btn.addEventListener('click', function() {
        const productName = this.getAttribute('data-product-name');
        const productId = this.getAttribute('data-product-id');
        
        // Open modal with product info
        openAddToCartModal(productId, productName);
    });
});

// Open Add to Cart Modal
function openAddToCartModal(productId, productName) {
    const modal = document.getElementById('addToCartModal');
    const cartProductId = document.getElementById('cartProductId');
    const cartProductName = document.getElementById('cartProductName');
    const cartProductDisplay = document.getElementById('cartProductDisplay');
    
    if (modal && cartProductId && cartProductName && cartProductDisplay) {
        cartProductId.value = productId;
        cartProductName.value = productName;
        cartProductDisplay.textContent = productName;
        
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
}

// Close Add to Cart Modal
function closeAddToCartModal() {
    const modal = document.getElementById('addToCartModal');
    const form = document.getElementById('addToCartForm');
    
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = '';
        
        // Reset form
        if (form) {
            form.reset();
        }
    }
}

// Handle Add to Cart Form Submission
document.getElementById('addToCartForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const productId = formData.get('productId');
    const quantity = formData.get('quantity');
    const productName = formData.get('productName');
    
    // Get submit button
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalBtnContent = submitBtn.innerHTML;
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
    
    // Determine the correct route based on current path
    const currentPath = window.location.pathname;
    const isAdmin = currentPath.includes('/admin/');
    const cartAddUrl = isAdmin ? `/admin/cart/add/${productId}` : `/staff/cart/add/${productId}`;
    
    // Update quantity in formData
    formData.set('quantity', quantity);
    
    // Send POST request to add to cart
    fetch(cartAddUrl, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.text();
    })
    .then(data => {
        // Close modal
        closeAddToCartModal();
        
        // Show success message
        showToast(`${productName} added to cart!`, 'success');
        
        // Reset button
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnContent;
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Failed to add item to cart. Please try again.', 'error');
        
        // Reset button
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnContent;
    });
});

// Close modal when clicking overlay
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function() {
        const modal = this.closest('.modal');
        if (modal && modal.id === 'addToCartModal') {
            closeAddToCartModal();
        }
    });
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const addToCartModal = document.getElementById('addToCartModal');
        if (addToCartModal && addToCartModal.classList.contains('show')) {
            closeAddToCartModal();
        }
    }
});

function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');
    
    if (toast && toastMessage) {
        // Remove existing type classes
        toast.classList.remove('toast-success', 'toast-info', 'toast-warning', 'toast-error');
        
        // Add type class
        if (type === 'info') {
            toast.classList.add('toast-info');
        } else if (type === 'warning') {
            toast.classList.add('toast-warning');
        } else if (type === 'error') {
            toast.classList.add('toast-error');
        } else {
            toast.classList.add('toast-success');
        }
        
        toastMessage.textContent = message;
        toast.classList.add('show');
        
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }
}
