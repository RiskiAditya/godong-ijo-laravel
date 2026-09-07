/**
 * Admin Dashboard JavaScript
 * Handles navigation, modals, search, filters, and notifications
 */

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    initSidebar();
    initUserDropdown();
    initToasts();
    initForms();
});

/**
 * Sidebar navigation handling
 */
function initSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const navItems = document.querySelectorAll('.nav-item');
    
    // Set active nav item based on current URL
    const currentPath = window.location.pathname;
    navItems.forEach(item => {
        const link = item.querySelector('a');
        if (link && link.getAttribute('href') === currentPath) {
            item.classList.add('active');
        }
    });
    
    // Handle nav item clicks
    navItems.forEach(item => {
        item.addEventListener('click', function(e) {
            const link = this.querySelector('a');
            if (link) {
                navItems.forEach(i => i.classList.remove('active'));
                this.classList.add('active');
            }
        });
    });
}

/**
 * User dropdown menu
 */
function initUserDropdown() {
    const userBtn = document.querySelector('.user-btn');
    const userDrop = document.querySelector('.user-drop');
    
    if (userBtn && userDrop) {
        userBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            userDrop.classList.toggle('open');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function() {
            userDrop.classList.remove('open');
        });
        
        // Prevent closing when clicking inside dropdown
        userDrop.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
}

/**
 * Toast notification system
 */
function initToasts() {
    // Create toast container if it doesn't exist
    if (!document.getElementById('toastContainer')) {
        const container = document.createElement('div');
        container.id = 'toastContainer';
        container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            display: flex;
            flex-direction: column;
            gap: 10px;
        `;
        document.body.appendChild(container);
    }
}

/**
 * Show toast notification
 * @param {string} message - The message to display
 * @param {string} type - Type of toast (success, error, warning, info)
 * @param {number} duration - Duration in milliseconds (default: 3000)
 */
function showToast(message, type = 'success', duration = 3000) {
    const container = document.getElementById('toastContainer');
    if (!container) return;
    
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.style.cssText = `
        padding: 14px 18px;
        background: var(--${type === 'success' ? 'green' : type === 'error' ? 'red' : 'brand'}-soft);
        color: var(--${type === 'success' ? 'green' : type === 'error' ? 'red' : 'brand'});
        border: 1px solid var(--${type === 'success' ? 'green' : type === 'error' ? 'red' : 'brand'});
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        font-weight: 500;
        min-width: 300px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        animation: slideIn 0.3s ease-out;
        transition: opacity 0.3s ease-out;
    `;
    
    const icon = type === 'success' 
        ? '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>'
        : type === 'error'
        ? '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="m15 9-6 6M9 9l6 6"/></svg>'
        : '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 8v4M12 16h.01"/></svg>';
    
    toast.innerHTML = `${icon}<span>${message}</span>`;
    container.appendChild(toast);
    
    // Auto-remove after duration
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, duration);
}

// Expose showToast globally
window.showToast = showToast;

// Add slide-in animation
if (!document.getElementById('toastAnimation')) {
    const style = document.createElement('style');
    style.id = 'toastAnimation';
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    `;
    document.head.appendChild(style);
}

/**
 * Form enhancements
 */
function initForms() {
    // Auto-focus first input in forms
    const firstInput = document.querySelector('form input:not([type="hidden"]):not([type="checkbox"])');
    if (firstInput && !firstInput.value) {
        firstInput.focus();
    }
    
    // Confirm on dangerous actions
    document.querySelectorAll('[data-confirm]').forEach(element => {
        element.addEventListener('click', function(e) {
            const message = this.getAttribute('data-confirm');
            if (!confirm(message)) {
                e.preventDefault();
                return false;
            }
        });
    });
    
    // Handle file input previews
    document.querySelectorAll('input[type="file"]').forEach(input => {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    let preview = input.parentElement.querySelector('.file-preview');
                    if (!preview) {
                        preview = document.createElement('img');
                        preview.className = 'file-preview';
                        preview.style.cssText = 'max-width: 200px; margin-top: 10px; border-radius: 6px; border: 1px solid var(--line);';
                        input.parentElement.appendChild(preview);
                    }
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    });
}

/**
 * Debounce function for search inputs
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

// Expose debounce globally
window.debounce = debounce;

/**
 * Format number as currency
 */
function formatCurrency(number) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(number);
}

// Expose formatCurrency globally
window.formatCurrency = formatCurrency;

/**
 * Format date
 */
function formatDate(date, format = 'short') {
    const d = new Date(date);
    const options = format === 'short' 
        ? { day: 'numeric', month: 'short', year: 'numeric' }
        : { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' };
    return new Intl.DateTimeFormat('id-ID', options).format(d);
}

// Expose formatDate globally
window.formatDate = formatDate;

/**
 * Copy to clipboard
 */
function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            showToast('Disalin ke clipboard', 'success');
        }).catch(() => {
            showToast('Gagal menyalin', 'error');
        });
    } else {
        // Fallback for older browsers
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();
        try {
            document.execCommand('copy');
            showToast('Disalin ke clipboard', 'success');
        } catch (err) {
            showToast('Gagal menyalin', 'error');
        }
        document.body.removeChild(textarea);
    }
}

// Expose copyToClipboard globally
window.copyToClipboard = copyToClipboard;

/**
 * Show Laravel validation errors as toasts
 */
window.addEventListener('load', function() {
    // Check for Laravel session flash messages
    const successMessage = document.querySelector('[data-success-message]');
    const errorMessage = document.querySelector('[data-error-message]');
    
    if (successMessage) {
        showToast(successMessage.getAttribute('data-success-message'), 'success');
    }
    
    if (errorMessage) {
        showToast(errorMessage.getAttribute('data-error-message'), 'error');
    }
});
