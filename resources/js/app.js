/**
 * Godong Ijo Eco-Luxury Tourism Landing Page
 * JavaScript Initialization
 */

// ========================================
// 1. IMPORT ALPINE.JS FIRST (CRITICAL!)
// ========================================
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

// Import Navigation Dropdown Controller
import { navigationDropdown } from './modules/navigation-dropdown.js';
import './modules/fishing-booking-modal.js';
import './modules/landing-page.js';
import './modules/private-room-booking.js';

// Register Alpine.js collapse plugin
Alpine.plugin(collapse);

// Make navigationDropdown available globally for Alpine.js x-data
window.Alpine = Alpine;
window.navigationDropdown = navigationDropdown;

Alpine.data('educationCarousel', (totalSlides = 0) => ({
  currentIndex: 0,
  totalSlides,
  touchStartX: 0,
  touchEndX: 0,
  touchStartY: 0,
  touchEndY: 0,
  isSwiping: false,

  init() {
    this.totalSlides = Number(this.totalSlides) || 0;
  },

  nextSlide() {
    if (this.currentIndex < this.totalSlides - 1) {
      this.currentIndex++;
    }
  },

  prevSlide() {
    if (this.currentIndex > 0) {
      this.currentIndex--;
    }
  },

  goToSlide(index) {
    if (index >= 0 && index < this.totalSlides) {
      this.currentIndex = index;
    }
  },

  handleTouchStart(event) {
    this.touchStartX = event.touches[0].clientX;
    this.touchStartY = event.touches[0].clientY;
    this.touchEndX = this.touchStartX;
    this.touchEndY = this.touchStartY;
    this.isSwiping = true;
  },

  handleTouchMove(event) {
    if (!this.isSwiping) return;
    this.touchEndX = event.touches[0].clientX;
    this.touchEndY = event.touches[0].clientY;
  },

  handleTouchEnd() {
    if (!this.isSwiping) return;

    const swipeThreshold = 50;
    const diffX = this.touchStartX - this.touchEndX;
    const diffY = Math.abs(this.touchStartY - this.touchEndY);

    if (Math.abs(diffX) > swipeThreshold && Math.abs(diffX) > diffY) {
      if (diffX > 0) {
        this.nextSlide();
      } else {
        this.prevSlide();
      }
    }

    this.touchStartX = 0;
    this.touchEndX = 0;
    this.touchStartY = 0;
    this.touchEndY = 0;
    this.isSwiping = false;
  }
}));

// START ALPINE.JS IMMEDIATELY
Alpine.start();

console.log('✅ Alpine.js started');
console.log('✅ navigationDropdown available:', typeof window.navigationDropdown);

// ========================================
// 2. IMPORT OTHER MODULES AFTER ALPINE
// ========================================
import './modules/packages-carousel.js';
import './modules/landing-page.js';
import { initSmoothScrolling } from './modules/smooth-scroll.js';

// Initialize trust badges display
let trustBadgesInstance = null;

// 1. Mobile Navigation Toggle
function initMobileNav() {
  const navToggle = document.querySelector('.nav-toggle');
  const navMenu = document.querySelector('.nav-menu');
  const navLinks = document.querySelectorAll('.nav-link');

  if (!navToggle || !navMenu) return;

  const toggleMenu = () => {
    const isExpanded = navToggle.getAttribute('aria-expanded') === 'true' || false;
    navToggle.setAttribute('aria-expanded', !isExpanded);
    navToggle.classList.toggle('active');
    navMenu.classList.toggle('active');
  };

  const closeMenu = () => {
    navToggle.setAttribute('aria-expanded', 'false');
    navToggle.classList.remove('active');
    navMenu.classList.remove('active');
  };

  navToggle.addEventListener('click', toggleMenu);

  navLinks.forEach(link => {
    link.addEventListener('click', closeMenu);
  });

  document.addEventListener('click', (e) => {
    if (!navToggle.contains(e.target) && !navMenu.contains(e.target) && navMenu.classList.contains('active')) {
      closeMenu();
    }
  });

  window.addEventListener('resize', () => {
    if (window.innerWidth > 768 && navMenu.classList.contains('active')) {
      closeMenu();
    }
  });
}

