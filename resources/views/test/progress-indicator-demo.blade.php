<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progress Indicator Demo - Godong Ijo Booking</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Progress Indicator Component Demo</h1>
        <p class="text-gray-600 mb-8">Testing the progress indicator UI component for the booking process</p>

        <!-- Demo Section: Step 1 -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Step 1: Data Entry</h2>
            <x-progress-indicator :current-step="1" :total-steps="2" />
            <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                <p class="text-sm text-blue-800">This shows the progress when the user is filling out the booking form (Step 1 of 2).</p>
            </div>
        </div>

        <!-- Demo Section: Step 2 -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Step 2: Payment</h2>
            <x-progress-indicator :current-step="2" :total-steps="2" />
            <div class="mt-4 p-4 bg-green-50 rounded-lg">
                <p class="text-sm text-green-800">This shows the progress when the user is on the payment step (Step 2 of 2). Note the first step shows as completed with a checkmark.</p>
            </div>
        </div>

        <!-- Interactive Demo -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Interactive Demo</h2>
            <div id="interactiveDemo"></div>
            <div class="mt-6 flex gap-4">
                <button onclick="setStep(1)" 
                        class="px-6 py-2 bg-emerald-600 text-white rounded-lg font-medium hover:bg-emerald-700 transition">
                    Go to Step 1
                </button>
                <button onclick="setStep(2)" 
                        class="px-6 py-2 bg-emerald-600 text-white rounded-lg font-medium hover:bg-emerald-700 transition">
                    Go to Step 2
                </button>
            </div>
        </div>

        <!-- Mobile Preview -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Mobile Preview (320px width)</h2>
            <div class="border-4 border-gray-300 rounded-lg p-4" style="max-width: 320px; margin: 0 auto;">
                <x-progress-indicator :current-step="1" :total-steps="2" />
            </div>
        </div>

        <!-- Component Usage Guide -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Usage Guide</h2>
            <div class="space-y-4">
                <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Basic Usage:</h3>
                    <pre class="bg-gray-100 p-4 rounded-lg overflow-x-auto"><code>&lt;x-progress-indicator :current-step="1" :total-steps="2" /&gt;</code></pre>
                </div>
                
                <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Props:</h3>
                    <ul class="list-disc list-inside space-y-1 text-gray-600">
                        <li><code class="bg-gray-100 px-2 py-1 rounded">currentStep</code> - Current step number (1 or 2)</li>
                        <li><code class="bg-gray-100 px-2 py-1 rounded">totalSteps</code> - Total number of steps (default: 2)</li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Features:</h3>
                    <ul class="list-disc list-inside space-y-1 text-gray-600">
                        <li>✅ Responsive design (desktop and mobile optimized)</li>
                        <li>✅ Visual progress bar with animated fill</li>
                        <li>✅ Numbered step labels with descriptions</li>
                        <li>✅ Distinct color highlighting for current step (green)</li>
                        <li>✅ Checkmark icon for completed steps</li>
                        <li>✅ Smooth animations and transitions</li>
                        <li>✅ Accessible (ARIA labels, keyboard navigation support)</li>
                        <li>✅ Reduced motion support</li>
                        <li>✅ Print-friendly (hidden in print)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Interactive demo functionality
        function setStep(step) {
            const demoContainer = document.getElementById('interactiveDemo');
            
            // Calculate progress percentage
            const percentage = (step / 2) * 100;
            
            // Generate HTML for the progress indicator
            const html = `
                <div class="progress-indicator" data-current-step="${step}" data-total-steps="2">
                    <div class="progress-bar-container">
                        <div class="progress-bar-track">
                            <div class="progress-bar-fill" 
                                 style="width: ${percentage}%"
                                 role="progressbar"
                                 aria-valuenow="${step}"
                                 aria-valuemin="1"
                                 aria-valuemax="2">
                            </div>
                        </div>
                    </div>

                    <div class="progress-steps">
                        ${[1, 2].map((s, index) => `
                            <div class="progress-step ${s === step ? 'active' : ''} ${s < step ? 'completed' : ''}"
                                 data-step="${s}">
                                <div class="step-badge">
                                    ${s < step ? `
                                        <svg class="step-check-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    ` : `
                                        <span class="step-number">${s}</span>
                                    `}
                                </div>
                                <div class="step-label">
                                    <span class="step-text">
                                        Step ${s} of 2:
                                        ${s === 1 ? 'Isi Data Pemesanan' : 'Pembayaran'}
                                    </span>
                                </div>
                            </div>
                            ${index < 1 ? `
                                <div class="step-connector ${s < step ? 'completed' : ''}"></div>
                            ` : ''}
                        `).join('')}
                    </div>
                </div>
            `;
            
            demoContainer.innerHTML = html;
        }

        // Initialize interactive demo
        setStep(1);
    </script>
</body>
</html>
