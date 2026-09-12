/**
 * Packages Continuous Scrolling Carousel
 * True infinite scroll with seamless looping - no gaps, no pauses
 */

class PackagesCarousel {
    constructor() {
        this.container = document.querySelector('.packages-carousel-container');
        this.wrapper = document.querySelector('.packages-carousel-wrapper');
        this.carousel = document.querySelector('.packages-carousel');
        
        // Try both button selectors for backward compatibility
        this.prevBtn = document.querySelector('.packages-carousel-container .carousel-nav-prev') || 
                       document.querySelector('.packages-carousel-container .carousel-prev');
        this.nextBtn = document.querySelector('.packages-carousel-container .carousel-nav-next') || 
                       document.querySelector('.packages-carousel-container .carousel-next');
        
        // Track current slide index
        this.currentIndex = 0;
        this.totalCards = 0;
        this.isTransitioning = false;
        
        this.init();
    }
    
    init() {
        if (!this.carousel) return;
        
        // Count the cards rendered by the landing page.
        const allCards = this.carousel.querySelectorAll('.package-card');
        this.totalCards = allCards.length;
        
        // Hide dots container if exists
        const dotsContainer = document.querySelector('#packages-dots');
        if (dotsContainer) dotsContainer.style.display = 'none';
        
        // Show navigation buttons if they exist
        if (this.prevBtn) this.prevBtn.style.display = 'flex';
        if (this.nextBtn) this.nextBtn.style.display = 'flex';
        
        // Enable native CSS animation for smooth infinite scroll
        // CSS handles the animation - we just provide manual controls
        this.carousel.style.animation = 'scroll-left 40s linear infinite';
        
        // Setup manual navigation
        this.setupManualNavigation();
        
        // Pause animation on hover
        this.setupHoverPause();
    }
    
    setupManualNavigation() {
        if (this.prevBtn) {
            this.prevBtn.addEventListener('click', () => {
                if (!this.isTransitioning) {
                    this.scrollToCard('prev');
                }
            });
        }
        
        if (this.nextBtn) {
            this.nextBtn.addEventListener('click', () => {
                if (!this.isTransitioning) {
                    this.scrollToCard('next');
                }
            });
        }
    }
    
    scrollToCard(direction) {
        if (!this.carousel || this.isTransitioning) return;
        
        this.isTransitioning = true;
        
        // Get card dimensions first
        const card = this.carousel.querySelector('.package-card');
        if (!card) {
            this.isTransitioning = false;
            return;
        }
        
        const cardWidth = card.offsetWidth;
        const gap = 24;
        const scrollAmount = cardWidth + gap;
        
        // Pause CSS animation immediately
        this.carousel.style.animationPlayState = 'paused';
        
        // Get ACTUAL current transform position from CSS animation
        const computedStyle = window.getComputedStyle(this.carousel);
        const matrix = new DOMMatrix(computedStyle.transform);
        let currentX = matrix.m41;
        
        // Stop the animation and freeze at current position
        this.carousel.style.animation = 'none';
        this.carousel.style.transform = `translateX(${currentX}px)`;
        
        // Force reflow
        this.carousel.offsetHeight;
        
        // Calculate which card is currently centered/visible
        // Use absolute position to find nearest card
        const currentAbsoluteX = Math.abs(currentX);
        const currentCardIndex = Math.round(currentAbsoluteX / scrollAmount);
        
        // Calculate next card index based on direction
        let targetIndex;
        if (direction === 'next') {
            targetIndex = currentCardIndex + 1;
        } else {
            targetIndex = Math.max(0, currentCardIndex - 1);
        }
        
        // Ensure we don't go beyond bounds
        if (targetIndex >= this.totalCards) {
            targetIndex = this.totalCards - 1;
        }
        
        // Store the target index
        this.currentIndex = targetIndex;
        
        // Calculate target position (negative because we're moving left)
        const targetX = -(targetIndex * scrollAmount);
        
        console.log('Direction:', direction, 'Current:', currentCardIndex, 'Target:', targetIndex, 'TargetX:', targetX);
        
        // Apply smooth transition to target card
        this.carousel.style.transition = 'transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
        this.carousel.style.transform = `translateX(${targetX}px)`;
        
        // After transition completes, resume CSS animation
        setTimeout(() => {
            // Remove transition
            this.carousel.style.transition = 'none';
            
            // Keep the current position (don't reset transform)
            // We stay at the target card position
            
            // Resume infinite scroll animation from current position
            // Note: The animation will continue from where we left off
            const animationDuration = 40; // seconds
            const totalWidth = this.totalCards * scrollAmount;
            const remainingDistance = totalWidth + targetX; // How far left to scroll
            const remainingTime = (remainingDistance / totalWidth) * animationDuration;
            
            // Resume animation with adjusted timing
            this.carousel.style.animation = `scroll-left ${animationDuration}s linear infinite`;
            this.carousel.style.animationDelay = `-${animationDuration - remainingTime}s`;
            this.carousel.style.animationPlayState = 'running';
            
            this.isTransitioning = false;
        }, 600);
    }
    
    setupHoverPause() {
        if (this.wrapper && this.carousel) {
            this.wrapper.addEventListener('mouseenter', () => {
                if (!this.isTransitioning) {
                    this.carousel.style.animationPlayState = 'paused';
                }
            });
            
            this.wrapper.addEventListener('mouseleave', () => {
                if (!this.isTransitioning) {
                    this.carousel.style.animationPlayState = 'running';
                }
            });
        }
    }
}

// Initialize carousel when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    const packagesCarousel = new PackagesCarousel();
    
    // Export for global access
    window.packagesCarousel = packagesCarousel;
});