// 2. Navbar Scroll Effect
function initNavbarScroll() {
  const navbar = document.querySelector('.navbar');
  if (!navbar) return;

  const handleScroll = () => {
    if (window.scrollY > 50) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
  };

  window.addEventListener('scroll', handleScroll, { passive: true });
  handleScroll(); // Initial check
}

// 3. Active Navigation Highlight
function initActiveNavHighlight() {
  const sections = document.querySelectorAll('section[id]');
  const navLinks = document.querySelectorAll('.nav-link');

  if (sections.length === 0 || navLinks.length === 0) return;

  const observerOptions = {
    root: null,
    rootMargin: '-20% 0px -70% 0px',
    threshold: 0
  };

  const observerCallback = (entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const id = entry.target.getAttribute('id');
        navLinks.forEach(link => {
          link.classList.remove('active');
          if (link.getAttribute('href') === `#${id}`) {
            link.classList.add('active');
          }
        });
      }
    });
  };

  const observer = new IntersectionObserver(observerCallback, observerOptions);

  sections.forEach(section => {
    observer.observe(section);
  });
}

// 4. Scroll Reveal Animations
function initScrollReveal() {
  const revealElements = document.querySelectorAll('.scroll-reveal');
  
  if (revealElements.length === 0) return;

  const observerOptions = {
    root: null,
    rootMargin: '0px 0px -80px 0px',
    threshold: 0.1
  };

  const observerCallback = (entries, observer) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        const el = entry.target;
        
        // Handle staggered delays for grids or groups if needed
        const parent = el.parentElement;
        if (parent && (parent.classList.contains('stagger-grid') || el.classList.contains('stagger-item'))) {
           const index = Array.from(parent.children).indexOf(el);
           const delay = el.dataset.delay || (index * 100);
           el.style.transitionDelay = `${delay}ms`;
        }

        el.classList.add('revealed');
        observer.unobserve(el);
      }
    });
  };

  const observer = new IntersectionObserver(observerCallback, observerOptions);

  revealElements.forEach(el => {
    observer.observe(el);
  });
}

// 6. Counter Animation (for stats section)
function initCounterAnimation() {
  const statNumbers = document.querySelectorAll('.stat-number[data-target]');
  if (statNumbers.length === 0) return;

  const easeOutExpo = (t) => {
    return t === 1 ? 1 : 1 - Math.pow(2, -10 * t);
  };

  const formatNumber = (num) => {
    return num.toLocaleString();
  };

  const animateValue = (obj, start, end, duration) => {
    let startTimestamp = null;
    const step = (timestamp) => {
      if (!startTimestamp) startTimestamp = timestamp;
      const progress = Math.min((timestamp - startTimestamp) / duration, 1);
      const easeProgress = easeOutExpo(progress);
      const current = Math.floor(easeProgress * (end - start) + start);
      
      obj.innerHTML = formatNumber(current);
      
      if (progress < 1) {
        window.requestAnimationFrame(step);
      } else {
        obj.innerHTML = formatNumber(end);
      }
    };
    window.requestAnimationFrame(step);
  };

  const observerOptions = {
    root: null,
    rootMargin: '0px',
    threshold: 0.5
  };

  const observerCallback = (entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const target = parseInt(entry.target.getAttribute('data-target'), 10);
        if (!isNaN(target)) {
          animateValue(entry.target, 0, target, 2000);
        }
        observer.unobserve(entry.target);
      }
    });
  };

  const observer = new IntersectionObserver(observerCallback, observerOptions);

  statNumbers.forEach(stat => {
    observer.observe(stat);
  });
}

