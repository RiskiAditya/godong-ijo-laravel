/**
 * Form Validator
 * Handles client-side validation with friendly error messages
 */

export class FormValidator {
    constructor(formElement) {
        this.form = formElement;
        this.rules = this.defineRules();
        this.friendlyMessages = this.defineFriendlyMessages();
        this.errors = {};
    }
    
    /**
     * Define validation rules for each field
     */
    defineRules() {
        return {
            nama_lengkap: {
                required: true,
                minLength: 3,
                maxLength: 255,
                pattern: /^[a-zA-Z\s]+$/
            },
            email: {
                required: true,
                email: true,
                maxLength: 255
            },
            no_hp: {
                required: true,
                phone: true,
                minLength: 10,
                maxLength: 15
            },
            tanggal_kunjungan: {
                required: true,
                date: true,
                minDate: 'today'
            },
            jumlah_orang: {
                required: true,
                integer: true,
                min: 1,
                max: 100
            }
        };
    }
    
    /**
     * Define friendly error messages in Indonesian
     */
    defineFriendlyMessages() {
        return {
            nama_lengkap: {
                required: 'Kami butuh nama lengkap Anda untuk konfirmasi',
                minLength: 'Nama minimal 3 karakter ya',
                maxLength: 'Nama terlalu panjang (maksimal 255 karakter)',
                pattern: 'Nama hanya boleh berisi huruf dan spasi'
            },
            email: {
                required: 'Email diperlukan untuk kirim konfirmasi',
                email: 'Email sepertinya belum benar, coba cek lagi ya!',
                maxLength: 'Email terlalu panjang'
            },
            no_hp: {
                required: 'Nomor WhatsApp diperlukan untuk konfirmasi',
                phone: 'Format nomor belum sesuai',
                minLength: 'Nomor WhatsApp minimal 10 digit agar kami bisa menghubungi Anda',
                maxLength: 'Nomor terlalu panjang'
            },
            tanggal_kunjungan: {
                required: 'Pilih tanggal kunjungan Anda',
                date: 'Format tanggal tidak valid',
                minDate: 'Tanggal tidak boleh di masa lalu'
            },
            jumlah_orang: {
                required: 'Masukkan jumlah orang',
                integer: 'Jumlah harus berupa angka',
                min: 'Minimal 1 orang',
                max: 'Maksimal 100 orang per booking'
            }
        };
    }
    
    /**
     * Validate a single field
     */
    validateField(fieldName, value) {
        const rules = this.rules[fieldName];
        const messages = this.friendlyMessages[fieldName];
        
        if (!rules || !messages) {
            return { valid: true, message: '' };
        }
        
        // Required check
        if (rules.required && !value) {
            return {
                valid: false,
                message: messages.required
            };
        }
        
        // Skip other validations if empty and not required
        if (!value) {
            return { valid: true, message: '' };
        }
        
        // String length validations
        if (rules.minLength && value.length < rules.minLength) {
            return {
                valid: false,
                message: messages.minLength
            };
        }
        
        if (rules.maxLength && value.length > rules.maxLength) {
            return {
                valid: false,
                message: messages.maxLength
            };
        }
        
        // Pattern validation
        if (rules.pattern && !rules.pattern.test(value)) {
            return {
                valid: false,
                message: messages.pattern
            };
        }
        
        // Email validation
        if (rules.email && !this.isValidEmail(value)) {
            return {
                valid: false,
                message: messages.email
            };
        }
        
        // Phone validation
        if (rules.phone) {
            const phoneDigits = value.replace(/\D/g, '');
            if (phoneDigits.length < rules.minLength) {
                return {
                    valid: false,
                    message: messages.minLength
                };
            }
            if (phoneDigits.length > rules.maxLength) {
                return {
                    valid: false,
                    message: messages.maxLength
                };
            }
        }
        
        // Integer validation
        if (rules.integer && !this.isInteger(value)) {
            return {
                valid: false,
                message: messages.integer
            };
        }
        
        // Min/Max number validation
        if (rules.min !== undefined) {
            const num = parseInt(value);
            if (num < rules.min) {
                return {
                    valid: false,
                    message: messages.min
                };
            }
        }
        
        if (rules.max !== undefined) {
            const num = parseInt(value);
            if (num > rules.max) {
                return {
                    valid: false,
                    message: messages.max
                };
            }
        }
        
        // Date validation
        if (rules.date && !this.isValidDate(value)) {
            return {
                valid: false,
                message: messages.date
            };
        }
        
        // Min date validation
        if (rules.minDate === 'today' && !this.isDateInFuture(value)) {
            return {
                valid: false,
                message: messages.minDate
            };
        }
        
        return { valid: true, message: '' };
    }
    
