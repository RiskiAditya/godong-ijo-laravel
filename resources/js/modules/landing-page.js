/**
 * Landing Page Module
 * Handles fishing modal functionality and other landing page interactions
 */

// Define global function to trigger Alpine.js fishing modal
window.openFishingModal = function() {
    console.log('✅ openFishingModal called');
    
    // Check if Alpine is loaded
    if (typeof window.Alpine === 'undefined') {
        console.error('❌ Alpine.js not loaded yet');
        alert('Modal belum siap. Silakan refresh halaman.');
        return;
    }
    
    console.log('✅ Alpine loaded, dispatching open-fishing-modal event');
    window.dispatchEvent(new CustomEvent('open-fishing-modal'));
    
    // Fallback: If modal doesn't open after 500ms, restore scroll
    setTimeout(function() {
        const modal = document.querySelector('[x-data*="fishingBookingModal"]');
        if (modal) {
            const isVisible = modal.style.display !== 'none' && 
                            window.getComputedStyle(modal).display !== 'none';
            console.log('🔍 Modal visibility check:', isVisible);
            
            if (!isVisible) {
                console.warn('⚠️ Modal did not open, restoring scroll');
                document.body.style.overflow = '';
            }
        } else {
            console.warn('⚠️ Fishing modal element not found');
            document.body.style.overflow = '';
        }
    }, 500);
};

// Initialize landing page functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ DOM Content Loaded - attaching button listeners');
    
    // Find all fishing buttons
    const fishingButtons = document.querySelectorAll('[data-booking-type="fishing"]');
    console.log('🎣 Found fishing buttons:', fishingButtons.length, fishingButtons);
    
    fishingButtons.forEach(function(button, index) {
        console.log('🎣 Attaching listener to fishing button', index, button);
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('🎣 Fishing button clicked!');
            window.openFishingModal();
        });
    });
    
    // Find all generic booking buttons
    const genericButtons = document.querySelectorAll('[data-booking-type="generic"]');
    console.log('📋 Found generic booking buttons:', genericButtons.length);
    
    genericButtons.forEach(function(button, index) {
        console.log('📋 Attaching listener to generic button', index);
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const packageId = parseInt(button.getAttribute('data-package-id'));
            const packageName = button.getAttribute('data-package-name');
            const packagePrice = parseInt(button.getAttribute('data-package-price'));
            
            console.log('📋 Generic button clicked:', {packageId, packageName, packagePrice});
            
            if (typeof window.openBookingModal === 'function') {
                window.openBookingModal(packageId, packageName, packagePrice);
            } else {
                console.error('❌ openBookingModal function not found');
                alert('Modal belum siap. Silakan refresh halaman.');
            }
        });
    });
    
    // Debug logging after 1 second
    setTimeout(function() {
        console.log('🔍 Alpine.js status:', typeof window.Alpine !== 'undefined' ? 'Loaded ✅' : 'Not loaded ❌');
        if (typeof window.Alpine !== 'undefined') {
            console.log('📦 Alpine version:', window.Alpine.version || 'Unknown');
        }
    }, 1000);
    
    console.log('✅ Landing page module initialized');
});