// 7. Testimonial Carousel
function initTestimonialCarousel() {
  const track = document.querySelector('.testimonials-track');
  const cards = document.querySelectorAll('.testimonial-card');
  const prevBtn = document.querySelector('.carousel-prev');
  const nextBtn = document.querySelector('.carousel-next');
  const dots = document.querySelectorAll('.carousel-dot');
  
  if (!track || cards.length === 0) return;

  let currentIndex = 0;
  let autoAdvanceInterval;
  
  const getCardsPerView = () => {
    if (window.innerWidth >= 1024) return 3;
    if (window.innerWidth >= 768) return 2;
    return 1;
  };

  let cardsPerView = getCardsPerView();
  
  const updateCarousel = () => {
    const cardWidth = 100 / cardsPerView;
    const maxIndex = Math.max(0, cards.length - cardsPerView);
    
    // Ensure index is within bounds
    currentIndex = Math.max(0, Math.min(currentIndex, maxIndex));
    
    // Calculate translation percentage
    const translateX = -(currentIndex * cardWidth);
    track.style.transform = `translateX(${translateX}%)`;
    
    // Update dots if they exist
    if (dots.length > 0) {
      dots.forEach((dot, index) => {
        if (index === currentIndex) {
          dot.classList.add('active');
        } else {
          dot.classList.remove('active');
        }
      });
    }
  };

  const slideNext = () => {
    const maxIndex = Math.max(0, cards.length - cardsPerView);
    if (currentIndex < maxIndex) {
      currentIndex++;
    } else {
      currentIndex = 0; // loop back
    }
    updateCarousel();
  };

  const slidePrev = () => {
    if (currentIndex > 0) {
      currentIndex--;
    } else {
      currentIndex = Math.max(0, cards.length - cardsPerView); // loop to end
    }
    updateCarousel();
  };

  // Event Listeners
  if (nextBtn) nextBtn.addEventListener('click', slideNext);
  if (prevBtn) prevBtn.addEventListener('click', slidePrev);

  dots.forEach((dot, index) => {
    dot.addEventListener('click', () => {
      currentIndex = index;
      updateCarousel();
    });
  });

  // Window Resize
  window.addEventListener('resize', () => {
    cardsPerView = getCardsPerView();
    updateCarousel();
  });

  // Auto Advance
  const startAutoAdvance = () => {
    autoAdvanceInterval = setInterval(slideNext, 5000);
  };
  
  const stopAutoAdvance = () => {
    clearInterval(autoAdvanceInterval);
  };

  startAutoAdvance();

  const carouselContainer = track.parentElement;
  if (carouselContainer) {
    carouselContainer.addEventListener('mouseenter', stopAutoAdvance);
    carouselContainer.addEventListener('mouseleave', startAutoAdvance);
  }

  // Touch Support
  let touchStartX = 0;
  let touchEndX = 0;
  
  track.addEventListener('touchstart', (e) => {
    touchStartX = e.changedTouches[0].screenX;
    stopAutoAdvance();
  }, { passive: true });
  
  track.addEventListener('touchmove', (e) => {
    touchEndX = e.changedTouches[0].screenX;
  }, { passive: true });
  
  track.addEventListener('touchend', () => {
    const delta = touchStartX - touchEndX;
    if (Math.abs(delta) > 50) {
      if (delta > 0) {
        slideNext();
      } else {
        slidePrev();
      }
    }
    startAutoAdvance();
  });

  // Initial setup
  updateCarousel();
}

// 8. Image Lazy Loading
function initLazyLoading() {
  const lazyImages = document.querySelectorAll('img[data-src]');
  if (lazyImages.length === 0) return;

  const observerOptions = {
    root: null,
    rootMargin: '100px',
    threshold: 0
  };

  const observerCallback = (entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const img = entry.target;
        const src = img.getAttribute('data-src');
        
        if (src) {
          img.src = src;
          
          img.onload = () => {
            img.classList.add('loaded');
            img.removeAttribute('data-src');
          };
          
          img.onerror = () => {
            img.classList.add('image-error');
            // Fallback image handling could go here
            img.removeAttribute('data-src');
          };
        }
        
        observer.unobserve(img);
      }
    });
  };

  const observer = new IntersectionObserver(observerCallback, observerOptions);

  lazyImages.forEach(img => {
    observer.observe(img);
  });
}

