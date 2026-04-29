document.addEventListener('DOMContentLoaded', function() {
    // Update low stock badge count
    updateLowStockBadge();
    
    // Global search functionality for cards
    const globalSearch = document.getElementById('globalSearch');
    if (globalSearch) {
        globalSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const activeTab = document.querySelector('.tab-content.active');
            
            if (activeTab) {
                const cards = activeTab.querySelectorAll('.stock-card, .low-stock-card, .product-in-use-card');
                cards.forEach(card => {
                    const textContent = card.textContent.toLowerCase();
                    if (textContent.includes(searchTerm)) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
                
                // Show/hide empty state based on visible cards
                const emptyState = activeTab.querySelector('.empty-state-cards');
                const visibleCards = Array.from(cards).filter(card => card.style.display !== 'none');
                
                if (emptyState) {
                    if (visibleCards.length === 0 && searchTerm !== '') {
                        emptyState.style.display = 'block';
                        emptyState.querySelector('h3').textContent = 'No Results Found';
                        emptyState.querySelector('p').textContent = `No items match "${searchTerm}"`;
                    } else if (visibleCards.length === 0) {
                        emptyState.style.display = 'block';
                        // Reset to original empty state message
                        if (activeTab.id === 'stock-inventory') {
                            emptyState.querySelector('h3').textContent = 'No Stock Entries Found';
                            emptyState.querySelector('p').textContent = 'There are currently no stock entries in the system.';
                        } else if (activeTab.id === 'low-stock') {
                            emptyState.querySelector('h3').textContent = 'No Low Stock Products';
                            emptyState.querySelector('p').textContent = 'All products are well-stocked at the moment.';
                        } else if (activeTab.id === 'product-in-use') {
                            emptyState.querySelector('h3').textContent = 'No Products Currently In Use';
                            emptyState.querySelector('p').textContent = 'There are no products currently being used in the system.';
                        }
                    } else {
                        emptyState.style.display = 'none';
                    }
                }
            }
        });
    }

    // Tab switching functionality
    const tabButtons = document.querySelectorAll('.tab-btn[data-tab]');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const targetTab = button.getAttribute('data-tab');
            
            // Add loading state
            button.style.opacity = '0.7';
            button.disabled = true;
            
            // Simulate loading for better UX
            setTimeout(() => {
                // Remove active class from all buttons and contents
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));
                
                // Add active class to clicked button and corresponding content
                button.classList.add('active');
                const targetContent = document.getElementById(targetTab);
                if (targetContent) {
                    targetContent.classList.add('active');
                    
                    // Add animation to cards in the newly active tab
                    const cards = targetContent.querySelectorAll('.stock-card, .low-stock-card, .product-in-use-card');
                    cards.forEach((card, index) => {
                        card.style.opacity = '0';
                        card.style.transform = 'translateY(20px)';
                        setTimeout(() => {
                            card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                            card.style.opacity = '1';
                            card.style.transform = 'translateY(0)';
                        }, index * 50);
                    });
                }
                
                // Clear global search when switching tabs
                if (globalSearch) {
                    globalSearch.value = '';
                    // Trigger search to reset card visibility
                    globalSearch.dispatchEvent(new Event('input'));
                }
                
                // Remove loading state
                button.style.opacity = '1';
                button.disabled = false;
                
                // Add visual feedback for Low Stock tab
                if (targetTab === 'low-stock') {
                    // Update badge when switching to low stock tab
                    updateLowStockBadge();
                    
                    const lowStockCount = document.querySelectorAll('#low-stock .low-stock-card').length;
                    if (lowStockCount === 0) {
                        // Show a message if no low stock products
                        const emptyState = document.querySelector('#low-stock .empty-state-cards');
                        if (emptyState) {
                            emptyState.style.display = 'block';
                        }
                    } else {
                        // Show success message for low stock products found
                        showNotification(`Found ${lowStockCount} product(s) with low stock`, 'info');
                    }
                }
                
                // Add visual feedback for Product In Use tab
                if (targetTab === 'product-in-use') {
                    const productInUseCount = document.querySelectorAll('#product-in-use .product-in-use-card').length;
                    if (productInUseCount === 0) {
                        // Show a message if no products in use
                        const emptyState = document.querySelector('#product-in-use .empty-state-cards');
                        if (emptyState) {
                            emptyState.style.display = 'block';
                        }
                    } else {
                        // Show success message for products in use found
                        showNotification(`Found ${productInUseCount} product(s) currently in use`, 'success');
                    }
                }
            }, 150);
        });
    });

    // Add hover effects and animations to cards
    const cards = document.querySelectorAll('.stock-card, .low-stock-card, .product-in-use-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-6px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(-4px)';
        });
    });

    // Add click animation to action buttons
    const actionButtons = document.querySelectorAll('.btn-delete-card, .btn-restock-card, .btn-view-card');
    actionButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Add ripple effect
            const ripple = document.createElement('span');
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            ripple.style.background = 'rgba(255, 255, 255, 0.6)';
            ripple.style.width = ripple.style.height = '40px';
            ripple.style.top = (e.clientY - this.offsetTop - 20) + 'px';
            ripple.style.left = (e.clientX - this.offsetLeft - 20) + 'px';
            ripple.style.animation = 'ripple 0.6s ease-out';
            ripple.style.pointerEvents = 'none';
            
            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
});

// Add ripple animation to CSS
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Function to update low stock badge
function updateLowStockBadge() {
    const lowStockButton = document.querySelector('.tab-btn[data-tab="low-stock"]');
    const lowStockCount = document.querySelectorAll('#low-stock .low-stock-card').length;
    
    if (lowStockButton) {
        // Remove existing badge if any
        const existingBadge = lowStockButton.querySelector('.low-stock-badge');
        if (existingBadge) {
            existingBadge.remove();
        }
        
        // Add badge if there are low stock products
        if (lowStockCount > 0) {
            const badge = document.createElement('span');
            badge.className = 'low-stock-badge';
            badge.textContent = lowStockCount;
            badge.style.cssText = `
                position: absolute;
                top: -8px;
                right: -8px;
                min-width: 20px;
                height: 20px;
                background: #ef4444;
                color: white;
                border-radius: 50%;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 11px;
                font-weight: 600;
                border: 2px solid white;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
                animation: pulse 2s infinite;
                z-index: 1;
            `;
            lowStockButton.appendChild(badge);
        }
    }
}

// Add pulse animation for badge
const pulseStyle = document.createElement('style');
pulseStyle.textContent = `
    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(239, 68, 68, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
        }
    }
`;
document.head.appendChild(pulseStyle);

// Function to show notifications
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotification = document.querySelector('.low-stock-notification');
    if (existingNotification) {
        existingNotification.remove();
    }
    
    const notification = document.createElement('div');
    notification.className = 'low-stock-notification';
    notification.textContent = message;
    
    // Set styles based on type
    const colors = {
        info: '#3b82f6',
        success: '#10b981',
        warning: '#f59e0b',
        error: '#ef4444'
    };
    
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${colors[type] || colors.info};
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 10000;
        font-size: 14px;
        font-weight: 500;
        max-width: 300px;
        animation: slideIn 0.3s ease-out;
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 300);
    }, 3000);
}

// Add notification animations
const notificationStyle = document.createElement('style');
notificationStyle.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(notificationStyle);
