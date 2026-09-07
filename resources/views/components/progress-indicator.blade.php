{{-- Progress Indicator Component
    
    Usage:
    <x-progress-indicator :current-step="1" :total-steps="2" />
    
    Props:
    - currentStep: Current step number (required)
    - totalSteps: Total number of steps (required, default: 2)
--}}

@props(['currentStep' => 1, 'totalSteps' => 2])

<div class="progress-indicator" data-current-step="{{ $currentStep }}" data-total-steps="{{ $totalSteps }}">
    <!-- Progress Bar Container -->
    <div class="progress-bar-container">
        <div class="progress-bar-track">
            <div class="progress-bar-fill" 
                 style="width: {{ ($currentStep / $totalSteps) * 100 }}%"
                 role="progressbar"
                 aria-valuenow="{{ $currentStep }}"
                 aria-valuemin="1"
                 aria-valuemax="{{ $totalSteps }}">
            </div>
        </div>
    </div>

    <!-- Step Labels (Breadcrumb Style) -->
    <div class="progress-steps">
        @for ($step = 1; $step <= $totalSteps; $step++)
            <div class="progress-step {{ $step === $currentStep ? 'active' : '' }} {{ $step < $currentStep ? 'completed' : '' }}"
                 data-step="{{ $step }}">
                <!-- Step Number Badge -->
                <div class="step-badge">
                    @if ($step < $currentStep)
                        <svg class="step-check-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    @else
                        <span class="step-number">{{ $step }}</span>
                    @endif
                </div>

                <!-- Step Label -->
                <div class="step-label">
                    <span class="step-text">
                        Step {{ $step }} of {{ $totalSteps }}:
                        @if ($step === 1)
                            Isi Data Pemesanan
                        @elseif ($step === 2)
                            Pembayaran
                        @endif
                    </span>
                </div>
            </div>

            @if ($step < $totalSteps)
                <!-- Connector Line Between Steps -->
                <div class="step-connector {{ $step < $currentStep ? 'completed' : '' }}"></div>
            @endif
        @endfor
    </div>
</div>

<style>
/* Progress Indicator Container */
.progress-indicator {
    width: 100%;
    margin-bottom: 24px;
}

/* Progress Bar Styling */
.progress-bar-container {
    margin-bottom: 20px;
}

.progress-bar-track {
    width: 100%;
    height: 8px;
    background-color: #e5e7eb;
    border-radius: 9999px;
    overflow: hidden;
    position: relative;
}

.progress-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, #059669, #10b981);
    border-radius: 9999px;
    transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

/* Animated shimmer effect on progress bar */
.progress-bar-fill::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, 
        transparent, 
        rgba(255, 255, 255, 0.3), 
        transparent
    );
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { left: -100%; }
    100% { left: 100%; }
}

/* Step Labels Container (Breadcrumb Style) */
.progress-steps {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

/* Individual Step */
.progress-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    flex: 1;
    text-align: center;
    transition: all 0.3s ease;
}

/* Step Badge (Circle with Number) */
.step-badge {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #e5e7eb;
    color: #9ca3af;
    font-weight: 600;
    font-size: 16px;
    transition: all 0.3s ease;
    border: 3px solid #e5e7eb;
}

/* Active Step Badge */
.progress-step.active .step-badge {
    background-color: #059669;
    color: white;
    border-color: #059669;
    box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.1);
    transform: scale(1.1);
}

/* Completed Step Badge */
.progress-step.completed .step-badge {
    background-color: #10b981;
    color: white;
    border-color: #10b981;
}

/* Check Icon for Completed Steps */
.step-check-icon {
    width: 20px;
    height: 20px;
}

/* Step Number */
.step-number {
    display: block;
}

/* Step Label Text */
.step-label {
    width: 100%;
}

.step-text {
    font-size: 14px;
    font-weight: 500;
    color: #6b7280;
    transition: color 0.3s ease;
}

/* Active Step Label */
.progress-step.active .step-text {
    color: #059669;
    font-weight: 600;
}

/* Completed Step Label */
.progress-step.completed .step-text {
    color: #10b981;
}

/* Connector Line Between Steps */
.step-connector {
    flex: 0 0 auto;
    width: 40px;
    height: 3px;
    background-color: #e5e7eb;
    margin: 0 8px;
    margin-bottom: 24px;
    transition: background-color 0.3s ease;
}

.step-connector.completed {
    background-color: #10b981;
}

/* Mobile Responsive (< 768px) */
@media (max-width: 767px) {
    /* Compact layout for mobile */
    .progress-steps {
        gap: 8px;
    }

    .step-badge {
        width: 32px;
        height: 32px;
        font-size: 14px;
        border-width: 2px;
    }

    .progress-step.active .step-badge {
        box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
    }

    .step-check-icon {
        width: 16px;
        height: 16px;
    }

    .step-text {
        font-size: 12px;
    }

    .step-connector {
        width: 24px;
        height: 2px;
        margin: 0 4px;
        margin-bottom: 20px;
    }

    /* Stack labels on very small screens */
    @media (max-width: 479px) {
        .step-text {
            font-size: 11px;
            line-height: 1.3;
        }
    }
}

/* Desktop Enhanced Styling (>= 768px) */
@media (min-width: 768px) {
    .progress-bar-track {
        height: 10px;
    }

    .step-badge {
        width: 48px;
        height: 48px;
        font-size: 18px;
    }

    .step-text {
        font-size: 15px;
    }

    .step-connector {
        width: 60px;
    }
}

/* Reduced Motion Support */
@media (prefers-reduced-motion: reduce) {
    .progress-bar-fill,
    .step-badge,
    .step-text,
    .step-connector,
    .progress-step {
        transition: none;
    }

    .progress-bar-fill::after {
        animation: none;
    }

    .progress-step.active .step-badge {
        transform: none;
    }
}

/* Print Styles */
@media print {
    .progress-indicator {
        display: none;
    }
}

/* Accessibility: Focus States */
.progress-step:focus-visible {
    outline: 2px solid #059669;
    outline-offset: 4px;
    border-radius: 8px;
}

/* High Contrast Mode Support */
@media (prefers-contrast: high) {
    .step-badge {
        border-width: 3px;
    }

    .progress-step.active .step-badge {
        border-width: 4px;
    }
}
</style>
