document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const contentArea = document.querySelector('.content-area');

    if (sidebarToggle && sidebar && sidebarOverlay) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            const isActive = sidebar.classList.contains('active');
            
            if (isActive) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });

        // Close sidebar when clicking on overlay
        sidebarOverlay.addEventListener('click', function() {
            closeSidebar();
        });

        // Close sidebar when clicking outside (for mobile)
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target) && !sidebarOverlay.contains(e.target)) {
                    closeSidebar();
                }
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                // On desktop, close sidebar when resizing up
                if (sidebar.classList.contains('active')) {
                    closeSidebar();
                }
            }
        });

        // Handle escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && sidebar.classList.contains('active')) {
                closeSidebar();
            }
        });
    }

    function openSidebar() {
        sidebar.classList.add('active');
        sidebarOverlay.classList.add('show');
        sidebarToggle.classList.add('active');
        document.body.style.overflow = 'hidden'; // Prevent body scroll
    }

    function closeSidebar() {
        sidebar.classList.remove('active');
        sidebarOverlay.classList.remove('show');
        sidebarToggle.classList.remove('active');
        document.body.style.overflow = ''; // Restore body scroll
    }

    // Handle navigation item clicks
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(item => {
        item.addEventListener('click', function(e) {
            // Remove active class from all items
            navItems.forEach(navItem => navItem.classList.remove('active'));
            // Add active class to clicked item
            this.classList.add('active');
            
            // Close sidebar after navigation on mobile
            if (window.innerWidth <= 768) {
                setTimeout(closeSidebar, 300);
            }
        });
    });
});
