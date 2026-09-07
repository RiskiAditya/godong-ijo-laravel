<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Midtrans Payment Gateway Test</title>
    
    <!-- Midtrans Snap.js -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 800px;
            width: 100%;
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #059669, #047857);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .header p {
            opacity: 0.9;
            font-size: 14px;
        }

        .content {
            padding: 30px;
        }

        .test-section {
            margin-bottom: 30px;
        }

        .test-section h2 {
            font-size: 20px;
            color: #1f2937;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .config-box {
            background: #f3f4f6;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .config-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .config-item:last-child {
            border-bottom: none;
        }

        .config-label {
            font-weight: 600;
            color: #6b7280;
        }

        .config-value {
            color: #1f2937;
            font-family: 'Courier New', monospace;
        }

        .btn {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-bottom: 15px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #059669, #047857);
            color: white;
        }

        .btn-primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(5, 150, 105, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
        }

        .btn-secondary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(59, 130, 246, 0.4);
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .result-box {
            display: none;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }

        .result-box.success {
            background: #d1fae5;
            border: 2px solid #059669;
        }

        .result-box.error {
            background: #fee2e2;
            border: 2px solid #ef4444;
        }

        .result-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .result-box.success .result-title {
            color: #047857;
        }

        .result-box.error .result-title {
            color: #991b1b;
        }

        .result-content {
            color: #374151;
        }

        .result-item {
            padding: 8px 0;
        }

        .result-item strong {
            color: #1f2937;
        }

        .solution-steps {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
        }

        .solution-steps ol {
            margin-left: 20px;
        }

        .solution-steps li {
            margin-bottom: 8px;
            color: #374151;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .spinner {
            border: 4px solid #f3f4f6;
            border-top: 4px solid #059669;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-sandbox {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-production {
            background: #fee2e2;
            color: #991b1b;
        }

        .code {
            background: #1f2937;
            color: #10b981;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            overflow-x: auto;
            margin: 10px 0;
        }

        .info-box {
            background: #dbeafe;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            color: #1e40af;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🔐 Midtrans Payment Gateway Test</h1>
            <p>Diagnostic Tool untuk Testing Integrasi Midtrans Sandbox</p>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Configuration Info -->
            <div class="test-section">
                <h2>📋 Konfigurasi Saat Ini</h2>
                <div class="config-box">
                    <div class="config-item">
                        <span class="config-label">Environment:</span>
                        <span class="config-value">
                            @if(config('midtrans.is_production'))
                                <span class="badge badge-production">PRODUCTION</span>
                            @else
                                <span class="badge badge-sandbox">SANDBOX</span>
                            @endif
                        </span>
                    </div>
                    <div class="config-item">
                        <span class="config-label">Server Key:</span>
                        <span class="config-value">{{ substr(config('midtrans.server_key'), 0, 15) }}...</span>
                    </div>
                    <div class="config-item">
                        <span class="config-label">Client Key:</span>
                        <span class="config-value">{{ substr(config('midtrans.client_key'), 0, 15) }}...</span>
                    </div>
                    <div class="config-item">
                        <span class="config-label">3D Secure:</span>
                        <span class="config-value">{{ config('midtrans.is_3ds') ? 'Enabled' : 'Disabled' }}</span>
                    </div>
                </div>
            </div>

            <!-- Test Actions -->
            <div class="test-section">
                <h2>🧪 Test Connection</h2>
                
                <button class="btn btn-primary" id="testApiBtn" onclick="testMidtransApi()">
                    🚀 Test Midtrans API Connection
                </button>

                <button class="btn btn-secondary" id="openPaymentBtn" onclick="openPaymentPopup()" disabled>
                    💳 Buka Payment Popup (Setelah API Test Berhasil)
                </button>

                <!-- Loading -->
                <div class="loading" id="loading">
                    <div class="spinner"></div>
                    <p>Testing koneksi ke Midtrans...</p>
                </div>

                <!-- Result Box -->
                <div class="result-box" id="resultBox"></div>
            </div>

            <!-- Instructions -->
            <div class="test-section">
                <h2>📖 Cara Menggunakan</h2>
                <div class="info-box">
                    <strong>Langkah-langkah:</strong>
                    <ol style="margin: 10px 0 0 20px;">
                        <li>Klik tombol "Test Midtrans API Connection"</li>
                        <li>Lihat hasil test di kotak hijau/merah</li>
                        <li>Jika sukses (✅), tombol payment popup akan aktif</li>
                        <li>Jika error (❌), ikuti solusi yang diberikan</li>
                        <li>Setelah sukses, klik "Buka Payment Popup" untuk test payment</li>
                    </ol>
                </div>

                <div class="info-box" style="background: #fef3c7; border-color: #f59e0b; color: #92400e; margin-top: 15px;">
                    <strong>⚠️ Test Card (Sandbox Mode):</strong>
                    <div class="code">
Card Number: 4811 1111 1111 1114<br>
CVV: 123<br>
Exp Date: 01/30
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentSnapToken = null;

        async function testMidtransApi() {
            const testBtn = document.getElementById('testApiBtn');
            const paymentBtn = document.getElementById('openPaymentBtn');
            const loading = document.getElementById('loading');
            const resultBox = document.getElementById('resultBox');

            // Reset UI
            testBtn.disabled = true;
            paymentBtn.disabled = true;
            loading.style.display = 'block';
            resultBox.style.display = 'none';
            currentSnapToken = null;

            try {
                const response = await fetch('/test/midtrans/api', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                loading.style.display = 'none';
                resultBox.style.display = 'block';

                if (data.success) {
                    // Success
                    resultBox.className = 'result-box success';
                    resultBox.innerHTML = `
                        <div class="result-title">
                            ✅ ${data.message}
                        </div>
                        <div class="result-content">
                            <div class="result-item"><strong>Order ID:</strong> ${data.data.order_id}</div>
                            <div class="result-item"><strong>Snap Token:</strong> ${data.data.snap_token.substring(0, 30)}...</div>
                            <div class="result-item"><strong>Client Key:</strong> ${data.data.client_key}</div>
                            <div class="result-item" style="margin-top: 15px; padding-top: 15px; border-top: 2px solid #059669;">
                                <strong>Status:</strong> Payment gateway siap digunakan! 🎉
                            </div>
                            ${data.instructions ? `
                                <div style="margin-top: 15px;">
                                    <strong>Instruksi:</strong>
                                    <ul style="margin-left: 20px; margin-top: 5px;">
                                        ${data.instructions.map(inst => `<li>${inst}</li>`).join('')}
                                    </ul>
                                </div>
                            ` : ''}
                        </div>
                    `;

                    // Enable payment button
                    paymentBtn.disabled = false;
                    currentSnapToken = data.data.snap_token;

                } else {
                    // Error
                    resultBox.className = 'result-box error';
                    resultBox.innerHTML = `
                        <div class="result-title">
                            ❌ ${data.message}
                        </div>
                        <div class="result-content">
                            <div class="result-item"><strong>Error Message:</strong> ${data.error}</div>
                            ${data.error_code ? `<div class="result-item"><strong>Error Code:</strong> ${data.error_code}</div>` : ''}
                            
                            ${data.diagnosis ? `
                                <div style="margin-top: 20px;">
                                    <strong style="display: block; margin-bottom: 10px; font-size: 16px;">🔍 Diagnosis:</strong>
                                    <div style="background: white; padding: 15px; border-radius: 8px;">
                                        <div style="margin-bottom: 10px;"><strong>Problem:</strong> ${data.diagnosis.problem}</div>
                                        
                                        <div class="solution-steps">
                                            <strong>Solusi:</strong>
                                            <ol>
                                                ${data.diagnosis.solution.map(sol => `<li>${sol}</li>`).join('')}
                                            </ol>
                                        </div>
                                        
                                        ${data.diagnosis.documentation ? `
                                            <div style="margin-top: 10px;">
                                                <strong>Dokumentasi:</strong> 
                                                <a href="${data.diagnosis.documentation}" target="_blank" style="color: #2563eb;">${data.diagnosis.documentation}</a>
                                            </div>
                                        ` : ''}
                                    </div>
                                </div>
                            ` : ''}

                            ${data.config ? `
                                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #f87171;">
                                    <strong>Config Check:</strong>
                                    <ul style="margin-left: 20px; margin-top: 5px;">
                                        <li>Server Key: ${data.config.server_key_set ? '✅ Set' : '❌ Not Set'}</li>
                                        <li>Client Key: ${data.config.client_key_set ? '✅ Set' : '❌ Not Set'}</li>
                                        <li>Mode: ${data.config.is_production ? 'Production' : 'Sandbox'}</li>
                                    </ul>
                                </div>
                            ` : ''}
                        </div>
                    `;
                }

            } catch (error) {
                loading.style.display = 'none';
                resultBox.style.display = 'block';
                resultBox.className = 'result-box error';
                resultBox.innerHTML = `
                    <div class="result-title">
                        ❌ Connection Error
                    </div>
                    <div class="result-content">
                        <div class="result-item"><strong>Error:</strong> ${error.message}</div>
                        <div class="result-item" style="margin-top: 10px;">
                            Tidak bisa terhubung ke server. Pastikan:
                            <ul style="margin-left: 20px; margin-top: 5px;">
                                <li>Laravel server berjalan (php artisan serve)</li>
                                <li>Koneksi internet aktif</li>
                                <li>Port tidak diblokir firewall</li>
                            </ul>
                        </div>
                    </div>
                `;
            } finally {
                testBtn.disabled = false;
            }
        }

        function openPaymentPopup() {
            if (!currentSnapToken) {
                alert('Snap token tidak tersedia. Jalankan test API terlebih dahulu.');
                return;
            }

            // Open Midtrans Snap popup
            window.snap.pay(currentSnapToken, {
                onSuccess: function(result) {
                    alert('Payment Success!');
                    console.log('Payment result:', result);
                },
                onPending: function(result) {
                    alert('Waiting for payment...');
                    console.log('Payment pending:', result);
                },
                onError: function(result) {
                    alert('Payment failed!');
                    console.log('Payment error:', result);
                },
                onClose: function() {
                    console.log('Payment popup closed');
                }
            });
        }
    </script>
</body>
</html>
