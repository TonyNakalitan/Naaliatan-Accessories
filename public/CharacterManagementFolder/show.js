// Character View Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Add fade-in animation to sections
    const sections = document.querySelectorAll('.glass-panel');
    sections.forEach((section, index) => {
        setTimeout(() => {
            section.classList.add('fade-in');
        }, index * 100);
    });

    // Add hover effect to action buttons
    const actionButtons = document.querySelectorAll('a[href*="edit"], button[type="submit"]');
    actionButtons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Confirm delete action
    const deleteForm = document.querySelector('form[action*="delete"]');
    if (deleteForm) {
        deleteForm.addEventListener('submit', function(e) {
            const characterName = document.querySelector('h1').textContent.trim();
            const confirmed = confirm(`Are you sure you want to delete "${characterName}"?\n\nThis action cannot be undone and will also delete all associated products.`);
            
            if (!confirmed) {
                e.preventDefault();
            } else {
                // Show loading indicator
                showLoadingIndicator();
            }
        });
    }

    // Copy color code to clipboard
    const colorCodeElement = document.querySelector('.font-mono');
    if (colorCodeElement) {
        colorCodeElement.style.cursor = 'pointer';
        colorCodeElement.title = 'Click to copy color code';
        
        colorCodeElement.addEventListener('click', function() {
            const colorCode = this.textContent.trim();
            
            // Copy to clipboard
            if (navigator.clipboard) {
                navigator.clipboard.writeText(colorCode).then(() => {
                    showNotification('Color code copied to clipboard!', 'success');
                }).catch(() => {
                    fallbackCopyToClipboard(colorCode);
                });
            } else {
                fallbackCopyToClipboard(colorCode);
            }
        });
    }
});

// Fallback copy method for older browsers
function fallbackCopyToClipboard(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    document.body.appendChild(textArea);
    textArea.select();
    
    try {
        document.execCommand('copy');
        showNotification('Color code copied to clipboard!', 'success');
    } catch (err) {
        showNotification('Failed to copy color code', 'error');
    }
    
    document.body.removeChild(textArea);
}

// Show loading indicator
function showLoadingIndicator() {
    const loader = document.createElement('div');
    loader.id = 'deleteLoader';
    loader.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    `;
    
    loader.innerHTML = `
        <div style="background: white; padding: 2rem; border-radius: 1rem; text-align: center;">
            <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #4f46e5; margin-bottom: 1rem;"></i>
            <p style="font-weight: 600; color: #1e293b;">Deleting character...</p>
        </div>
    `;
    
    document.body.appendChild(loader);
}

// Notification system
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#4f46e5'};
        color: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 9999;
        font-weight: 600;
        animation: slideIn 0.3s ease-out;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    `;
    
    const icon = type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle';
    notification.innerHTML = `<i class="fas ${icon}"></i> ${message}`;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
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
document.head.appendChild(style);
