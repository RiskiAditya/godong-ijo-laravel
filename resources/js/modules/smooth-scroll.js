/**
 * Smooth Scroll Module
 * 
 * Requirements:
 * - 9.1: Smooth scrolling for anchor links with 500ms duration
 * - 9.2: Account for fixed navbar offset (80px)
 * - 9.3: Support cross-page navigation with hash fragments
 * - 9.4: Disable smooth scroll for users with prefers-reduced-motion
 * - 9.5: Back-to-top button with smooth scroll
 */

export function initSmoothScrolling() {
    // Check if user prefers reduced motion
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    
    // Fixed navbar height offset
    const navbarOffset = 80;
    
    /**
     * Smooth scroll to element
     * 
     * @param {HTMLElement} target Target element to scroll to
     * @param {number} duration Scroll duration in milliseconds
     */
    function smoothScrollTo(target, duration = 500) {
        if (!target) return;
        
        const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - navbarOffset;
        const startPosition = window.pageYOffset;
        const distance = targetPosition - startPosition;
        
        // If user prefers reduced motion, scroll instantly
        if (prefersReducedMotion) {
            window.scrollTo(0, targetPosition);
            return;
        }
        
        let startTime = null;
        
        function animation(currentTime) {
            if (startTime === null) startTime = currentTime;
            const timeElapsed = currentTime - startTime;
            const progress = Math.min(timeElapsed / duration, 1);
            
            // Ease-in-out easing function
            const ease = progress < 0.5 
                ? 2 * progress * progress 
                : 1 - Math.pow(-2 * progress + 2, 2) / 2;
            
            window.scrollTo(0, startPosition + (distance * ease));
            
            if (timeElapsed < duration) {
                requestAnimationFrame(animation);
            }
        }
        
        requestAnimationFrame(animation);
    }
    
    /**
     * Handle anchor link clicks
     */
    function handleAnchorLinks() {
        const anchorLinks = document.querySelectorAll('a[href^="#"]:not([href="#"])');
        
        anchorLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    e.preventDefault();
                    smoothScrollTo(targetElement);
                    
                    // Update URL hash without jumping
                    if (history.pushState) {
                        history.pushState(null, null, targetId);
                    } else {
                        window.location.hash = targetId;
                    }
                    
                    // Set focus on target element for accessibility
                    targetElement.setAttribute('tabindex', '-1');
                    targetElement.focus();
                }
            });
        });
    }
    
    /**
     * Handle cross-page navigation with hash fragments
     * If URL has hash on page load, scroll to that element
     */
    function handleInitialHashScroll() {
        if (window.location.hash) {
            const targetElement = document.querySelector(window.location.hash);
            
            if (targetElement) {
                // Wait for page to fully load before scrolling
                setTimeout(() => {
                    smoothScrollTo(targetElement);
                }, 100);
            }
        }
    }
    
    /**
     * Create and manage back-to-top button
     */
    function createBackToTopButton() {
        // Check if button already exists
        if (document.getElementById('back-to-top-btn')) return;
        
        // Create button element
        const button = document.createElement('button');
        button.id = 'back-to-top-btn';
        button.className = 'back-to-top-btn';
        button.setAttribute('aria-label', 'Kembali ke atas');
        button.innerHTML = `
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="18 15 12 9 6 15"></polyline>
            </svg>
        `;
        
        document.body.appendChild(button);
        
        // Show/hide button based on scroll position
        function toggleButtonVisibility() {
            if (window.pageYOffset > 300) {
                button.classList.add('visible');
            } else {
                button.classList.remove('visible');
            }
        }
        
        // Handle button click
        button.addEventListener('click', function() {
            if (prefersReducedMotion) {
                window.scrollTo(0, 0);
            } else {
                smoothScrollTo(document.body, 500);
            }
        });
        
        // Listen to scroll events
        window.addEventListener('scroll', toggleButtonVisibility);
        
        // Initial check
        toggleButtonVisibility();
    }
    
    // Initialize
    handleAnchorLinks();
    handleInitialHashScroll();
    createBackToTopButton();
}

// Auto-initialize if DOM is already loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSmoothScrolling);
} else {
    initSmoothScrolling();
}