    /**
     * Validate all form fields
     */
    validateAll() {
        this.errors = {};
        let isValid = true;
        
        Object.keys(this.rules).forEach(fieldName => {
            const field = this.form.querySelector(`[name="${fieldName}"]`);
            if (!field) return;
            
            const result = this.validateField(fieldName, field.value);
            
            if (!result.valid) {
                this.errors[fieldName] = result.message;
                isValid = false;
            }
        });
        
        return {
            valid: isValid,
            errors: this.errors
        };
    }
    
    /**
     * Display error message for a field
     */
    displayError(fieldName, message) {
        const field = this.form.querySelector(`[name="${fieldName}"]`);
        if (!field) return;
        
        // Remove existing error
        this.clearFieldError(fieldName);
        
        // Add error class to field
        field.classList.add('field-error');
        field.setAttribute('aria-invalid', 'true');
        
        // Create error message element
        const errorEl = document.createElement('div');
        errorEl.className = 'field-error-message';
        errorEl.id = `${fieldName}-error`;
        errorEl.setAttribute('role', 'alert');
        errorEl.textContent = message;
        
        // Insert error message after field
        field.parentElement.appendChild(errorEl);
        
        // Associate error with field for screen readers
        field.setAttribute('aria-describedby', errorEl.id);
        
        // Animate error appearance
        setTimeout(() => errorEl.classList.add('field-error-visible'), 10);
    }
    
    /**
     * Clear error for a specific field
     */
    clearFieldError(fieldName) {
        const field = this.form.querySelector(`[name="${fieldName}"]`);
        if (!field) return;
        
        field.classList.remove('field-error');
        field.removeAttribute('aria-invalid');
        field.removeAttribute('aria-describedby');
        
        const errorEl = document.getElementById(`${fieldName}-error`);
        if (errorEl) {
            errorEl.remove();
        }
    }
    
    /**
     * Clear all errors
     */
    clearErrors() {
        Object.keys(this.rules).forEach(fieldName => {
            this.clearFieldError(fieldName);
        });
        this.errors = {};
    }
    
    /**
     * Attach validation to form fields
     */
    attachToForm() {
        Object.keys(this.rules).forEach(fieldName => {
            const field = this.form.querySelector(`[name="${fieldName}"]`);
            if (!field) return;
            
            // Validate on blur
            field.addEventListener('blur', () => {
                const result = this.validateField(fieldName, field.value);
                
                if (!result.valid) {
                    this.displayError(fieldName, result.message);
                } else {
                    this.clearFieldError(fieldName);
                }
            });
            
            // Clear error on input
            field.addEventListener('input', () => {
                if (this.errors[fieldName]) {
                    this.clearFieldError(fieldName);
                    delete this.errors[fieldName];
                }
            });
        });
        
        // Validate on submit
        this.form.addEventListener('submit', (e) => {
            const result = this.validateAll();
            
            if (!result.valid) {
                e.preventDefault();
                
                // Display all errors
                Object.keys(result.errors).forEach(fieldName => {
                    this.displayError(fieldName, result.errors[fieldName]);
                });
                
                // Focus first error field
                const firstErrorField = Object.keys(result.errors)[0];
                const field = this.form.querySelector(`[name="${firstErrorField}"]`);
                if (field) {
                    field.focus();
                    field.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    }
    
    // Helper validation methods
    
    isValidEmail(email) {
        // RFC 5322 simplified email regex
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    isInteger(value) {
        return /^\d+$/.test(value);
    }
    
    isValidDate(date) {
        const d = new Date(date);
        return d instanceof Date && !isNaN(d);
    }
    
    isDateInFuture(date) {
        const selected = new Date(date);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        return selected >= today;
    }
}

// Add CSS styles dynamically
if (typeof document !== 'undefined') {
    const style = document.createElement('style');
    style.textContent = `
        .field-error {
            border-color: #ef4444 !important;
            background-color: #fef2f2 !important;
        }
        
        .field-error:focus {
            outline: 2px solid #ef4444 !important;
            outline-offset: 2px;
        }
        
        .field-error-message {
            display: block;
            margin-top: 0.5rem;
            font-size: 0.875rem;
            color: #dc2626;
            opacity: 0;
            transform: translateY(-10px);
            transition: opacity 0.2s ease, transform 0.2s ease;
        }
        
        .field-error-message.field-error-visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        .field-error-message::before {
            content: '⚠️ ';
            margin-right: 0.25rem;
        }
        
        /* Shake animation for error fields */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        
        .field-error {
            animation: shake 0.3s ease-in-out;
        }
    `;
    document.head.appendChild(style);
}

export default FormValidator;
