/**
 * Naaliatan's Accessories - Home Page JavaScript
 * Handles navigation, animations, and interactive elements
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initMobileMenu();
    initScrollEffects();
    initStatCounters();
    initIntersectionObserver();
});

/**
 * Mobile Menu Toggle
 * Handles the mobile navigation menu visibility
 */
function initMobileMenu() {
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mainNav = document.getElementById('mainNav');
    
    if (!mobileMenuBtn || !mainNav) return;
    
    mobileMenuBtn.addEventListener('click', function() {
        mainNav.classList.toggle('active');
        
        // Toggle icon between bars and times (X)
        const icon = mobileMenuBtn.querySelector('i');
        if (mainNav.classList.contains('active')) {
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-times');
            mobileMenuBtn.style.background = 'var(--primary)';
            mobileMenuBtn.style.color = 'white';
        } else {
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
            mobileMenuBtn.style.background = '';
            mobileMenuBtn.style.color = '';
        }
    });
    
    // Close menu when clicking outside
    document.addEventListener('click', function(e) {
        if (!mainNav.contains(e.target) && !mobileMenuBtn.contains(e.target)) {
            mainNav.classList.remove('active');
            const icon = mobileMenuBtn.querySelector('i');
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
            mobileMenuBtn.style.background = '';
            mobileMenuBtn.style.color = '';
        }
    });
    
    // Close menu when clicking a nav link
    const navLinks = mainNav.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 1024) {
                mainNav.classList.remove('active');
                const icon = mobileMenuBtn.querySelector('i');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
                mobileMenuBtn.style.background = '';
                mobileMenuBtn.style.color = '';
            }
        });
    });
}

/**
 * Scroll Effects
 * Handles navbar appearance on scroll and smooth scrolling
 */
function initScrollEffects() {
    const topNav = document.querySelector('.top-nav');
    const pageContainer = document.querySelector('.page-container');
    
    if (!topNav || !pageContainer) return;
    
    let lastScrollY = 0;
    let ticking = false;
    
    pageContainer.addEventListener('scroll', function() {
        if (!ticking) {
            window.requestAnimationFrame(function() {
                handleScroll(pageContainer.scrollTop);
                ticking = false;
            });
            ticking = true;
        }
    });
    
    function handleScroll(scrollY) {
        // Add/remove scrolled class based on scroll position
        if (scrollY > 50) {
            topNav.classList.add('scrolled');
        } else {
            topNav.classList.remove('scrolled');
        }
        lastScrollY = scrollY;
    }
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                e.preventDefault();
                pageContainer.scrollTo({
                    top: targetElement.offsetTop - 100,
                    behavior: 'smooth'
                });
            }
        });
    });
}

/**
 * Stat Counters Animation
 * Animates numbers counting up when they come into view
 */
function initStatCounters() {
    const statValues = document.querySelectorAll('.stat-info .value');
    
    if (statValues.length === 0) return;
    
    const animateCounter = (element) => {
        const target = parseInt(element.textContent.replace(/,/g, ''));
        const duration = 1500; // ms
        const step = target / (duration / 16); // 60fps
        let current = 0;
        
        const updateCounter = () => {
            current += step;
            if (current < target) {
                element.textContent = Math.floor(current).toLocaleString();
                requestAnimationFrame(updateCounter);
            } else {
                element.textContent = target.toLocaleString();
            }
        };
        
        updateCounter();
    };
    
    // Use Intersection Observer to trigger animation when visible
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !entry.target.classList.contains('animated')) {
                entry.target.classList.add('animated');
                animateCounter(entry.target);
            }
        });
    }, {
        threshold: 0.5
    });
    
    statValues.forEach(value => observer.observe(value));
}

/**
 * Intersection Observer for Scroll Animations
 * Triggers fade-in animations for elements as they come into view
 */
function initIntersectionObserver() {
    const animatedElements = document.querySelectorAll(
        '.hero-section, .stats-overview, .features-section, .quick-actions-panel, .cta-section'
    );
    
    if (animatedElements.length === 0) return;
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting && !entry.target.classList.contains('animate-fade-in')) {
                // Add staggered delay based on index
                setTimeout(() => {
                    entry.target.classList.add('animate-fade-in');
                }, index * 100);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });
    
    animatedElements.forEach(element => {
        observer.observe(element);
    });
    
    // Animate stat cards with staggered delay
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        card.style.opacity = '0';
        setTimeout(() => {
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            card.style.opacity = '1';
        }, 200 + (index * 100));
    });
    
    // Animate feature cards with staggered delay
    const featureCards = document.querySelectorAll('.feature-card');
    featureCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 300 + (index * 150));
    });
    
    // Animate action cards with staggered delay
    const actionCards = document.querySelectorAll('.action-card');
    actionCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 400 + (index * 100));
    });
}

/**
 * Parallax Effect for Hero Section
 * Creates a subtle parallax effect on the hero visual elements
 */
function initParallaxEffect() {
    const heroSection = document.querySelector('.hero-section');
    const heroVisual = document.querySelector('.hero-visual');
    
    if (!heroSection || !heroVisual) return;
    
    heroSection.addEventListener('mousemove', (e) => {
        const rect = heroSection.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        
        const centerX = rect.width / 2;
        const centerY = rect.height / 2;
        
        const rotateX = (y - centerY) / 50;
        const rotateY = (centerX - x) / 50;
        
        heroVisual.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
    });
    
    heroSection.addEventListener('mouseleave', () => {
        heroVisual.style.transform = 'perspective(1000px) rotateX(0) rotateY(0)';
    });
}

/**
 * Utility: Debounce function
 * Limits the rate at which a function can fire
 */
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

/**
 * Utility: Throttle function
 * Ensures a function is only called once per specified time period
 */
function throttle(func, limit) {
    let inThrottle;
    return function(...args) {
        if (!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// Export functions for external use if needed
window.NaaliatanHome = {
    initParallaxEffect,
    debounce,
    throttle
};