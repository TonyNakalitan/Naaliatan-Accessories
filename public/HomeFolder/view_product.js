/**
 * Naaliatan Accessories - Product View JavaScript
 * Enhanced & Cleaned Implementation
 */

(function() {
    'use strict';

    // ============================================
    // Configuration & Constants
    // ============================================
    const CONFIG = {
        debounceDelay: 300,
        animationDuration: 250,
        toastDuration: 3500
    };

    // ============================================
    // DOM Ready Handler
    // ============================================
    document.addEventListener('DOMContentLoaded', function() {
        initStickyNavbar();
        initMobileMenu();
        initSearchBox();
        initFilterChips();
        initProductCards();
        initModals();
        initQuantityInputs();
        initSortFunctionality();
        initPagination();
        initAccessibility();
    });

    // ============================================
    // Sticky Navbar with Scroll Effects
    // ============================================
    function initStickyNavbar() {
        const topNav = document.querySelector('.top-nav');
        const pageContainer = document.querySelector('.page-container');
        
        if (!topNav || !pageContainer) return;
        
        let lastScrollTop = 0;
        const scrollThreshold = 50;
        
        // Handle scroll events
        pageContainer.addEventListener('scroll', function() {
            const scrollTop = pageContainer.scrollTop;
            
            // Add/remove scrolled class based on scroll position
            if (scrollTop > scrollThreshold) {
                topNav.classList.add('scrolled');
            } else {
                topNav.classList.remove('scrolled');
            }
            
            lastScrollTop = scrollTop;
        });
        
        // Handle window scroll as well (for full page scrolling)
        window.addEventListener('scroll', function() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            // Add/remove scrolled class based on scroll position
            if (scrollTop > scrollThreshold) {
                topNav.classList.add('scrolled');
            } else {
                topNav.classList.remove('scrolled');
            }
            
            lastScrollTop = scrollTop;
        });
        
        // Initial check
        const initialScroll = window.pageYOffset || document.documentElement.scrollTop;
        if (initialScroll > scrollThreshold) {
            topNav.classList.add('scrolled');
        }
    }

    // ============================================
    // Mobile Menu
    // ============================================
    function initMobileMenu() {
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mainNav = document.getElementById('mainNav');

        if (!mobileMenuBtn || !mainNav) return;

        mobileMenuBtn.addEventListener('click', function() {
            const isActive = mainNav.classList.toggle('active');
            const icon = this.querySelector('i');

            // Update ARIA attributes
            this.setAttribute('aria-expanded', isActive);

            // Toggle icon
            if (isActive) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });

        // Close menu when clicking on a nav link
        mainNav.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', closeMobileMenu);
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (mainNav.classList.contains('active') &&
                !mainNav.contains(e.target) &&
                !mobileMenuBtn.contains(e.target)) {
                closeMobileMenu();
            }
        });

        function closeMobileMenu() {
            mainNav.classList.remove('active');
            mobileMenuBtn.setAttribute('aria-expanded', 'false');
            const icon = mobileMenuBtn.querySelector('i');
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        }
    }

    // ============================================
    // Search Box
    // ============================================
    function initSearchBox() {
        const searchInput = document.getElementById('productSearch');
        const searchClear = document.getElementById('searchClear');
        const productCards = document.querySelectorAll('.product-card');

        if (!searchInput) return;

        // Search functionality
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();

            // Show/hide clear button
            if (searchClear) {
                searchClear.style.display = searchTerm ? 'flex' : 'none';
            }

            filterProducts();
            checkEmptyState();
        });

        // Clear search
        if (searchClear) {
            searchClear.addEventListener('click', function() {
                searchInput.value = '';
                this.style.display = 'none';
                searchInput.focus();
                filterProducts();
                checkEmptyState();
            });
        }

        // Filter products based on search
        function filterProducts() {
            const searchTerm = searchInput.value.toLowerCase().trim();

            productCards.forEach(card => {
                const name = card.querySelector('.product-name');
                const code = card.querySelector('.product-code');

                if (!name || !code) return;

                const nameText = name.textContent.toLowerCase();
                const codeText = code.textContent.toLowerCase();

                const matches = nameText.includes(searchTerm) || codeText.includes(searchTerm);
                card.style.display = matches ? '' : 'none';
            });
        }
    }

    // ============================================
    // Filter Chips
    // ============================================
    function initFilterChips() {
        const filterChips = document.querySelectorAll('.filter-chip');
        const productCards = document.querySelectorAll('.product-card');

        if (!filterChips.length) return;

        filterChips.forEach(chip => {
            chip.addEventListener('click', function() {
                // Update active state
                filterChips.forEach(c => {
                    c.classList.remove('active');
                    c.setAttribute('aria-selected', 'false');
                });
                this.classList.add('active');
                this.setAttribute('aria-selected', 'true');

                // Filter products
                const category = this.getAttribute('data-category');

                productCards.forEach(card => {
                    if (category === 'all' || card.getAttribute('data-category') === category) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });

                checkEmptyState();
            });
        });
    }

    // ============================================
    // Product Cards
    // ============================================
    function initProductCards() {
        // Add to Cart buttons
        document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const productId = this.getAttribute('data-product-id');
                const productName = this.getAttribute('data-product-name');
                openAddToCartModal(productId, productName);
            });
        });

        // Buy Now buttons
        document.querySelectorAll('.buy-now-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const productId = this.getAttribute('data-product-id');
                const productName = this.getAttribute('data-product-name');
                openBuyNowModal(productId, productName);
            });
        });

        // Quick Add to Cart buttons (in overlay)
        document.querySelectorAll('.btn-quick-cart').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const productId = this.getAttribute('data-product-id');
                const productName = this.getAttribute('data-product-name');
                quickAddToCart(productId, productName);
            });
        });

        // View Details buttons (in overlay)
        document.querySelectorAll('.btn-view').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                // Could open a quick view modal or navigate to product page
                const card = this.closest('.product-card');
                if (card) {
                    const name = card.querySelector('.product-name').textContent;
                    showToast(`Viewing details for ${name}`, 'info');
                }
            });
        });

        // Keyboard navigation for product cards
        document.querySelectorAll('.product-card').forEach(card => {
            card.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    // Open product details or focus first action button
                    const firstBtn = this.querySelector('.btn-cart, .add-to-cart-btn');
                    if (firstBtn) firstBtn.focus();
                }
            });
        });
    }

    // ============================================
    // Quick Add to Cart (without modal)
    // ============================================
    function quickAddToCart(productId, productName) {
        // Show loading state
        showToast(`Adding ${productName} to cart...`, 'info');

        // Determine the correct route
        const currentPath = window.location.pathname;
        const isAdmin = currentPath.includes('/admin/');
        const isStaff = currentPath.includes('/staff/');
        const cartAddUrl = isAdmin ? `/admin/cart/add/${productId}` :
            (isStaff ? `/staff/cart/add/${productId}` : `/api/cart/add/${productId}`);

        // Send request
        fetch(cartAddUrl, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ quantity: 1 })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message || `Added ${productName} to cart!`, 'success');
                    updateCartCount(data.cartCount);
                } else {
                    showToast(data.message || 'Error adding to cart', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Something went wrong. Please try again.', 'error');
            });
    }

    // ============================================
    // Modals
    // ============================================
    function initModals() {
        // Close modal on overlay click
        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', function() {
                const modal = this.closest('.modal');
                if (modal) {
                    closeModal(modal);
                }
            });
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const openModal = document.querySelector('.modal.show');
                if (openModal) {
                    closeModal(openModal);
                }
            }
        });

        // Add to Cart form submission
        const addToCartForm = document.getElementById('addToCartForm');
        if (addToCartForm) {
            addToCartForm.addEventListener('submit', function(e) {
                e.preventDefault();
                submitAddToCart(this);
            });
        }

        // Buy Now form submission
        const buyNowForm = document.getElementById('buyNowForm');
        if (buyNowForm) {
            buyNowForm.addEventListener('submit', function(e) {
                e.preventDefault();
                submitBuyNow(this);
            });
        }
    }

    function openAddToCartModal(productId, productName) {
        const modal = document.getElementById('addToCartModal');
        const productIdInput = document.getElementById('cartProductId');
        const productNameInput = document.getElementById('cartProductName');
        const productDisplay = document.getElementById('cartProductDisplay');
        const quantityInput = document.getElementById('cartQuantity');

        if (!modal) return;

        if (productIdInput) productIdInput.value = productId;
        if (productNameInput) productNameInput.value = productName;
        if (productDisplay) productDisplay.textContent = productName;
        if (quantityInput) quantityInput.value = 1;

        showModal(modal);
    }

    function closeAddToCartModal() {
        const modal = document.getElementById('addToCartModal');
        const form = document.getElementById('addToCartForm');

        if (modal) closeModal(modal);
        if (form) form.reset();
    }

    function openBuyNowModal(productId, productName) {
        const modal = document.getElementById('buyNowModal');
        const productIdInput = document.getElementById('modalProductId');
        const productNameInput = document.getElementById('modalProductName');
        const productDisplay = document.getElementById('modalProductDisplay');
        const quantityInput = document.getElementById('quantity');

        if (!modal) return;

        if (productIdInput) productIdInput.value = productId;
        if (productNameInput) productNameInput.value = productName;
        if (productDisplay) productDisplay.textContent = productName;
        if (quantityInput) quantityInput.value = 1;

        // Pre-fill user data if logged in
        prefillUserData();

        showModal(modal);
    }

    function closeBuyNowModal() {
        const modal = document.getElementById('buyNowModal');
        const form = document.getElementById('buyNowForm');

        if (modal) closeModal(modal);
        if (form) form.reset();
    }

    function showModal(modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';

        // Focus first input
        setTimeout(() => {
            const firstInput = modal.querySelector('input:not([type="hidden"]), textarea');
            if (firstInput) firstInput.focus();
        }, 100);
    }

    function closeModal(modal) {
        modal.classList.remove('show');
        document.body.style.overflow = '';
    }

    function submitAddToCart(form) {
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalContent = submitBtn.innerHTML;
        const productId = form.querySelector('#cartProductId')?.value;
        const productName = form.querySelector('#cartProductName')?.value;
        const quantity = form.querySelector('#cartQuantity')?.value || 1;

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Adding...</span>';

        // Determine the correct route
        const currentPath = window.location.pathname;
        const isAdmin = currentPath.includes('/admin/');
        const isStaff = currentPath.includes('/staff/');
        const cartAddUrl = isAdmin ? `/admin/cart/add/${productId}` :
            (isStaff ? `/staff/cart/add/${productId}` : `/api/cart/add/${productId}`);

        const payload = { quantity: parseInt(quantity) || 1 };

        fetch(cartAddUrl, {
            method: 'POST',
            body: JSON.stringify(payload),
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.json())
            .then(data => {
                closeAddToCartModal();
                if (data.success) {
                    showToast(data.message || `Added ${quantity}x ${productName} to cart!`, 'success');
                    updateCartCount(data.cartCount);
                } else {
                    showToast(data.message || 'Error adding to cart', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Something went wrong. Please try again.', 'error');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalContent;
            });
    }

    function submitBuyNow(form) {
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalContent = submitBtn.innerHTML;
        const productId = form.querySelector('#modalProductId')?.value;
        const productName = form.querySelector('#modalProductName')?.value;

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Processing...</span>';

        // Determine the correct route
        const currentPath = window.location.pathname;
        const isAdmin = currentPath.includes('/admin/');
        const isStaff = currentPath.includes('/staff/');
        const buyNowUrl = isAdmin ? '/admin/buy-now' :
            (isStaff ? '/staff/buy-now' : '/buy-now');

        const payload = {
            productId: productId,
            quantity: parseInt(form.querySelector('#quantity')?.value || 1),
            customer: {
                name: form.querySelector('#customerName')?.value,
                email: form.querySelector('#customerEmail')?.value,
                phone: form.querySelector('#customerPhone')?.value,
                address: form.querySelector('#customerAddress')?.value
            }
        };

        fetch(buyNowUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(payload)
        })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                closeBuyNowModal();
                showToast(data.message || `Order placed successfully for ${productName}!`, 'success');
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Failed to process order. Please try again.', 'error');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalContent;
            });
    }

    function prefillUserData() {
        // If user is logged in, pre-fill their data (would need to be passed from backend)
        // This is a placeholder for future implementation
    }

    // ============================================
    // Quantity Input Controls
    // ============================================
    function initQuantityInputs() {
        document.querySelectorAll('.quantity-input-wrapper').forEach(wrapper => {
            const minusBtn = wrapper.querySelector('.quantity-minus');
            const plusBtn = wrapper.querySelector('.quantity-plus');
            const input = wrapper.querySelector('.quantity-input');

            if (!minusBtn || !plusBtn || !input) return;

            minusBtn.addEventListener('click', function() {
                const currentVal = parseInt(input.value) || 1;
                if (currentVal > 1) {
                    input.value = currentVal - 1;
                    input.dispatchEvent(new Event('change'));
                }
            });

            plusBtn.addEventListener('click', function() {
                const currentVal = parseInt(input.value) || 1;
                const max = parseInt(input.getAttribute('max')) || Infinity;
                if (currentVal < max) {
                    input.value = currentVal + 1;
                    input.dispatchEvent(new Event('change'));
                }
            });

            // Prevent non-numeric input
            input.addEventListener('keypress', function(e) {
                if (!/[0-9]/.test(e.key)) {
                    e.preventDefault();
                }
            });
        });
    }

    // ============================================
    // Sort Functionality
    // ============================================
    function initSortFunctionality() {
        const sortSelect = document.getElementById('productSort');
        const productGrid = document.getElementById('productGrid');

        if (!sortSelect || !productGrid) return;

        sortSelect.addEventListener('change', function() {
            const sortBy = this.value;
            sortProducts(sortBy);
        });
    }

    function sortProducts(sortBy) {
        const productGrid = document.getElementById('productGrid');
        const products = Array.from(productGrid.querySelectorAll('.product-card'));

        products.sort((a, b) => {
            switch (sortBy) {
                case 'newest':
                    return 0; // Default order
                case 'price_low':
                    return extractPrice(a) - extractPrice(b);
                case 'price_high':
                    return extractPrice(b) - extractPrice(a);
                case 'name':
                    const nameA = a.querySelector('.product-name').textContent.toLowerCase();
                    const nameB = b.querySelector('.product-name').textContent.toLowerCase();
                    return nameA.localeCompare(nameB);
                default:
                    return 0;
            }
        });

        // Re-append sorted products
        products.forEach(product => productGrid.appendChild(product));
    }

    function extractPrice(productCard) {
        const priceElement = productCard.querySelector('.product-price');
        if (!priceElement) return 0;
        
        const priceText = priceElement.textContent;
        const priceMatch = priceText.match(/[\d,]+\.?\d*/);
        return priceMatch ? parseFloat(priceMatch[0].replace(',', '')) : 0;
    }

    // ============================================
    // Pagination Functionality
    // ============================================
    function initPagination() {
        const paginationLinks = document.querySelectorAll('.pagination-link:not(.disabled)');
        
        paginationLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                // Only handle client-side navigation for search/filter scenarios
                const hasActiveFilters = document.querySelector('.filter-chip.active:not([data-category="all"])') ||
                                     document.getElementById('productSearch').value.trim();
                
                if (hasActiveFilters) {
                    e.preventDefault();
                    const page = this.getAttribute('data-page') || 
                                this.textContent.trim();
                    handleClientSidePagination(page);
                }
            });
        });
    }

    function handleClientSidePagination(page) {
        const productGrid = document.getElementById('productGrid');
        const productCards = Array.from(productGrid.querySelectorAll('.product-card'));
        const itemsPerPage = 4;
        const startIndex = (page - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        
        // Hide all cards first
        productCards.forEach(card => {
            card.style.display = 'none';
        });
        
        // Show only cards for current page
        const visibleCards = productCards.filter((card, index) => {
            const cardDisplay = card.style.display !== 'none';
            if (cardDisplay) {
                card.style.display = 'none'; // Hide temporarily for pagination
            }
            return cardDisplay;
        });
        
        const pageCards = visibleCards.slice(startIndex, endIndex);
        pageCards.forEach(card => {
            card.style.display = '';
        });
        
        // Update pagination info
        updatePaginationInfo(page, visibleCards.length, itemsPerPage);
        
        // Update pagination links
        updatePaginationLinks(page, Math.ceil(visibleCards.length / itemsPerPage));
        
        // Scroll to top of products
        productGrid.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function updatePaginationInfo(currentPage, totalItems, itemsPerPage) {
        const paginationText = document.querySelector('.pagination-text');
        if (paginationText) {
            const startItem = (currentPage - 1) * itemsPerPage + 1;
            const endItem = Math.min(currentPage * itemsPerPage, totalItems);
            paginationText.textContent = `Showing ${startItem}-${endItem} of ${totalItems} products`;
        }
    }

    function updatePaginationLinks(currentPage, totalPages) {
        const pagination = document.querySelector('.pagination');
        if (!pagination) return;
        
        // Get current URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const hasFilters = urlParams.toString();
        
        // Update or create pagination links
        const paginationItems = pagination.querySelectorAll('.pagination-item');
        
        paginationItems.forEach((item, index) => {
            const link = item.querySelector('.pagination-link');
            if (!link) return;
            
            // Skip prev/next buttons for now
            if (link.classList.contains('pagination-prev') || link.classList.contains('pagination-next')) {
                return;
            }
            
            const pageNum = parseInt(link.textContent.trim());
            if (!isNaN(pageNum)) {
                // Update active state
                if (pageNum === currentPage) {
                    link.classList.add('active');
                    link.setAttribute('aria-current', 'page');
                } else {
                    link.classList.remove('active');
                    link.removeAttribute('aria-current');
                }
                
                // Update href for client-side navigation
                if (hasFilters) {
                    link.setAttribute('data-page', pageNum);
                }
            }
        });
        
        // Update prev/next buttons
        const prevBtn = pagination.querySelector('.pagination-prev');
        const nextBtn = pagination.querySelector('.pagination-next');
        
        if (prevBtn) {
            if (currentPage === 1) {
                prevBtn.classList.add('disabled');
                prevBtn.setAttribute('aria-disabled', 'true');
            } else {
                prevBtn.classList.remove('disabled');
                prevBtn.removeAttribute('aria-disabled');
                if (hasFilters) {
                    prevBtn.setAttribute('data-page', currentPage - 1);
                }
            }
        }
        
        if (nextBtn) {
            if (currentPage === totalPages) {
                nextBtn.classList.add('disabled');
                nextBtn.setAttribute('aria-disabled', 'true');
            } else {
                nextBtn.classList.remove('disabled');
                nextBtn.removeAttribute('aria-disabled');
                if (hasFilters) {
                    nextBtn.setAttribute('data-page', currentPage + 1);
                }
            }
        }
    }

    // ============================================
    // Accessibility Enhancements
    // ============================================
    function initAccessibility() {
        // Add ARIA labels to filter chips
        document.querySelectorAll('.filter-chip').forEach(chip => {
            const label = chip.querySelector('span').textContent;
            chip.setAttribute('aria-label', `Filter by ${label}`);
        });

        // Add keyboard navigation to filter chips
        document.querySelectorAll('.filter-chip').forEach(chip => {
            chip.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.click();
                }
            });
        });

        // Add focus management for modals
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('show', function() {
                const firstInput = this.querySelector('input, textarea, button');
                if (firstInput) firstInput.focus();
            });
        });
    }

    // ============================================
    // Utility Functions
    // ============================================

    function checkEmptyState() {
        const productGrid = document.getElementById('productGrid');
        const productCards = document.querySelectorAll('.product-card');
        const visibleCards = Array.from(productCards).filter(card => card.style.display !== 'none');

        if (visibleCards.length === 0) {
            if (productGrid) productGrid.style.display = 'none';
            showEmptyMessage();
        } else {
            if (productGrid) productGrid.style.display = 'grid';
            hideEmptyMessage();
        }
    }

    function showEmptyMessage() {
        let emptyState = document.querySelector('.empty-state');

        if (!emptyState) {
            // Create empty state if it doesn't exist
            emptyState = document.createElement('div');
            emptyState.className = 'empty-state';
            emptyState.setAttribute('role', 'alert');
            emptyState.innerHTML = `
                <div class="empty-icon-wrapper">
                    <i class="fas fa-search" aria-hidden="true"></i>
                    <div class="empty-icon-pulse"></div>
                </div>
                <h3 class="empty-title">No Products Found</h3>
                <p class="empty-text">Try adjusting your search or filter to find what you're looking for.</p>
            `;

            const productsMain = document.querySelector('.products-main');
            if (productsMain) {
                productsMain.appendChild(emptyState);
            }
        }

        emptyState.style.display = 'flex';
    }

    function hideEmptyMessage() {
        const emptyState = document.querySelector('.products-main > .empty-state');
        if (emptyState) {
            emptyState.style.display = 'none';
        }
    }

    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toastMessage');

        if (!toast || !toastMessage) return;

        // Remove existing type classes
        toast.classList.remove('toast-success', 'toast-info', 'toast-warning', 'toast-error');

        // Set icon based on type
        const icon = toast.querySelector('i');
        if (icon) {
            icon.className = 'fas';
            switch (type) {
                case 'success':
                    icon.classList.add('fa-check-circle');
                    break;
                case 'error':
                    icon.classList.add('fa-times-circle');
                    break;
                case 'warning':
                    icon.classList.add('fa-exclamation-triangle');
                    break;
                case 'info':
                    icon.classList.add('fa-info-circle');
                    break;
            }
        }

        // Add type class
        toast.classList.add(`toast-${type}`);

        // Set message
        toastMessage.textContent = message;

        // Show toast
        toast.classList.add('show');

        // Hide after delay
        clearTimeout(toast._timeout);
        toast._timeout = setTimeout(() => {
            toast.classList.remove('show');
        }, 3500);
    }

    function updateCartCount(count) {
        if (count === undefined) return;

        const cartCountBadge = document.querySelector('.cart-count-badge');
        if (cartCountBadge) {
            cartCountBadge.textContent = count;
            cartCountBadge.classList.add('pulse');
            setTimeout(() => cartCountBadge.classList.remove('pulse'), 500);
        }
    }

    // Make functions globally available for inline onclick handlers
    window.closeAddToCartModal = closeAddToCartModal;
    window.closeBuyNowModal = closeBuyNowModal;

})();