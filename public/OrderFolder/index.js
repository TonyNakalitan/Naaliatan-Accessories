$(document).ready(function() {
    // Initialize card filtering functionality
    initializeCardFilters();
});

function initializeCardFilters() {
    const globalSearch = document.getElementById('globalSearch');
    const statusFilter = document.getElementById('statusFilter');
    const deliveryFilter = document.getElementById('deliveryFilter');
    const orderCards = document.querySelectorAll('.order-card');
    
    // Global search functionality
    if (globalSearch) {
        globalSearch.addEventListener('keyup', function() {
            filterCards();
        });
    }
    
    // Status Filter Logic
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            filterCards();
        });
    }
    
    // Delivery Filter Logic
    if (deliveryFilter) {
        deliveryFilter.addEventListener('change', function() {
            filterCards();
        });
    }
    
    // Initial filter to ensure cards are properly displayed
    filterCards();
}

function filterCards() {
    const globalSearch = document.getElementById('globalSearch');
    const statusFilter = document.getElementById('statusFilter');
    const deliveryFilter = document.getElementById('deliveryFilter');
    const orderCards = document.querySelectorAll('.order-card');
    const emptyState = document.querySelector('.empty-state-cards');
    
    const searchTerm = globalSearch ? globalSearch.value.toLowerCase() : '';
    const selectedStatus = statusFilter ? statusFilter.value.toLowerCase() : 'all';
    const selectedDelivery = deliveryFilter ? deliveryFilter.value.toLowerCase() : 'all';
    
    let visibleCards = 0;
    
    orderCards.forEach(card => {
        const cardText = card.textContent.toLowerCase();
        const cardStatus = card.getAttribute('data-status') ? card.getAttribute('data-status').toLowerCase() : '';
        const cardDelivery = card.getAttribute('data-delivery') ? card.getAttribute('data-delivery').toLowerCase() : '';
        
        // Check if card matches all filters
        const matchesSearch = searchTerm === '' || cardText.includes(searchTerm);
        const matchesStatus = selectedStatus === 'all' || cardStatus === selectedStatus;
        const matchesDelivery = selectedDelivery === 'all' || 
                               (selectedDelivery === 'courier' && cardDelivery.includes('courier')) ||
                               (selectedDelivery === 'pickup' && cardDelivery.includes('pickup')) ||
                               (selectedDelivery === 'all');
        
        if (matchesSearch && matchesStatus && matchesDelivery) {
            card.style.display = 'flex';
            visibleCards++;
        } else {
            card.style.display = 'none';
        }
    });
    
    // Show/hide empty state
    if (emptyState) {
        emptyState.style.display = visibleCards === 0 ? 'block' : 'none';
    }
}

// Add sorting functionality for cards
function sortCards(sortBy) {
    const cardsContainer = document.getElementById('orderCardsContainer');
    const orderCards = Array.from(document.querySelectorAll('.order-card'));
    
    orderCards.sort((a, b) => {
        let aValue, bValue;
        
        switch(sortBy) {
            case 'date':
                aValue = a.querySelector('.order-date').textContent;
                bValue = b.querySelector('.order-date').textContent;
                break;
            case 'order-number':
                aValue = a.querySelector('.order-number').textContent;
                bValue = b.querySelector('.order-number').textContent;
                break;
            case 'status':
                aValue = a.getAttribute('data-status');
                bValue = b.getAttribute('data-status');
                break;
            default:
                return 0;
        }
        
        if (sortBy === 'date') {
            // Parse dates for proper sorting
            const dateA = new Date(aValue);
            const dateB = new Date(bValue);
            return dateB - dateA; // Descending order (newest first)
        } else {
            return aValue.localeCompare(bValue);
        }
    });
    
    // Re-append sorted cards to container
    orderCards.forEach(card => {
        cardsContainer.appendChild(card);
    });
}

// Add click handler for sorting (optional enhancement)
document.addEventListener('DOMContentLoaded', function() {
    const orderNumberElements = document.querySelectorAll('.order-number');
    const orderDateElements = document.querySelectorAll('.order-date');
    
    // Make order numbers clickable for sorting
    orderNumberElements.forEach(element => {
        element.style.cursor = 'pointer';
        element.addEventListener('click', function(e) {
            e.preventDefault();
            sortCards('order-number');
        });
    });
    
    // Make dates clickable for sorting
    orderDateElements.forEach(element => {
        element.style.cursor = 'pointer';
        element.addEventListener('click', function(e) {
            e.preventDefault();
            sortCards('date');
        });
    });
});
