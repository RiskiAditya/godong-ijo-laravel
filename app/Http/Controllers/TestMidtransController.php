<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;

class TestMidtransController extends Controller
{
    /**
     * Show Midtrans test page
     */
    public function index()
    {
        return view('test.midtrans');
    }

    /**
     * Test Midtrans API connection
     */
    public function testApi()
    {
        try {
            // Check if in simulation mode
            $paymentMode = env('PAYMENT_MODE', 'live');
            
            if ($paymentMode === 'simulation') {
                // Return simulation mode info
                return response()->json([
                    'success' => true,
                    'message' => '🎭 Simulation Mode Active',
                    'data' => [
                        'snap_token' => 'SIMULATION-MODE-ACTIVE',
                        'order_id' => 'TEST-SIMULATION',
                        'client_key' => config('midtrans.client_key'),
                        'is_production' => config('midtrans.is_production'),
                        'payment_mode' => 'simulation',
                    ],
                    'instructions' => [
                        'Payment mode is set to SIMULATION',
                        'Bookings will be created without calling Midtrans API',
                        'Users will be redirected directly to confirmation page',
                        'To use real Midtrans, set PAYMENT_MODE=live in .env',
                        'Real payment flow is currently disabled for testing',
                    ],
                ], 200);
            }
            
            // Set Midtrans configuration
            // If server key is not configured, return diagnostic without calling Midtrans
            if (empty(config('midtrans.server_key'))) {
                Log::warning('Midtrans server key is not configured (TestMidtransController)');

                return response()->json([
                    'success' => false,
                    'message' => 'Midtrans server key not configured. Set MIDTRANS_SERVER_KEY in .env or enable simulation mode.',
                    'config' => [
                        'server_key_set' => !empty(config('midtrans.server_key')),
                        'client_key_set' => !empty(config('midtrans.client_key')),
                        'is_production' => config('midtrans.is_production'),
                        'payment_mode' => $paymentMode,
                    ],
                ], 400);
            }

            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production');
            Config::$isSanitized = config('midtrans.is_sanitized');
            Config::$is3ds = config('midtrans.is_3ds');
            
            // Configure cURL with timeout and SSL settings for development
            if (config('app.env') === 'local') {
                Config::$curlOptions = [
                    CURLOPT_HTTPHEADER => [],
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_SSL_VERIFYPEER => 0,
                    CURLOPT_TIMEOUT => 10, // 10 seconds timeout
                    CURLOPT_CONNECTTIMEOUT => 5, // 5 seconds connection timeout
                ];
            } else {
                Config::$curlOptions = [
                    CURLOPT_HTTPHEADER => [],
                    CURLOPT_TIMEOUT => 10,
                    CURLOPT_CONNECTTIMEOUT => 5,
                ];
            }

            // Create test transaction
            $orderId = 'TEST-' . time();
            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => 10000,
                ],
                'item_details' => [
                    [
                        'id' => 'test-item',
                        'price' => 10000,
                        'quantity' => 1,
                        'name' => 'Test Item',
                    ],
                ],
                'customer_details' => [
                    'first_name' => 'Test Customer',
                    'email' => 'test@example.com',
                    'phone' => '081234567890',
                ],
            ];

            // Try to get snap token
            $snapToken = Snap::getSnapToken($params);

