$(document).ready(function() {
    // Initialize product cards functionality
    initializeProductCards();
});

function initializeProductCards() {
    const productsGrid = document.getElementById('productsGrid');
    if (!productsGrid) return;
    
    // Real-time search filter - now triggers server-side pagination
    const globalSearch = document.getElementById('globalSearch');
    if (globalSearch) {
        globalSearch.addEventListener('input', debounce(function() {
            navigateWithFilters(1);
        }, 500));
    }
    
    // Status Filter Logic - now triggers server-side pagination
    const statusFilter = document.getElementById('statusFilter');
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            navigateWithFilters(1);
        });
    }
    
    // Handle pagination clicks
    document.addEventListener('click', function(e) {
        if (e.target.closest('.pagination-btn') && !e.target.closest('.pagination-btn').classList.contains('active')) {
            e.preventDefault();
            const page = e.target.closest('.pagination-btn').getAttribute('href').match(/page=(\d+)/);
            if (page) {
                navigateWithFilters(parseInt(page[1]));
            }
        }
    });
}

function navigateWithFilters(page) {
    const globalSearch = document.getElementById('globalSearch');
    const statusFilter = document.getElementById('statusFilter');
    
    // Get current URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    
    // Update parameters
    urlParams.set('page', page);
    
    // Add search parameter if exists
    if (globalSearch && globalSearch.value.trim()) {
        urlParams.set('search', globalSearch.value.trim());
    } else {
        urlParams.delete('search');
    }
    
    // Add status parameter if not 'all'
    if (statusFilter && statusFilter.value !== 'all') {
        urlParams.set('status', statusFilter.value);
    } else {
        urlParams.delete('status');
    }
    
    // Navigate to new URL
    window.location.href = window.location.pathname + '?' + urlParams.toString();
}

// Debounce function to limit how often a function can be called
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Delete product function
function deleteProduct(url, productName, token) {
    if (confirm('Are you sure you want to delete "' + productName + '"?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = token;
        
        form.appendChild(tokenInput);
        document.body.appendChild(form);
        form.submit();
    }
}
