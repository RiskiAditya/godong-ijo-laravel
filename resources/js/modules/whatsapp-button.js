/**
 * WhatsApp Button Module
 * 
 * Provides a floating WhatsApp contact button with scroll-based visibility,
 * bounce animation, and modal state awareness.
 * 
 * Requirements Covered:
 * - 8.1: Fixed positioning in bottom-right corner
 * - 8.2: Scroll trigger (show after 50% page scroll)
 * - 8.7: Green circular background (60px diameter) with white icon
 * - 8.8: Bounce animation every 5 seconds
 */

class WhatsAppButton {
  constructor(options = {}) {
    this.options = {
      phoneNumber: options.phoneNumber || '6281234567890', // Default Indonesian format
      message: options.message || 'Halo, saya mau tanya tentang paket wisata...',
      scrollThreshold: options.scrollThreshold || 0.5, // 50% of page height
      bounceInterval: options.bounceInterval || 5000, // 5 seconds
      fadeInDuration: options.fadeInDuration || 300, // 300ms
      bottomMargin: options.bottomMargin || 20, // 20px
      rightMargin: options.rightMargin || 20, // 20px
      ...options
    };

    this.button = null;
    this.isVisible = false;
    this.bounceIntervalId = null;
    this.isModalOpen = false;

    this.init();
  }

  /**
   * Initialize the WhatsApp button
   */
  init() {
    this.createButton();
    this.attachStyles();
    this.attachEventListeners();
    this.startBounceAnimation();

    console.log('WhatsApp button initialized');
  }

  /**
   * Create the button element
   */
  createButton() {
    this.button = document.createElement('a');
    this.button.href = this.getWhatsAppUrl();
    this.button.target = '_blank';
    this.button.rel = 'noopener noreferrer';
    this.button.className = 'whatsapp-button';
    this.button.setAttribute('aria-label', 'Tanya via WhatsApp');
    this.button.style.opacity = '0';
    this.button.style.pointerEvents = 'none';

    // WhatsApp icon (SVG)
    this.button.innerHTML = `
      <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" fill="white"/>
      </svg>
      <span class="whatsapp-tooltip">Tanya via WhatsApp</span>
    `;

    document.body.appendChild(this.button);
  }

  /**
   * Attach CSS styles to the button
   */
  attachStyles() {
    const style = document.createElement('style');
    style.textContent = `
      .whatsapp-button {
        position: fixed;
        bottom: ${this.options.bottomMargin}px;
        right: ${this.options.rightMargin}px;
        width: 60px;
        height: 60px;
        background-color: #25D366;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4);
        z-index: 1000;
        transition: opacity ${this.options.fadeInDuration}ms ease,
                    transform 0.3s ease,
                    box-shadow 0.3s ease;
        cursor: pointer;
        text-decoration: none;
      }

      .whatsapp-button:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 16px rgba(37, 211, 102, 0.6);
      }

      .whatsapp-button svg {
        width: 28px;
        height: 28px;
      }

      .whatsapp-tooltip {
        position: absolute;
        right: 70px;
        background-color: #333;
        color: white;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 14px;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
      }

      .whatsapp-tooltip::after {
        content: '';
        position: absolute;
        right: -6px;
        top: 50%;
        transform: translateY(-50%);
        width: 0;
        height: 0;
        border-left: 6px solid #333;
        border-top: 6px solid transparent;
        border-bottom: 6px solid transparent;
      }

      .whatsapp-button:hover .whatsapp-tooltip {
        opacity: 1;
      }

      /* Bounce animation */
      @keyframes whatsapp-bounce {
        0%, 20%, 50%, 80%, 100% {
          transform: translateY(0);
        }
        40% {
          transform: translateY(-10px);
        }
        60% {
          transform: translateY(-5px);
        }
      }

      .whatsapp-button.bounce {
        animation: whatsapp-bounce 1s ease;
      }

      /* Hide on mobile when modal is open to avoid overlap */
      @media (max-width: 768px) {
        .whatsapp-button {
          bottom: ${this.options.bottomMargin + 10}px;
        }
        
        .whatsapp-tooltip {
          display: none;
        }
      }

      /* Respect prefers-reduced-motion */
      @media (prefers-reduced-motion: reduce) {
        .whatsapp-button {
          animation: none !important;
          transition: opacity ${this.options.fadeInDuration}ms ease;
        }
        
        .whatsapp-button.bounce {
          animation: none !important;
        }
      }
    `;

    document.head.appendChild(style);
  }

