/**
 * Navigation Dropdown Controller
 * 
 * Manages dropdown interaction and accessibility for the multi-page navigation system.
 * 
 * Features:
 * - Desktop dropdown with hover behavior
 * - Mobile accordion with click/tap behavior
 * - Keyboard navigation (Tab, Enter, Arrow keys, Escape)
 * - Focus trapping
 * - Body scroll prevention when mobile menu is open
 * 
 * Requirements: 1.2, 1.3, 1.5, 1.6, 2.3, 2.4, 2.5, 2.7, 2.8, 15.5, 15.6
 */

export function navigationDropdown() {
    return {
        // State
        mobileOpen: false,
        activeDropdown: null,
        activeAccordion: null,
        closeTimeout: null,
        
        // Desktop dropdown handlers
        openDropdown(key) {
            this.clearCloseTimeout();
            this.activeDropdown = key;
            console.log('Dropdown opened:', key);
        },
        
        closeDropdown() {
            this.clearCloseTimeout();
            this.activeDropdown = null;
            console.log('Dropdown closed');
        },
        
        toggleDropdown(key) {
            if (this.activeDropdown === key) {
                this.closeDropdown();
            } else {
                this.openDropdown(key);
            }
        },
        
        closeWithDelay(delay = 300) {
            this.clearCloseTimeout();
            this.closeTimeout = setTimeout(() => {
                this.closeDropdown();
            }, delay);
        },
        
        clearCloseTimeout() {
            if (this.closeTimeout) {
                clearTimeout(this.closeTimeout);
                this.closeTimeout = null;
            }
        },
        
        // Mobile accordion handlers
        toggleMobile() {
            this.mobileOpen = !this.mobileOpen;
            console.log('Mobile menu toggled:', this.mobileOpen);
            
            // Prevent body scrolling when mobile menu is open
            if (this.mobileOpen) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
                this.activeAccordion = null;
            }
        },
        
        closeMobile() {
            this.mobileOpen = false;
            document.body.style.overflow = '';
            this.activeAccordion = null;
            console.log('Mobile menu closed');
        },

        init() {
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 768) {
                    this.closeMobile();
                }
            });
        },
        
        toggleAccordion(key) {
            if (this.activeAccordion === key) {
                this.activeAccordion = null;
            } else {
                this.activeAccordion = key;
            }
            console.log('Accordion toggled:', key);
        },
        
        // Keyboard navigation
        focusFirstChild(event) {
            const dropdown = event.target.nextElementSibling;
            if (dropdown) {
                const firstLink = dropdown.querySelector('a');
                if (firstLink) {
                    firstLink.focus();
                }
            }
        },
        
        focusNext(event) {
            const currentItem = event.target.parentElement;
            const nextItem = currentItem.nextElementSibling;
            if (nextItem) {
                const nextLink = nextItem.querySelector('a');
                if (nextLink) {
                    nextLink.focus();
                }
            }
        },
        
        focusPrev(event) {
            const currentItem = event.target.parentElement;
            const prevItem = currentItem.previousElementSibling;
            if (prevItem) {
                const prevLink = prevItem.querySelector('a');
                if (prevLink) {
                    prevLink.focus();
                } else {
                    // Focus back to trigger button
                    const dropdown = currentItem.parentElement;
                    const trigger = dropdown.previousElementSibling;
                    if (trigger) {
                        trigger.focus();
                    }
                }
            }
        },
        
        // Accessibility helpers
        getAriaExpanded(key) {
            return this.activeDropdown === key ? 'true' : 'false';
        },
        
        trapFocus(element) {
            const focusableElements = element.querySelectorAll(
                'a[href], button:not([disabled]), textarea:not([disabled]), input:not([disabled]), select:not([disabled])'
            );
            
            if (focusableElements.length === 0) return;
            
            const firstElement = focusableElements[0];
            const lastElement = focusableElements[focusableElements.length - 1];
            
            element.addEventListener('keydown', (e) => {
                if (e.key !== 'Tab') return;
                
                if (e.shiftKey) {
                    // Shift + Tab
                    if (document.activeElement === firstElement) {
                        e.preventDefault();
                        lastElement.focus();
                    }
                } else {
                    // Tab
                    if (document.activeElement === lastElement) {
                        e.preventDefault();
                        firstElement.focus();
                    }
                }
            });
        },
    };
}
