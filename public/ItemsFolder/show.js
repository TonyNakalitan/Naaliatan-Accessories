// Add to Cart functionality - Direct form submission
document.querySelectorAll('.add-to-cart-btn:not(.disabled)').forEach(btn => {
    btn.addEventListener('click', function() {
        const productId = this.getAttribute('data-product-id');
        const productName = this.getAttribute('data-product-name');

        // Determine the correct route based on current path
        const currentPath = window.location.pathname;
        const isAdmin = currentPath.includes('/admin/');
        const cartAddUrl = isAdmin ? `/admin/cart/add/${productId}` : `/staff/cart/add/${productId}`;

        // Create a form and submit it directly
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = cartAddUrl;

        const quantityInput = document.createElement('input');
        quantityInput.type = 'hidden';
        quantityInput.name = 'quantity';
        quantityInput.value = '1';

        form.appendChild(quantityInput);
        document.body.appendChild(form);
        form.submit();
    });
});

// Buy Now button handler
document.addEventListener('DOMContentLoaded', function() {
    // Handle Buy Now button clicks
    document.querySelectorAll('.buy-now-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const productName = this.getAttribute('data-product-name');
            const action = this.getAttribute('data-action');
            const stock = parseInt(this.getAttribute('data-stock'));

            // For Buy Now, redirect to order page or handle differently
            // This could be modified to go directly to checkout
            const currentPath = window.location.pathname;
            const isAdmin = currentPath.includes('/admin/');
            const cartAddUrl = isAdmin ? `/admin/cart/add/${productId}` : `/staff/cart/add/${productId}`;

            // Create a form and submit it directly
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = cartAddUrl;

            const quantityInput = document.createElement('input');
            quantityInput.type = 'hidden';
            quantityInput.name = 'quantity';
            quantityInput.value = '1';

            form.appendChild(quantityInput);
            document.body.appendChild(form);
            form.submit();
        });
    });
});