            return response()->json([
                'success' => true,
                'message' => '✅ Midtrans API berhasil terhubung!',
                'data' => [
                    'snap_token' => $snapToken,
                    'order_id' => $orderId,
                    'client_key' => config('midtrans.client_key'),
                    'is_production' => config('midtrans.is_production'),
                    'server_key_prefix' => substr(config('midtrans.server_key'), 0, 10) . '...',
                    'payment_mode' => 'live',
                ],
                'instructions' => [
                    'Snap token berhasil dibuat',
                    'Payment gateway siap digunakan',
                    'Klik tombol "Buka Payment Popup" untuk test pembayaran',
                ],
            ], 200);

        } catch (\Exception $e) {
            // Get detailed error
            $errorMessage = $e->getMessage();
            $errorCode = method_exists($e, 'getCode') ? $e->getCode() : null;
            
            // Log error for debugging
            Log::error('Midtrans Test Error: ' . $errorMessage, [
                'code' => $errorCode,
                'trace' => $e->getTraceAsString(),
            ]);

            // Parse Midtrans error
            $diagnosis = $this->diagnoseMidtransError($errorMessage, $errorCode);

            return response()->json([
                'success' => false,
                'message' => '❌ Midtrans API Error',
                'error' => $errorMessage,
                'error_code' => $errorCode,
                'diagnosis' => $diagnosis,
                'config' => [
                    'server_key_set' => !empty(config('midtrans.server_key')),
                    'client_key_set' => !empty(config('midtrans.client_key')),
                    'is_production' => config('midtrans.is_production'),
                    'server_key_prefix' => substr(config('midtrans.server_key'), 0, 10) . '...',
                    'payment_mode' => env('PAYMENT_MODE', 'live'),
                ],
            ], 500);
        }
    }

    /**
     * Diagnose Midtrans error and provide solutions
     */
    private function diagnoseMidtransError($message, $code)
    {
        $diagnosis = [
            'problem' => '',
            'solution' => [],
            'documentation' => '',
        ];

        // Check for common error patterns
        if (strpos($message, 'Undefined array key') !== false || strpos($message, '10023') !== false) {
            $diagnosis['problem'] = 'Access denied / Unauthorized transaction (Error 10023)';
            $diagnosis['solution'] = [
                '1. Login ke Midtrans Dashboard: https://dashboard.sandbox.midtrans.com/',
                '2. Pilih menu Settings → Snap Preferences',
                '3. AKTIFKAN payment methods (Credit Card, Bank Transfer, E-Wallet, QRIS)',
                '4. Klik Save Changes',
                '5. Tunggu 1-2 menit lalu test ulang',
            ];
            $diagnosis['documentation'] = 'https://docs.midtrans.com/docs/snap-advanced-feature';
        } elseif (strpos($message, 'timed out') !== false || strpos($message, 'timeout') !== false || strpos($message, 'Resolving timed out') !== false) {
            $diagnosis['problem'] = 'Connection timeout - Tidak bisa koneksi ke Midtrans server';
            $diagnosis['solution'] = [
                '1. Cek koneksi internet Anda',
                '2. Pastikan firewall tidak memblokir koneksi ke api.sandbox.midtrans.com',
                '3. Coba restart router/modem',
                '4. Gunakan VPN jika koneksi terblokir ISP',
                '5. Cek di browser: https://api.sandbox.midtrans.com (harus bisa diakses)',
            ];
            $diagnosis['documentation'] = 'https://docs.midtrans.com/docs/overview';
        } elseif (strpos($message, 'Access forbidden') !== false || strpos($message, '401') !== false) {
            $diagnosis['problem'] = 'Invalid API credentials';
            $diagnosis['solution'] = [
                '1. Cek Server Key dan Client Key di dashboard',
                '2. Pastikan menggunakan Sandbox keys (bukan Production)',
                '3. Update keys di file .env',
                '4. Jalankan: php artisan config:clear',
            ];
            $diagnosis['documentation'] = 'https://docs.midtrans.com/docs/midtrans-api-keys';
        } elseif (strpos($message, 'cURL error') !== false || strpos($message, 'Connection') !== false) {
            $diagnosis['problem'] = 'Koneksi ke Midtrans server gagal';
            $diagnosis['solution'] = [
                '1. Cek koneksi internet',
                '2. Pastikan port 443 (HTTPS) tidak diblokir',
                '3. Jika pakai proxy, konfigurasikan di config',
            ];
            $diagnosis['documentation'] = 'https://docs.midtrans.com/docs/overview';
        } else {
            $diagnosis['problem'] = 'Unknown error dari Midtrans API';
            $diagnosis['solution'] = [
                '1. Cek log detail di storage/logs/laravel.log',
                '2. Verifikasi semua konfigurasi di .env',
                '3. Hubungi Midtrans support jika masalah berlanjut',
            ];
            $diagnosis['documentation'] = 'https://docs.midtrans.com/docs/status-code';
        }

        return $diagnosis;
    }
}
