/**
 * Auto Formatter
 * Handles real-time form field formatting
 */

export class AutoFormatter {
    constructor() {
        this.formatters = {
            phone: this.formatPhone.bind(this),
            name: this.formatName.bind(this),
            email: this.formatEmail.bind(this)
        };
    }
    
    /**
     * Format phone number as 0812-3456-789 pattern
     */
    formatPhone(value) {
        // Remove all non-digits
        const cleaned = value.replace(/\D/g, '');
        
        // Format as 0812-3456-789
        if (cleaned.length <= 4) {
            return cleaned;
        }
        
        if (cleaned.length <= 8) {
            return `${cleaned.slice(0, 4)}-${cleaned.slice(4)}`;
        }
        
        return `${cleaned.slice(0, 4)}-${cleaned.slice(4, 8)}-${cleaned.slice(8, 11)}`;
    }
    
    /**
     * Capitalize first letter of each word
     */
    formatName(value) {
        return value
            .toLowerCase()
            .split(' ')
            .map(word => {
                if (word.length === 0) return word;
                return word.charAt(0).toUpperCase() + word.slice(1);
            })
            .join(' ');
    }
    
    /**
     * Convert email to lowercase
     */
    formatEmail(value) {
        return value.toLowerCase().trim();
    }
    
    /**
     * Get suggested email domains
     */
    getSuggestedDomains(partialEmail) {
        const domains = [
            '@gmail.com',
            '@yahoo.com',
            '@outlook.com',
            '@hotmail.com'
        ];
        
        const parts = partialEmail.split('@');
        
        // Only suggest if there's an @ symbol and partial domain
        if (parts.length === 2 && parts[1].length > 0) {
            const partial = '@' + parts[1].toLowerCase();
            return domains.filter(domain => 
                domain.toLowerCase().startsWith(partial)
            );
        }
        
        // If just @ is typed, return all domains
        if (parts.length === 2 && parts[1].length === 0) {
            return domains;
        }
        
        return [];
    }
    
    /**
     * Attach formatter to input field
     */
    attachToField(fieldElement, formatterType) {
        if (!fieldElement || !this.formatters[formatterType]) {
            console.warn('Invalid field or formatter type:', formatterType);
            return;
        }
        
        const formatter = this.formatters[formatterType];
        
        // Format on input with debounce for better performance
        let timeout;
        fieldElement.addEventListener('input', (e) => {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                const cursorPosition = e.target.selectionStart;
                const oldValue = e.target.value;
                const newValue = formatter(oldValue);
                
                if (oldValue !== newValue) {
                    e.target.value = newValue;
                    
                    // Try to maintain cursor position
                    const cursorOffset = newValue.length - oldValue.length;
                    e.target.setSelectionRange(
                        cursorPosition + cursorOffset,
                        cursorPosition + cursorOffset
                    );
                }
                
                // Show email suggestions
                if (formatterType === 'email') {
                    this.showEmailSuggestions(fieldElement, newValue);
                }
            }, 50); // 50ms debounce
        });
        
        // Format on blur (final formatting)
        fieldElement.addEventListener('blur', (e) => {
            const formatted = formatter(e.target.value);
            if (e.target.value !== formatted) {
                e.target.value = formatted;
                e.target.dispatchEvent(new Event('change', { bubbles: true }));
            }
            
            // Hide email suggestions on blur
            if (formatterType === 'email') {
                setTimeout(() => this.hideEmailSuggestions(fieldElement), 200);
            }
        });
    }
    
    /**
     * Show email domain suggestions
     */
    showEmailSuggestions(fieldElement, value) {
        const suggestions = this.getSuggestedDomains(value);
        
        if (suggestions.length === 0) {
            this.hideEmailSuggestions(fieldElement);
            return;
        }
        
        // Remove existing dropdown
        this.hideEmailSuggestions(fieldElement);
        
        // Create suggestions dropdown
        const dropdown = document.createElement('div');
        dropdown.className = 'email-suggestions-dropdown';
        dropdown.setAttribute('role', 'listbox');
        
        const emailPrefix = value.split('@')[0];
        
        suggestions.forEach((domain, index) => {
            const suggestion = document.createElement('div');
            suggestion.className = 'email-suggestion-item';
            suggestion.setAttribute('role', 'option');
            suggestion.setAttribute('tabindex', '0');
            suggestion.textContent = emailPrefix + domain;
            
            suggestion.addEventListener('click', () => {
                fieldElement.value = emailPrefix + domain;
                fieldElement.dispatchEvent(new Event('input', { bubbles: true }));
                fieldElement.dispatchEvent(new Event('change', { bubbles: true }));
                this.hideEmailSuggestions(fieldElement);
                fieldElement.focus();
            });
            
            suggestion.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    suggestion.click();
                }
            });
            
            dropdown.appendChild(suggestion);
        });
        
        // Position dropdown
        const rect = fieldElement.getBoundingClientRect();
        dropdown.style.top = `${rect.bottom + window.scrollY}px`;
        dropdown.style.left = `${rect.left + window.scrollX}px`;
        dropdown.style.width = `${rect.width}px`;
        
        document.body.appendChild(dropdown);
    }
    
    /**
     * Hide email suggestions dropdown
     */
    hideEmailSuggestions(fieldElement) {
        const existing = document.querySelectorAll('.email-suggestions-dropdown');
        existing.forEach(el => el.remove());
    }
}

// Add CSS styles dynamically
if (typeof document !== 'undefined') {
    const style = document.createElement('style');
    style.textContent = `
        .email-suggestions-dropdown {
            position: absolute;
            z-index: 1000;
            background: white;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            max-height: 200px;
            overflow-y: auto;
            margin-top: 0.25rem;
        }
        
        .email-suggestion-item {
            padding: 0.5rem 0.75rem;
            cursor: pointer;
            transition: background-color 0.15s ease;
            font-size: 0.875rem;
            color: #374151;
        }
        
        .email-suggestion-item:hover,
        .email-suggestion-item:focus {
            background-color: #f3f4f6;
            outline: none;
        }
        
        .email-suggestion-item:first-child {
            border-top-left-radius: 0.375rem;
            border-top-right-radius: 0.375rem;
        }
        
        .email-suggestion-item:last-child {
            border-bottom-left-radius: 0.375rem;
            border-bottom-right-radius: 0.375rem;
        }
    `;
    document.head.appendChild(style);
}

export default AutoFormatter;