  /**
   * Attach event listeners
   */
  attachEventListeners() {
    // Scroll event for showing/hiding button
    let ticking = false;

    const handleScroll = () => {
      if (!ticking) {
        window.requestAnimationFrame(() => {
          this.checkScrollPosition();
          ticking = false;
        });
        ticking = true;
      }
    };

    window.addEventListener('scroll', handleScroll, { passive: true });

    // Initial check
    this.checkScrollPosition();

    // Listen for modal state changes
    this.observeModalState();
  }

  /**
   * Check scroll position and show/hide button accordingly
   */
  checkScrollPosition() {
    // Don't show if modal is open
    if (this.isModalOpen) {
      this.hide();
      return;
    }

    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    const documentHeight = Math.max(
      document.body.scrollHeight,
      document.body.offsetHeight,
      document.documentElement.clientHeight,
      document.documentElement.scrollHeight,
      document.documentElement.offsetHeight
    );
    const windowHeight = window.innerHeight;
    const scrollableHeight = documentHeight - windowHeight;
    const scrollPercentage = scrollTop / scrollableHeight;

    // Show button if scrolled past threshold
    if (scrollPercentage >= this.options.scrollThreshold) {
      this.show();
    } else {
      this.hide();
    }
  }

  /**
   * Show the button with fade-in animation
   */
  show() {
    if (!this.isVisible) {
      this.isVisible = true;
      this.button.style.opacity = '1';
      this.button.style.pointerEvents = 'auto';
    }
  }

  /**
   * Hide the button with fade-out animation
   */
  hide() {
    if (this.isVisible) {
      this.isVisible = false;
      this.button.style.opacity = '0';
      this.button.style.pointerEvents = 'none';
    }
  }

  /**
   * Start the bounce animation interval
   */
  startBounceAnimation() {
    // Check if user prefers reduced motion
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) {
      return;
    }

    this.bounceIntervalId = setInterval(() => {
      // Only bounce if button is visible
      if (this.isVisible && !this.isModalOpen) {
        this.button.classList.add('bounce');
        
        // Remove class after animation completes
        setTimeout(() => {
          this.button.classList.remove('bounce');
        }, 1000);
      }
    }, this.options.bounceInterval);
  }

  /**
   * Stop the bounce animation interval
   */
  stopBounceAnimation() {
    if (this.bounceIntervalId) {
      clearInterval(this.bounceIntervalId);
      this.bounceIntervalId = null;
    }
  }

  /**
   * Observe modal state changes
   */
  observeModalState() {
    // Watch for booking modal open/close
    const observer = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
        if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
          const target = mutation.target;
          
          // Check if this is the booking modal
          if (target.id === 'bookingModalOverlay' || target.classList.contains('modal-overlay')) {
            const isActive = target.classList.contains('active') || 
                           target.classList.contains('show') ||
                           target.style.display === 'block';
            
            this.isModalOpen = isActive;
            
            // Hide button when modal is open, show when closed
            if (this.isModalOpen) {
              this.hide();
            } else {
              // Recheck scroll position when modal closes
              setTimeout(() => {
                this.checkScrollPosition();
              }, 100);
            }
          }
        }
      });
    });

    // Start observing the document for modal changes
    observer.observe(document.body, {
      attributes: true,
      subtree: true,
      attributeFilter: ['class', 'style']
    });

    // Also check for modal element existence periodically (fallback)
    setInterval(() => {
      const modal = document.querySelector('#bookingModalOverlay, .modal-overlay.active');
      const wasModalOpen = this.isModalOpen;
      this.isModalOpen = modal && (
        modal.classList.contains('active') ||
        modal.classList.contains('show') ||
        modal.style.display === 'block'
      );

      // If modal state changed, recheck scroll position
      if (wasModalOpen !== this.isModalOpen && !this.isModalOpen) {
        this.checkScrollPosition();
      }
    }, 500);
  }

  /**
   * Generate WhatsApp URL with pre-filled message
   */
  getWhatsAppUrl() {
    const encodedMessage = encodeURIComponent(this.options.message);
    return `https://wa.me/${this.options.phoneNumber}?text=${encodedMessage}`;
  }

  /**
   * Update phone number
   */
  setPhoneNumber(phoneNumber) {
    this.options.phoneNumber = phoneNumber;
    this.button.href = this.getWhatsAppUrl();
  }

  /**
   * Update pre-filled message
   */
  setMessage(message) {
    this.options.message = message;
    this.button.href = this.getWhatsAppUrl();
  }

  /**
   * Destroy the button and clean up
   */
  destroy() {
    this.stopBounceAnimation();
    if (this.button && this.button.parentNode) {
      this.button.parentNode.removeChild(this.button);
    }
  }
}

// Export to global scope for use in app.js
window.WhatsAppButton = WhatsAppButton;