// 9. Gallery Lightbox
function initLightbox() {
  const bentoImages = document.querySelectorAll('.hero-bento-item img');
  if (bentoImages.length === 0) return;

  // Create lightbox HTML
  const lightboxHTML = `
    <div class="lightbox-overlay">
      <button class="lightbox-close">&times;</button>
      <img class="lightbox-image" src="" alt="">
      <p class="lightbox-caption"></p>
    </div>
  `;
  
  // Append to body
  document.body.insertAdjacentHTML('beforeend', lightboxHTML);
  
  const lightboxOverlay = document.querySelector('.lightbox-overlay');
  const lightboxClose = document.querySelector('.lightbox-close');
  const lightboxImage = document.querySelector('.lightbox-image');
  const lightboxCaption = document.querySelector('.lightbox-caption');

  // Add inline styles for lightbox just to ensure it works without external CSS
  Object.assign(lightboxOverlay.style, {
    position: 'fixed',
    inset: '0',
    backgroundColor: 'rgba(0, 0, 0, 0.9)',
    zIndex: '9999',
    display: 'flex',
    flexDirection: 'column',
    alignItems: 'center',
    justifyContent: 'center',
    opacity: '0',
    pointerEvents: 'none',
    transition: 'opacity 0.3s ease'
  });

  Object.assign(lightboxClose.style, {
    position: 'absolute',
    top: '20px',
    right: '20px',
    color: 'white',
    fontSize: '32px',
    background: 'none',
    border: 'none',
    cursor: 'pointer',
    padding: '10px'
  });

  Object.assign(lightboxImage.style, {
    maxWidth: '90vw',
    maxHeight: '80vh',
    objectFit: 'contain',
    borderRadius: '12px'
  });

  Object.assign(lightboxCaption.style, {
    color: 'white',
    marginTop: '16px',
    fontSize: '14px',
    textAlign: 'center'
  });

  const openLightbox = (src, alt) => {
    lightboxImage.src = src;
    lightboxImage.alt = alt;
    lightboxCaption.textContent = alt;
    
    lightboxOverlay.classList.add('active');
    lightboxOverlay.style.opacity = '1';
    lightboxOverlay.style.pointerEvents = 'auto';
    
    document.body.style.overflow = 'hidden'; // Prevent body scroll
  };

  const closeLightbox = () => {
    lightboxOverlay.classList.remove('active');
    lightboxOverlay.style.opacity = '0';
    lightboxOverlay.style.pointerEvents = 'none';
    
    setTimeout(() => {
      lightboxImage.src = '';
    }, 300);
    
    document.body.style.overflow = ''; // Restore body scroll
  };

  bentoImages.forEach(img => {
    img.style.cursor = 'pointer';
    img.addEventListener('click', () => {
      openLightbox(img.src, img.alt);
    });
  });

  lightboxClose.addEventListener('click', closeLightbox);
  
  lightboxOverlay.addEventListener('click', (e) => {
    if (e.target === lightboxOverlay) {
      closeLightbox();
    }
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && lightboxOverlay.style.opacity === '1') {
      closeLightbox();
    }
  });
}

// 10. Newsletter Form
function initNewsletterForm() {
  const forms = document.querySelectorAll('.newsletter-form');
  if (forms.length === 0) return;

  forms.forEach(form => {
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      
      const emailInput = form.querySelector('input[type="email"]');
      const submitBtn = form.querySelector('button[type="submit"]');
      
      if (!emailInput || !submitBtn) return;
      
      const email = emailInput.value.trim();
      
      // Basic email validation
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(email)) {
        emailInput.focus();
        return;
      }
      
      // Simulate form submission
      const originalBtnText = submitBtn.innerHTML;
      submitBtn.innerHTML = '✓ Subscribed!';
      
      emailInput.value = '';
      emailInput.disabled = true;
      submitBtn.disabled = true;
      
      setTimeout(() => {
        submitBtn.innerHTML = originalBtnText;
        emailInput.disabled = false;
        submitBtn.disabled = false;
      }, 3000);
    });
  });
}

