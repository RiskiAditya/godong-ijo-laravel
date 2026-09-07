/**
 * Loading State Manager
 * Handles multi-stage loading feedback during booking process
 */

export class LoadingStateManager {
    constructor(containerElement) {
        this.container = containerElement;
        this.currentStage = null;
    }
    
    /**
     * Start loading with specified stage
     */
    startLoading(stage) {
        this.currentStage = stage;
        this.clearLoadingDisplay();
    }
    
    /**
     * Transition to next stage with animation
     */
    nextStage(stage) {
        this.currentStage = stage;
        this.clearLoadingDisplay();
    }
    
    /**
     * Show success animation with checkmark
     */
    showSuccess(message = 'Booking berhasil!') {
        this.renderSuccess(message);
    }
    
    /**
     * Show error message with suggested action
     */
    showError(message, suggestedAction = 'Silakan coba lagi') {
        this.renderError(message, suggestedAction);
    }
    
    /**
     * Display toast notification
     */
    showToast(message, type = 'info', duration = 5000) {
        const toast = this.createToast(message, type);
        document.body.appendChild(toast);
        
        // Auto dismiss after duration
        setTimeout(() => {
            toast.classList.add('toast-fade-out');
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }
    
    /**
     * Stop loading and clear UI
     */
    stopLoading() {
        this.currentStage = null;
        this.clearLoadingDisplay();
    }

    /**
     * Keep background booking stages invisible to the customer.
     */
    clearLoadingDisplay() {
        if (this.container) {
            this.container.innerHTML = '';
        }
    }
    
    /**
     * Render current stage UI
     */
    render() {
        this.clearLoadingDisplay();
    }
    
    /**
     * Render success state
     */
    renderSuccess(message) {
        if (!this.container) return;
        
        this.container.innerHTML = `
            <div class="success-state-container" role="status" aria-live="polite">
                <div class="success-checkmark">
                    <svg class="checkmark-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                        <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none"/>
                        <path class="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                    </svg>
                </div>
                <div class="success-message">${message}</div>
            </div>
        `;
        
        // Trigger animation
        setTimeout(() => {
            const circle = this.container.querySelector('.checkmark-circle');
            const check = this.container.querySelector('.checkmark-check');
            if (circle) circle.style.strokeDashoffset = '0';
            if (check) check.style.strokeDashoffset = '0';
        }, 100);
    }
    
    /**
     * Render error state
     */
    renderError(message, suggestedAction) {
        if (!this.container) return;
        
        this.container.innerHTML = `
            <div class="error-state-container" role="alert" aria-live="assertive">
                <div class="error-icon">
                    <svg class="h-12 w-12 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="error-message">${message}</div>
                <div class="error-action">${suggestedAction}</div>
            </div>
        `;
    }
    
    /**
     * Create toast notification element
     */
    createToast(message, type) {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'polite');
        
        const icons = {
            info: 'ℹ️',
            success: '✅',
            error: '❌',
            warning: '⚠️'
        };
        
        const colors = {
            info: 'bg-blue-500',
            success: 'bg-green-500',
            error: 'bg-red-500',
            warning: 'bg-yellow-500'
        };
        
        toast.innerHTML = `
            <div class="toast-content ${colors[type] || colors.info}">
                <span class="toast-icon">${icons[type] || icons.info}</span>
                <span class="toast-message">${message}</span>
                <button class="toast-close" aria-label="Close">&times;</button>
            </div>
        `;
        
        // Add close button handler
        const closeBtn = toast.querySelector('.toast-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                toast.classList.add('toast-fade-out');
                setTimeout(() => toast.remove(), 300);
            });
        }
        
        return toast;
    }
}

// Add CSS styles dynamically
if (typeof document !== 'undefined') {
    const style = document.createElement('style');
    style.textContent = `
        .loading-state-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .loading-spinner {
            margin-bottom: 1rem;
        }
        
        .loading-message {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1rem;
            color: #374151;
        }
        
        .loading-icon {
            font-size: 1.5rem;
        }
        
        .success-state-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 2rem;
        }
        
        .success-checkmark {
            width: 80px;
            height: 80px;
            margin-bottom: 1rem;
        }
        
        .checkmark-icon {
            width: 100%;
            height: 100%;
        }
        
        .checkmark-circle {
            stroke: #059669;
            stroke-width: 2;
            stroke-dasharray: 166;
            stroke-dashoffset: 166;
            animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
        }
        
        .checkmark-check {
            stroke: #059669;
            stroke-width: 3;
            stroke-linecap: round;
            stroke-dasharray: 48;
            stroke-dashoffset: 48;
            animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.4s forwards;
        }
        
        @keyframes stroke {
            100% {
                stroke-dashoffset: 0;
            }
        }
        
        .success-message {
            font-size: 1.25rem;
            font-weight: 600;
            color: #059669;
            text-align: center;
        }
        
        .error-state-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 2rem;
        }
        
        .error-icon {
            margin-bottom: 1rem;
        }
        
        .error-message {
            font-size: 1rem;
            font-weight: 600;
            color: #dc2626;
            text-align: center;
            margin-bottom: 0.5rem;
        }
        
        .error-action {
            font-size: 0.875rem;
            color: #6b7280;
            text-align: center;
        }
        
        /* Toast Styles */
        .toast {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 9999;
            min-width: 300px;
            max-width: 400px;
            animation: toast-slide-in 0.3s ease-out;
        }
        
        .toast-content {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
            border-radius: 0.5rem;
            color: white;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        .toast-icon {
            font-size: 1.25rem;
            flex-shrink: 0;
        }
        
        .toast-message {
            flex: 1;
            font-size: 0.875rem;
            line-height: 1.4;
        }
        
        .toast-close {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            line-height: 1;
            cursor: pointer;
            padding: 0;
            margin-left: 0.5rem;
            opacity: 0.8;
            transition: opacity 0.2s;
        }
        
        .toast-close:hover {
            opacity: 1;
        }
        
        .toast-fade-out {
            animation: toast-fade-out 0.3s ease-out forwards;
        }
        
        @keyframes toast-slide-in {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes toast-fade-out {
            from {
                opacity: 1;
            }
            to {
                opacity: 0;
                transform: translateY(-10px);
            }
        }
        
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
        
        .animate-spin {
            animation: spin 1s linear infinite;
        }
    `;
    document.head.appendChild(style);
}

export default LoadingStateManager;
