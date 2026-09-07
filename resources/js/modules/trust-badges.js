/**
 * TrustBadgeDisplay - Display trust and security indicators
 * 
 * Handles:
 * - Security badge rendering
 * - Social proof counter with real-time updates
 * - Payment method logos
 * - Lazy loading for badge images
 */

class TrustBadgeDisplay {
  constructor(options = {}) {
    this.updateInterval = options.updateInterval || 60000; // 60 seconds default
    this.counterEndpoint = options.counterEndpoint || '/api/stats/bookings-today';
    this.autoUpdate = options.autoUpdate !== false;
    this.intervalId = null;
    
    this.init();
  }

  /**
   * Initialize trust badges
   */
  init() {
    console.log('🔒 TrustBadgeDisplay initialized');
    
    // Start auto-update for social proof counter if enabled
    if (this.autoUpdate) {
      this.startAutoUpdate();
    }
  }

  /**
   * Render trust badges HTML for modal header
   * @returns {string} HTML string for header badges
   */
  renderHeaderBadges() {
    return `
    `;
  }

  /**
   * Render trust badges HTML for modal footer
   * @returns {string} HTML string for footer badges
   */
  renderFooterBadges() {
    return ``;
  }

  /**
   * Update social proof counter from API
   */
  async updateSocialProofCounter() {
    const counterElement = document.getElementById('trustBadgeSocialProofCount');
    
    if (!counterElement) {
      return;
    }

    try {
      const response = await fetch(this.counterEndpoint, {
        method: 'GET',
        headers: {
          'Accept': 'application/json'
        }
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const data = await response.json();

      if (data.success && typeof data.count !== 'undefined') {
        // Animate counter update
        this.animateCounter(counterElement, data.count);
      }
    } catch (error) {
      console.warn('Failed to update social proof counter:', error);
      // Keep existing count or show fallback
      if (counterElement.textContent === '-') {
        counterElement.textContent = '47'; // Fallback static number
      }
    }
  }

  /**
   * Animate counter change
   * @param {HTMLElement} element - Counter element
   * @param {number} targetValue - Target count value
   */
  animateCounter(element, targetValue) {
    const currentValue = parseInt(element.textContent) || 0;
    
    // If values are the same, no animation needed
    if (currentValue === targetValue) {
      return;
    }

    const duration = 500; // 500ms animation
    const startTime = performance.now();
    const difference = targetValue - currentValue;

    const animate = (currentTime) => {
      const elapsed = currentTime - startTime;
      const progress = Math.min(elapsed / duration, 1);
      
      // Ease out animation
      const easeProgress = 1 - Math.pow(1 - progress, 3);
      const current = Math.round(currentValue + (difference * easeProgress));
      
      element.textContent = current;

      if (progress < 1) {
        requestAnimationFrame(animate);
      } else {
        element.textContent = targetValue;
      }
    };

    requestAnimationFrame(animate);
  }

  /**
   * Start auto-update interval for social proof counter
   */
  startAutoUpdate() {
    // Initial update
    this.updateSocialProofCounter();

    // Set interval for periodic updates
    this.intervalId = setInterval(() => {
      this.updateSocialProofCounter();
    }, this.updateInterval);
  }

  /**
   * Stop auto-update interval
   */
  stopAutoUpdate() {
    if (this.intervalId) {
      clearInterval(this.intervalId);
      this.intervalId = null;
    }
  }

  /**
   * Inject trust badges into modal
   * @param {string} modalSelector - CSS selector for modal element
   */
  injectIntoModal(modalSelector = '#bookingModalOverlay') {
    const modal = document.querySelector(modalSelector);
    
    if (!modal) {
      console.warn('Modal not found:', modalSelector);
      return;
    }

    // Inject header badges
    const headerElement = modal.querySelector('.booking-modal-header');
    if (headerElement) {
      const existingHeaderBadges = headerElement.querySelector('.trust-badges-header');
      if (!existingHeaderBadges) {
        headerElement.insertAdjacentHTML('beforeend', this.renderHeaderBadges());
      }
    }

    // Inject footer badges
    const modalContent = modal.querySelector('.booking-modal-content');
    if (modalContent) {
      const existingFooterBadges = modalContent.querySelector('.trust-badges-footer');
      if (!existingFooterBadges) {
        modalContent.insertAdjacentHTML('beforeend', this.renderFooterBadges());
      }
    }

    console.log('✓ Trust badges injected into modal');
  }

  /**
   * Remove trust badges from modal
   * @param {string} modalSelector - CSS selector for modal element
   */
  removeFromModal(modalSelector = '#bookingModalOverlay') {
    const modal = document.querySelector(modalSelector);
    
    if (!modal) {
      return;
    }

    const headerBadges = modal.querySelector('.trust-badges-header');
    const footerBadges = modal.querySelector('.trust-badges-footer');

    if (headerBadges) headerBadges.remove();
    if (footerBadges) footerBadges.remove();
  }

  /**
   * Cleanup when component is destroyed
   */
  destroy() {
    this.stopAutoUpdate();
    console.log('TrustBadgeDisplay destroyed');
  }
}

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
  module.exports = TrustBadgeDisplay;
}

// Make available globally
window.TrustBadgeDisplay = TrustBadgeDisplay;