// 11. Parallax Effect for Hero
function initHeroParallax() {
  const bentoItems = document.querySelectorAll('.hero-bento-item');
  const heroSection = document.querySelector('.hero');
  
  if (bentoItems.length === 0 || !heroSection) return;

  // Check for prefers-reduced-motion
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (prefersReducedMotion) return;

  let ticking = false;
  
  const updateParallax = () => {
    const scrollY = window.scrollY;
    const heroRect = heroSection.getBoundingClientRect();
    
    // Only animate if hero is visible
    if (heroRect.bottom > 0 && heroRect.top < window.innerHeight) {
      bentoItems.forEach((item, index) => {
        // Different movement rate based on index (0.02 to 0.05)
        const rate = 0.02 + (index % 4) * 0.01;
        const yPos = scrollY * rate;
        item.style.transform = `translateY(${yPos}px)`;
      });
    }
    
    ticking = false;
  };

  window.addEventListener('scroll', () => {
    if (!ticking) {
      window.requestAnimationFrame(updateParallax);
      ticking = true;
    }
  }, { passive: true });
}

// 12. Initialize Trust Badges for Booking Modal
function initTrustBadges() {
  // Check if TrustBadgeDisplay is available
  if (typeof window.TrustBadgeDisplay === 'undefined') {
    console.warn('TrustBadgeDisplay not loaded');
    return;
  }

  // Initialize trust badges display
  trustBadgesInstance = new window.TrustBadgeDisplay({
    updateInterval: 60000, // Update every 60 seconds
    counterEndpoint: '/api/stats/bookings-today',
    autoUpdate: true
  });

  // Wait for modal to be available in DOM, then inject badges
  const checkModalInterval = setInterval(() => {
    const modal = document.querySelector('#bookingModalOverlay');
    if (modal) {
      trustBadgesInstance.injectIntoModal('#bookingModalOverlay');
      clearInterval(checkModalInterval);
    }
  }, 100);

  // Clear interval after 5 seconds if modal not found
  setTimeout(() => {
    clearInterval(checkModalInterval);
  }, 5000);
}

// 13. Initialize WhatsApp Button
function initWhatsAppButton() {
  // Check if WhatsAppButton is available
  if (typeof window.WhatsAppButton === 'undefined') {
    console.warn('WhatsAppButton not loaded');
    return;
  }

  // Initialize WhatsApp button with default settings
  // Phone number should be configured based on business contact
  const whatsappButton = new window.WhatsAppButton({
    phoneNumber: '6281234567890', // Replace with actual business WhatsApp number
    message: 'Halo, saya mau tanya tentang paket wisata...',
    scrollThreshold: 0.5, // Show after 50% page scroll
    bounceInterval: 5000, // Bounce every 5 seconds
    fadeInDuration: 300, // 300ms fade-in
    bottomMargin: 20, // 20px from bottom
    rightMargin: 20 // 20px from right
  });

  console.log('WhatsApp button initialized');
}

// 14. Initialize Booking Form Enhancement
async function initBookingFormEnhancement() {
  // Dynamically import modules
  try {
    const { LoadingStateManager } = await import('./modules/loading-manager.js');
    const { AutoFormatter } = await import('./modules/auto-formatter.js');
    const { FormValidator } = await import('./modules/form-validator.js');
    
    // Wait for modal to be available
    const waitForModal = setInterval(() => {
      const bookingForm = document.getElementById('bookingForm');
      
      if (bookingForm) {
        clearInterval(waitForModal);
        
        // Initialize Form Validator
        const validator = new FormValidator(bookingForm);
        validator.attachToForm();
        
        // Initialize Auto Formatter
        const formatter = new AutoFormatter();
        
        const phoneField = document.getElementById('bookingPhone');
        const nameField = document.getElementById('bookingNama');
        const emailField = document.getElementById('bookingEmail');
        
        if (phoneField) formatter.attachToField(phoneField, 'phone');
        if (nameField) formatter.attachToField(nameField, 'name');
        if (emailField) formatter.attachToField(emailField, 'email');
        
        // Initialize Loading Manager
        const loadingContainer = document.getElementById('loadingContainer');
        if (loadingContainer) {
          const loadingManager = new LoadingStateManager(loadingContainer);
          
          // Store in window for global access
          window.bookingLoadingManager = loadingManager;
        }
        
        console.log('✅ Booking form enhancement initialized');
      }
    }, 100);
    
    // Clear interval after 5 seconds if modal not found
    setTimeout(() => clearInterval(waitForModal), 5000);
    
  } catch (error) {
    console.warn('Booking form modules not available:', error);
  }
}

