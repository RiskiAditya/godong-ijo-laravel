/**
 * Progress Indicator Controller
 * 
 * Manages the booking progress indicator component dynamically.
 * Provides methods to update steps, animate transitions, and handle state changes.
 * 
 * Usage:
 *   const progressIndicator = new ProgressIndicator(containerElement, totalSteps);
 *   progressIndicator.setStep(2); // Move to step 2
 *   progressIndicator.nextStep(); // Go to next step
 *   progressIndicator.prevStep(); // Go to previous step
 */

class ProgressIndicator {
    constructor(container, totalSteps = 2) {
        this.container = container;
        this.totalSteps = totalSteps;
        this.currentStep = 1;
        this.stepLabels = {
            1: 'Isi Data Pemesanan',
            2: 'Pembayaran'
        };
        
        this.render();
    }

    /**
     * Get the label for a specific step
     */
    getStepLabel(step) {
        return this.stepLabels[step] || `Step ${step}`;
    }

    /**
     * Set custom step labels
     */
    setStepLabels(labels) {
        this.stepLabels = labels;
        this.render();
    }

    /**
     * Calculate progress percentage
     */
    getProgressPercentage() {
        return (this.currentStep / this.totalSteps) * 100;
    }

    /**
     * Set the current step
     */
    setStep(step) {
        if (step < 1 || step > this.totalSteps) {
            console.warn(`Invalid step: ${step}. Must be between 1 and ${this.totalSteps}`);
            return;
        }

        this.currentStep = step;
        this.update();
    }

    /**
     * Move to next step
     */
    nextStep() {
        if (this.currentStep < this.totalSteps) {
            this.currentStep++;
            this.update();
            return true;
        }
        return false;
    }

    /**
     * Move to previous step
     */
    prevStep() {
        if (this.currentStep > 1) {
            this.currentStep--;
            this.update();
            return true;
        }
        return false;
    }

    /**
     * Check if at first step
     */
    isFirstStep() {
        return this.currentStep === 1;
    }

    /**
     * Check if at last step
     */
    isLastStep() {
        return this.currentStep === this.totalSteps;
    }

    /**
     * Get current step number
     */
    getCurrentStep() {
        return this.currentStep;
    }

    /**
     * Render the complete progress indicator
     */
    render() {
        const html = `
            <div class="progress-indicator" data-current-step="${this.currentStep}" data-total-steps="${this.totalSteps}">
                <!-- Progress Bar Container -->
                <div class="progress-bar-container">
                    <div class="progress-bar-track">
                        <div class="progress-bar-fill" 
                             style="width: ${this.getProgressPercentage()}%"
                             role="progressbar"
                             aria-valuenow="${this.currentStep}"
                             aria-valuemin="1"
                             aria-valuemax="${this.totalSteps}">
                        </div>
                    </div>
                </div>

                <!-- Step Labels (Breadcrumb Style) -->
                <div class="progress-steps">
                    ${this.renderSteps()}
                </div>
            </div>
        `;

        this.container.innerHTML = html;

        // Announce step change to screen readers
        this.announceStepChange();
    }

    /**
     * Render individual steps
     */
    renderSteps() {
        let html = '';
        
        for (let step = 1; step <= this.totalSteps; step++) {
            const isActive = step === this.currentStep;
            const isCompleted = step < this.currentStep;
            const classes = `progress-step ${isActive ? 'active' : ''} ${isCompleted ? 'completed' : ''}`;

            html += `
                <div class="${classes}" data-step="${step}">
                    <!-- Step Number Badge -->
                    <div class="step-badge">
                        ${isCompleted ? this.renderCheckIcon() : `<span class="step-number">${step}</span>`}
                    </div>

                    <!-- Step Label -->
                    <div class="step-label">
                        <span class="step-text">
                            Step ${step} of ${this.totalSteps}: ${this.getStepLabel(step)}
                        </span>
                    </div>
                </div>
            `;

            // Add connector between steps
            if (step < this.totalSteps) {
                html += `<div class="step-connector ${isCompleted ? 'completed' : ''}"></div>`;
            }
        }

        return html;
    }

    /**
     * Render checkmark icon for completed steps
     */
    renderCheckIcon() {
        return `
            <svg class="step-check-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
        `;
    }

    /**
     * Update the progress indicator (more efficient than full render)
     */
    update() {
        const progressIndicator = this.container.querySelector('.progress-indicator');
        if (!progressIndicator) {
            this.render();
            return;
        }

        // Update data attributes
        progressIndicator.setAttribute('data-current-step', this.currentStep);

        // Update progress bar
        const progressBar = progressIndicator.querySelector('.progress-bar-fill');
        if (progressBar) {
            progressBar.style.width = `${this.getProgressPercentage()}%`;
            progressBar.setAttribute('aria-valuenow', this.currentStep);
        }

        // Update step states
        const steps = progressIndicator.querySelectorAll('.progress-step');
        steps.forEach((stepElement, index) => {
            const stepNumber = index + 1;
            const isActive = stepNumber === this.currentStep;
            const isCompleted = stepNumber < this.currentStep;

            // Update classes
            stepElement.classList.toggle('active', isActive);
            stepElement.classList.toggle('completed', isCompleted);

            // Update badge content
            const badge = stepElement.querySelector('.step-badge');
            if (badge) {
                if (isCompleted) {
                    badge.innerHTML = this.renderCheckIcon();
                } else {
                    badge.innerHTML = `<span class="step-number">${stepNumber}</span>`;
                }
            }
        });

        // Update connectors
        const connectors = progressIndicator.querySelectorAll('.step-connector');
        connectors.forEach((connector, index) => {
            const stepNumber = index + 1;
            connector.classList.toggle('completed', stepNumber < this.currentStep);
        });

        // Announce step change to screen readers
        this.announceStepChange();
    }

    /**
     * Announce step change to screen readers
     */
    announceStepChange() {
        // Create or update ARIA live region for accessibility
        let liveRegion = document.getElementById('progress-indicator-live-region');
        
        if (!liveRegion) {
            liveRegion = document.createElement('div');
            liveRegion.id = 'progress-indicator-live-region';
            liveRegion.setAttribute('role', 'status');
            liveRegion.setAttribute('aria-live', 'polite');
            liveRegion.setAttribute('aria-atomic', 'true');
            liveRegion.style.position = 'absolute';
            liveRegion.style.left = '-10000px';
            liveRegion.style.width = '1px';
            liveRegion.style.height = '1px';
            liveRegion.style.overflow = 'hidden';
            document.body.appendChild(liveRegion);
        }

        liveRegion.textContent = `Step ${this.currentStep} of ${this.totalSteps}: ${this.getStepLabel(this.currentStep)}`;
    }

    /**
     * Reset to first step
     */
    reset() {
        this.currentStep = 1;
        this.update();
    }

    /**
     * Destroy the progress indicator
     */
    destroy() {
        this.container.innerHTML = '';
        
        // Remove live region
        const liveRegion = document.getElementById('progress-indicator-live-region');
        if (liveRegion) {
            liveRegion.remove();
        }
    }
}

// Export for use in modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ProgressIndicator;
}

// Make available globally
if (typeof window !== 'undefined') {
    window.ProgressIndicator = ProgressIndicator;
}
