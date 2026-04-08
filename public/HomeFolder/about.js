// About Page JavaScript - Interactive Features

document.addEventListener('DOMContentLoaded', function() {
    // Initialize sticky navbar
    initStickyNavbar();
    
    // Initialize scroll animations
    initScrollAnimations();
    
    // Initialize counter animations
    initCounterAnimations();
    
    // Initialize typing effect
    initTypingEffect();
});

// Sticky Navbar with Scroll Effects
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

// Initialize Scroll Animations
function initScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe elements for scroll animations
    const animatedElements = document.querySelectorAll(
        '.hero-section, .stats-overview, .about-content-section, .features-section, .team-section, .quick-actions-panel, .cta-section'
    );
    
    animatedElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
}

// Initialize Counter Animations
function initCounterAnimations() {
    // Add hover effects to cards
    document.querySelectorAll('.stat-card, .feature-card, .team-card, .about-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Counter animation for stats
    function animateCounter(element, target, duration = 2000) {
        const start = 0;
        const increment = target / (duration / 16);
        let current = start;
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            
            if (element.textContent.includes('K+')) {
                element.textContent = Math.floor(current / 1000) + 'K+';
            } else if (element.textContent.includes('%')) {
                element.textContent = Math.floor(current) + '%';
            } else if (element.textContent.includes('+')) {
                element.textContent = Math.floor(current) + '+';
            } else {
                element.textContent = Math.floor(current);
            }
        }, 16);
    }

    // Initialize counter animations when stats section is visible
    const statsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !entry.target.classList.contains('animated')) {
                entry.target.classList.add('animated');
                const valueElement = entry.target.querySelector('.value');
                if (valueElement) {
                    const text = valueElement.textContent;
                    let target = 0;
                    
                    if (text.includes('K+')) {
                        target = parseInt(text) * 1000;
                    } else if (text.includes('%')) {
                        target = parseInt(text);
                    } else if (text.includes('+')) {
                        target = parseInt(text);
                    }
                    
                    if (target > 0) {
                        animateCounter(valueElement, target);
                    }
                }
            }
        });
    }, { threshold: 0.5 });

    document.querySelectorAll('.stat-card').forEach(card => {
        statsObserver.observe(card);
    });
}

// Initialize Typing Effect
function initTypingEffect() {
    // Add typing effect to hero title
    function typeWriter(element, text, speed = 100) {
        let i = 0;
        element.textContent = '';
        
        function type() {
            if (i < text.length) {
                element.textContent += text.charAt(i);
                i++;
                setTimeout(type, speed);
            }
        }
        
        type();
    }

    // Initialize typing effect
    const heroTitle = document.querySelector('.hero-title .gradient-text');
    if (heroTitle) {
        const text = heroTitle.textContent;
        typeWriter(heroTitle, text, 150);
    }
}

// Mobile Menu Toggle
const mobileMenuBtn = document.getElementById('mobileMenuBtn');
const mainNav = document.getElementById('mainNav');

if (mobileMenuBtn && mainNav) {
    mobileMenuBtn.addEventListener('click', () => {
        mainNav.classList.toggle('active');
        const icon = mobileMenuBtn.querySelector('i');
        if (icon) {
            icon.classList.toggle('fa-bars');
            icon.classList.toggle('fa-times');
        }
    });

    // Close mobile menu when clicking outside
    document.addEventListener('click', (e) => {
        if (!mobileMenuBtn.contains(e.target) && !mainNav.contains(e.target)) {
            mainNav.classList.remove('active');
            const icon = mobileMenuBtn.querySelector('i');
            if (icon) {
                icon.classList.add('fa-bars');
                icon.classList.remove('fa-times');
            }
        }
    });
}

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Add parallax effect to hero section
window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    const heroVisual = document.querySelector('.hero-visual');
    const heroIcon = document.querySelector('.hero-icon-main');
    
    if (heroVisual && heroIcon) {
        const speed = 0.5;
        heroIcon.style.transform = `translateY(${scrolled * speed}px)`;
    }
});