// Initialization
function init() {
  initSmoothScrolling(); // Use imported smooth scroll module
  initNavbarScroll();
  initActiveNavHighlight();
  initScrollReveal();
  initCounterAnimation();
  initTestimonialCarousel();
  initLazyLoading();
  initLightbox();
  initNewsletterForm();
  initHeroParallax();
  initTrustBadges();
  initWhatsAppButton();
  initBookingFormEnhancement(); // Add booking form enhancement
  console.log('🌿 Godong Ijo Landing Page initialized');
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', init);
} else {
  init();
}


// Menu Carousel Auto-Slide
document.addEventListener('DOMContentLoaded', () => {
    const carouselWrapper = document.querySelector('.menu-carousel-wrapper');
    if (!carouselWrapper) return;

    const track = carouselWrapper.querySelector('.menu-carousel-track');
    const slides = Array.from(track.children);
    const dots = Array.from(carouselWrapper.querySelectorAll('.menu-carousel-dot'));
    
    let currentIndex = 0;
    const autoSlideInterval = 6000; // 6 seconds

    const moveToSlide = (targetIndex) => {
        track.style.transform = `translateX(-${targetIndex * 100}%)`;
        
        dots.forEach(dot => dot.classList.remove('active'));
        dots[targetIndex].classList.add('active');
        
        currentIndex = targetIndex;
    };

    const nextSlide = () => {
        const nextIndex = (currentIndex + 1) % slides.length;
        moveToSlide(nextIndex);
    };

    // Auto slide
    let autoSlide = setInterval(nextSlide, autoSlideInterval);

    // Dot click handlers
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            moveToSlide(index);
            // Reset auto-slide timer
            clearInterval(autoSlide);
            autoSlide = setInterval(nextSlide, autoSlideInterval);
        });
    });

    // Pause on hover
    carouselWrapper.addEventListener('mouseenter', () => {
        clearInterval(autoSlide);
    });

    carouselWrapper.addEventListener('mouseleave', () => {
        autoSlide = setInterval(nextSlide, autoSlideInterval);
    });
});


// ========================================
// WHATSAPP CLICK HANDLER (ANALYTICS)
// ========================================
window.handleWhatsAppClick = function(event) {
    // Optional: Track WhatsApp clicks for analytics
    if (typeof gtag !== 'undefined') {
        const message = new URL(event.currentTarget.href).searchParams.get('text');
        gtag('event', 'whatsapp_click', {
            'event_category': 'engagement',
            'event_label': message,
        });
    }
};

// ========================================
// HERO MODERN CAROUSEL CONTROL (DISABLED - Static background now)
// ========================================
/*
document.addEventListener('DOMContentLoaded', function() {
  const heroSection = document.querySelector('.hero-modern-section');
  
  if (!heroSection) return; // Exit if hero modern section not present
  
  const slides = document.querySelectorAll('.hero-bg-slide');
  const dots = document.querySelectorAll('.carousel-dot');
  const totalSlides = slides.length;
  
  if (totalSlides === 0) return;
  
  let currentSlide = 0;
  let autoPlayInterval;
  const slideInterval = 6000; // 6 seconds per slide
  
  // Function to update active slide and dot
  function updateSlide(index) {
    // Remove active class from all dots
    dots.forEach(dot => {
      dot.classList.remove('active');
      dot.removeAttribute('aria-current');
    });
    
    // Add active class to current dot
    if (dots[index]) {
      dots[index].classList.add('active');
      dots[index].setAttribute('aria-current', 'true');
    }
    
    currentSlide = index;
  }
  
  // Function to go to next slide
  function nextSlide() {
    currentSlide = (currentSlide + 1) % totalSlides;
    updateSlide(currentSlide);
  }
  
  // Function to go to specific slide
  function goToSlide(index) {
    if (index >= 0 && index < totalSlides) {
      updateSlide(index);
      resetAutoPlay();
    }
  }
  
  // Auto-play function
  function startAutoPlay() {
    autoPlayInterval = setInterval(nextSlide, slideInterval);
  }
  
  // Reset auto-play (when user manually selects a slide)
  function resetAutoPlay() {
    clearInterval(autoPlayInterval);
    startAutoPlay();
  }
  
  // Pause auto-play on hover
  heroSection.addEventListener('mouseenter', () => {
    clearInterval(autoPlayInterval);
  });
  
  // Resume auto-play when mouse leaves
  heroSection.addEventListener('mouseleave', () => {
    startAutoPlay();
  });
  
  // Add click event to each dot
  dots.forEach((dot, index) => {
    dot.addEventListener('click', () => {
      goToSlide(index);
    });
  });
  
  // Sync with CSS animation timing
  // The CSS animation runs continuously, so we just update the dots to match
  setInterval(() => {
    nextSlide();
  }, slideInterval);
  
  // Initialize first slide as active
  updateSlide(0);
});
*/


// ========================================
// HERO CARDS CAROUSEL CONTROL
// ========================================
document.addEventListener('DOMContentLoaded', function() {
  const carouselContainer = document.getElementById('heroCardsCarousel');
  
  if (!carouselContainer) return;
  
  const cards = carouselContainer.querySelectorAll('.hero-destination-card');
  const prevBtn = document.querySelector('.card-nav-prev');
  const nextBtn = document.querySelector('.card-nav-next');
  const indicators = document.querySelectorAll('.card-indicator-dot');
  const totalCards = cards.length;
  
  if (totalCards === 0) return;
  
  let currentIndex = 0;
  let autoPlayInterval;
  const autoPlayDelay = 5000; // 5 seconds
  
  // Update active card
  function updateCards(newIndex) {
    if (newIndex < 0 || newIndex >= totalCards) return;
    
    // Remove all states
    cards.forEach(card => {
      card.classList.remove('active', 'prev', 'next');
    });
    
    indicators.forEach(dot => {
      dot.classList.remove('active');
    });
    
    // Set new active
    cards[newIndex].classList.add('active');
    indicators[newIndex].classList.add('active');
    
    // Set prev (if exists)
    if (newIndex > 0) {
      cards[newIndex - 1].classList.add('prev');
    }
    
    // Set next (if exists)
    if (newIndex < totalCards - 1) {
      cards[newIndex + 1].classList.add('next');
    }
    
    currentIndex = newIndex;
    
    // Update button states
    prevBtn.disabled = currentIndex === 0;
    nextBtn.disabled = currentIndex === totalCards - 1;
  }
  
  // Navigate to specific card
  function goToCard(index) {
    updateCards(index);
    resetAutoPlay();
  }
  
  // Previous card
  function prevCard() {
    if (currentIndex > 0) {
      goToCard(currentIndex - 1);
    }
  }
  
  // Next card
  function nextCard() {
    if (currentIndex < totalCards - 1) {
      goToCard(currentIndex + 1);
    }
  }
  
  // Auto-play
  function startAutoPlay() {
    autoPlayInterval = setInterval(() => {
      if (currentIndex < totalCards - 1) {
        nextCard();
      } else {
        goToCard(0); // Loop back to first
      }
    }, autoPlayDelay);
  }
  
  function stopAutoPlay() {
    clearInterval(autoPlayInterval);
  }
  
  function resetAutoPlay() {
    stopAutoPlay();
    startAutoPlay();
  }
  
  // Event listeners
  prevBtn.addEventListener('click', prevCard);
  nextBtn.addEventListener('click', nextCard);
  
  indicators.forEach((dot, index) => {
    dot.addEventListener('click', () => goToCard(index));
  });
  
  // Pause on hover
  carouselContainer.addEventListener('mouseenter', stopAutoPlay);
  carouselContainer.addEventListener('mouseleave', startAutoPlay);
  
  // Keyboard navigation
  document.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowLeft') prevCard();
    if (e.key === 'ArrowRight') nextCard();
  });
  
  // Initialize
  updateCards(0);
  startAutoPlay();
});
