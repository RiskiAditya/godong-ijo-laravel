<?php

namespace App\Http\Controllers;

use App\Models\Pemesanan;
use App\Models\Pembayaran;
use App\Models\PaketWisata;
use App\Models\Jadwal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentSuccessMail;
use App\Models\SystemSetting;
use Midtrans\Config;
use Midtrans\Snap;

class BookingController extends Controller
{
    protected $emailService;
    protected $notificationService;

    public function __construct(
        \App\Services\EmailService $emailService,
        \App\Services\NotificationService $notificationService
    ) {
        $this->emailService = $emailService;
        $this->notificationService = $notificationService;
        
        // Set Midtrans configuration
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
        
        // Configure cURL with timeout and SSL settings for development
        // IMPORTANT: Remove SSL bypass in production!
        if (config('app.env') === 'local') {
            Config::$curlOptions = [
                CURLOPT_HTTPHEADER => [],
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_TIMEOUT => 30, // 30 seconds timeout for payment gateway
                CURLOPT_CONNECTTIMEOUT => 10, // 10 seconds connection timeout
            ];
        } else {
            Config::$curlOptions = [
                CURLOPT_HTTPHEADER => [],
                CURLOPT_TIMEOUT => 30, // 30 seconds timeout for payment gateway
                CURLOPT_CONNECTTIMEOUT => 10, // 10 seconds connection timeout
            ];
        }
    }

    /**
     * Check if Midtrans server key is configured
     */
    private function isMidtransConfigured(): bool
    {
        return !empty(config('midtrans.server_key'));
    }

    /**
     * Store a new booking (guest checkout)
     */
    public function store(Request $request)
    {
        try {
            // Normalize common phone formatting before applying validation rules.
            if ($request->filled('no_hp')) {
                $request->merge([
                    'no_hp' => preg_replace('/[^0-9]/', '', $request->input('no_hp')),
                ]);
            }

            // Validate input
            $validated = $request->validate([
                'paket_wisata_id' => 'required|exists:paket_wisata,id',
                'nama_lengkap' => 'required|string|min:3|max:255',
                'email' => 'required|email|max:255',
                'no_hp' => [
                    'required',
                    'string',
                    'min:10',
                    'max:20',
                    'regex:/^(08|62)[0-9]{8,13}$/',
                ],
                'tanggal_kunjungan' => 'required|date|after_or_equal:today',
                'jumlah_orang' => 'required|integer|min:1|max:100',
                'package_specific_data' => 'nullable|array',
            ], [
                'no_hp.regex' => 'Nomor HP harus dimulai dengan 08 atau 62 dan berisi 10-15 digit angka',
            ]);
            
            // Clean phone number - remove all non-numeric characters
            $validated['no_hp'] = preg_replace('/[^0-9]/', '', $validated['no_hp']);
            
            // Validate cleaned phone number length
            if (strlen($validated['no_hp']) < 10 || strlen($validated['no_hp']) > 15) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor HP harus 10-15 digit angka',
                    'errors' => ['no_hp' => ['Nomor HP harus 10-15 digit angka']],
                ], 422);
            }

            // Get paket wisata
            $paket = PaketWisata::findOrFail($validated['paket_wisata_id']);

            // Check if paket is active
            if (!$paket->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Paket wisata tidak tersedia saat ini',
                ], 400);
            }

            // Check if package has price (not custom price)
            if ($paket->harga == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Paket ini memerlukan konsultasi harga. Silakan hubungi kami.',
                ], 400);
            }

            $bookingConfig = $paket->booking_config ?? [];
            $minimumPax = (int) ($bookingConfig['minimum_pax'] ?? 1);
            if ($paket->jenis_paket === 'Private Room' && $validated['jumlah_orang'] < $minimumPax) {
                return response()->json([
                    'success' => false,
                    'message' => "Minimal peserta untuk paket ini adalah {$minimumPax} orang.",
                    'errors' => ['jumlah_orang' => ["Minimal peserta adalah {$minimumPax} orang."]],
                ], 422);
            }

            // Find or create jadwal for the selected date with pessimistic lock to prevent race conditions
            DB::beginTransaction();

            try {
            $jadwal = Jadwal::lockForUpdate()
                ->where('paket_id', $paket->id)
                ->where('tanggal', $validated['tanggal_kunjungan'])
                ->first();
            
            if (!$jadwal) {
                $jadwal = Jadwal::create([
                    'paket_id' => $paket->id,
                    'tanggal' => $validated['tanggal_kunjungan'],
                    'kuota_tersedia' => $paket->kuota,
                ]);
            }

            // Check quota availability (within same transaction with lock)
            if (!$jadwal->isAvailable($validated['jumlah_orang'])) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Kuota tidak mencukupi untuk tanggal yang dipilih',
                    'available_quota' => $jadwal->kuota_tersedia,
                    'requested' => $validated['jumlah_orang'],
                ], 400);
            }

            // Calculate total price
            $totalHarga = ($bookingConfig['price_type'] ?? 'per_person') === 'package'
                ? (float) $paket->harga
                : (float) $paket->harga * $validated['jumlah_orang'];
                // Generate unique booking code using UUID to prevent race conditions
                // Format: BK-YYYYMMDD-UNIQUE6
                $kodeBooking = 'BK-' . now()->format('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));

                // Create pemesanan record with kode_booking
                $pemesanan = Pemesanan::create([
                    'kode_booking' => $kodeBooking,
                    'user_id' => null, // Guest checkout
                    'jadwal_id' => $jadwal->id,
                    'paket_wisata_id' => $paket->id, // Direct reference to paket (post-migration)
                    'nama_lengkap' => $validated['nama_lengkap'],
                    'email' => $validated['email'],
                    'no_hp' => $validated['no_hp'],
                    'jumlah_orang' => $validated['jumlah_orang'],
                    'package_specific_data' => $validated['package_specific_data'] ?? null,
                    'total_harga' => $totalHarga,
                    'status' => 'pending',
                ]);

                // Decrement quota
                $jadwal->decrementKuota($validated['jumlah_orang']);

                // Generate Order ID for Midtrans
                $orderId = 'BOOKING-' . $pemesanan->id . '-' . time();

                // Check payment mode
                $paymentMode = config('midtrans.payment_mode', 'live');
                $snapToken = null;

                if ($paymentMode === 'simulation' || !$this->isMidtransConfigured()) {
                    // SIMULATION MODE: Skip Midtrans, create fake snap token
                    if (!$this->isMidtransConfigured() && $paymentMode !== 'simulation') {
                        Log::warning('Midtrans server key missing — falling back to simulation mode for booking', [
                            'kode_booking' => $kodeBooking,
                            'order_id' => $orderId,
                        ]);
                    }
                    $snapToken = 'SIMULATION-' . bin2hex(random_bytes(16));
                    
                    Log::info('Payment Simulation Mode: Booking created without real Midtrans', [
                        'kode_booking' => $kodeBooking,
                        'order_id' => $orderId,
                    ]);
                } else {
                    // LIVE MODE: Use real Midtrans
                    $params = [
                        'transaction_details' => [
                            'order_id' => $orderId,
                            'gross_amount' => $totalHarga,
                        ],
                        'item_details' => [
                            [
                                'id' => 'paket-' . $paket->id,
                                'price' => $paket->harga,
                                'quantity' => $validated['jumlah_orang'],
                                'name' => $paket->nama_paket,
                            ],
                        ],
                        'customer_details' => [
                            'first_name' => $validated['nama_lengkap'],
                            'email' => $validated['email'],
                            'phone' => $validated['no_hp'],
                        ],
                        'enabled_payments' => [
                            'credit_card',
                            'bca_va',
                            'bni_va',
                            'bri_va',
                            'permata_va',
                            'other_va',
                            'gopay',
                            'shopeepay',
                            'qris',
                        ],
                        'callbacks' => [
                            'finish' => route('booking.confirmation', ['kode_booking' => $kodeBooking, 'from_payment' => '1']),
                        ],
                    ];

                    try {
                        $snapToken = Snap::getSnapToken($params);
                    } catch (\Exception $e) {
                        Log::error('Midtrans error: ' . $e->getMessage());
                        throw new \RuntimeException('Pembayaran Midtrans tidak dapat dibuat. Silakan coba lagi.', 0, $e);
                    }
                }

                // Create pembayaran record
                Pembayaran::create([
                    'pemesanan_id' => $pemesanan->id,
                    'order_id' => $orderId,
                    'gross_amount' => $totalHarga,
                    'snap_token' => $snapToken,
                    'status' => 'pending',
                ]);

                DB::commit();

                // Send booking confirmation email (after successful commit)
                $emailSent = true;
                try {
                    $this->sendBookingConfirmationEmail($pemesanan);
                } catch (\Exception $e) {
                    $emailSent = false;
                    Log::error('Failed to send booking confirmation email', [
                        'booking_code' => $pemesanan->kode_booking,
                        'error' => $e->getMessage(),
                    ]);
                }

                // Create notification for new booking
                try {
                    $this->notificationService->createBookingNotification($pemesanan);
                } catch (\Exception $e) {
                    Log::error('Failed to create booking notification', [
                        'booking_code' => $pemesanan->kode_booking,
                        'error' => $e->getMessage(),
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Booking berhasil dibuat',
                    'data' => [
                        'pemesanan_id' => $pemesanan->id,
                        'kode_booking' => $pemesanan->kode_booking,
                        'order_id' => $orderId,
                        'snap_token' => $snapToken,
                        'gross_amount' => $totalHarga,
                        'paket_nama' => $paket->nama_paket,
                        'tanggal_kunjungan' => $validated['tanggal_kunjungan'],
                        'jumlah_orang' => $validated['jumlah_orang'],
                        'payment_mode' => $paymentMode,
                        'email_sent' => $emailSent,
                        'redirect_url' => route('booking.confirmation', $kodeBooking),
                    ],
                    'warnings' => $emailSent ? [] : ['Email konfirmasi tidak dapat dikirim. Silakan simpan kode booking Anda.'],
                ], 200);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data yang Anda masukkan tidak valid',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('Booking error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan. Silakan coba lagi.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Check booking status by kode_booking
     */
    public function status($kodeBooking)
    {
        // Post-migration: Use direct paketWisata relationship instead of jadwal->paket chain
        // Migration: add_paket_wisata_id_and_backfill_jadwal_id
        $booking = Pemesanan::with(['paketWisata', 'jadwal', 'pembayaran'])
            ->where('kode_booking', $kodeBooking)
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'kode_booking' => $booking->kode_booking,
                'nama_lengkap' => $booking->nama_lengkap,
                'paket' => $booking->paketWisata->nama_paket ?? 'N/A',
                'tanggal_kunjungan' => $booking->jadwal->tanggal->format('d M Y') ?? null,
                'jumlah_orang' => $booking->jumlah_orang,
                'total_harga' => $booking->total_harga,
                'booking_status' => $booking->status,
                'payment_status' => $booking->pembayaran->status ?? null,
                'payment_type' => $booking->pembayaran->payment_type ?? null,
                'transaction_id' => $booking->pembayaran->transaction_id ?? null,
            ],
        ], 200);
    }

    /**
     * Handle Midtrans payment notification (webhook)
     */
    public function notification(Request $request)
    {
        // Extract only expected notification data (security: prevent mass assignment)
        $notification = $request->only([
            'order_id',
            'transaction_status',
            'fraud_status',
            'payment_type',
            'transaction_id',
            'signature_key',
            'gross_amount',
            'transaction_time',
            'status_code',
        ]);
        
        $orderId = $notification['order_id'] ?? null;
        $transactionStatus = $notification['transaction_status'] ?? null;
        $fraudStatus = $notification['fraud_status'] ?? null;
        $paymentType = $notification['payment_type'] ?? null;
        $transactionId = $notification['transaction_id'] ?? null;
        $signatureKey = $notification['signature_key'] ?? null;

        try {
            // Verify Midtrans signature for security
            $serverKey = config('midtrans.server_key');
            $grossAmount = $notification['gross_amount'] ?? null;
            
            // Generate signature hash
            $expectedSignature = hash('sha512', $orderId . $transactionStatus . $grossAmount . $serverKey);
            
            // Verify signature matches
            if ($signatureKey !== $expectedSignature) {
                Log::warning('Invalid Midtrans signature detected', [
                    'order_id' => $orderId,
                    'expected' => $expectedSignature,
                    'received' => $signatureKey,
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid signature',
                ], 403);
            }
            
            // Find pembayaran record
            $pembayaran = Pembayaran::where('order_id', $orderId)->first();

            if (!$pembayaran) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found',
                ], 404);
            }

            if (abs((float) $grossAmount - (float) $pembayaran->gross_amount) > 0.01) {
                Log::warning('Midtrans amount mismatch detected', [
                    'order_id' => $orderId,
                    'expected_amount' => $pembayaran->gross_amount,
                    'received_amount' => $grossAmount,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid transaction amount',
                ], 422);
            }

            $isFinalStatus = in_array($pembayaran->status, ['success', 'failed'], true);
            if ($isFinalStatus && in_array($transactionStatus, ['capture', 'settlement', 'deny', 'expire', 'cancel'], true)) {
                return response()->json(['success' => true]);
            }

            // Wrap all database operations in transaction for atomicity
            DB::transaction(function () use (
                $pembayaran,
                $transactionStatus,
                $fraudStatus,
                $paymentType,
                $transactionId
            ) {
                // Update payment status based on transaction status
                if ($transactionStatus == 'capture') {
                    if ($fraudStatus == 'accept') {
                        $updated = Pembayaran::whereKey($pembayaran->getKey())
                            ->whereNotIn('status', ['success', 'failed'])
                            ->update([
                            'status' => 'success',
                            'payment_type' => $paymentType,
                            'transaction_id' => $transactionId,
                            'paid_at' => now(),
                        ]);
                        if (!$updated) {
                            return;
                        }
                        $pembayaran->refresh();
                        $pembayaran->pemesanan->update(['status' => 'paid']);
                        
                        // Send payment success email within transaction
                        $this->sendPaymentSuccessEmail($pembayaran->pemesanan);
                        
                        // Create notification for successful payment
                        try {
                            $this->notificationService->createPaymentNotification($pembayaran->pemesanan);
                        } catch (\Exception $e) {
                            Log::error('Failed to create payment notification', [
                                'booking_code' => $pembayaran->pemesanan->kode_booking,
                                'error' => $e->getMessage(),
                            ]);
                        }
                    }
                } elseif ($transactionStatus == 'settlement') {
                    $updated = Pembayaran::whereKey($pembayaran->getKey())
                        ->whereNotIn('status', ['success', 'failed'])
                        ->update([
                        'status' => 'success',
                        'payment_type' => $paymentType,
                        'transaction_id' => $transactionId,
                        'paid_at' => now(),
                    ]);
                    if (!$updated) {
                        return;
                    }
                    $pembayaran->refresh();
                    $pembayaran->pemesanan->update(['status' => 'paid']);
                    
                    // Send payment success email within transaction
                    $this->sendPaymentSuccessEmail($pembayaran->pemesanan);
                    
                    // Create notification for successful payment
                    try {
                        $this->notificationService->createPaymentNotification($pembayaran->pemesanan);
                    } catch (\Exception $e) {
                        Log::error('Failed to create payment notification', [
                            'booking_code' => $pembayaran->pemesanan->kode_booking,
                            'error' => $e->getMessage(),
                        ]);
                    }
                } elseif ($transactionStatus == 'pending') {
                    $pembayaran->update([
                        'status' => 'pending',
                        'payment_type' => $paymentType,
                        'transaction_id' => $transactionId,
                    ]);
                } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                    $updated = Pembayaran::whereKey($pembayaran->getKey())
                        ->whereNotIn('status', ['success', 'failed'])
                        ->update([
                        'status' => 'failed',
                        'payment_type' => $paymentType,
                        'transaction_id' => $transactionId,
                    ]);
                    if (!$updated) {
                        return;
                    }
                    $pembayaran->refresh();
                    $pembayaran->pemesanan->update(['status' => 'cancelled']);
                    
                    // Create notification for cancellation
                    try {
                        $reason = match($transactionStatus) {
                            'deny' => 'Pembayaran ditolak',
                            'expire' => 'Pembayaran kadaluarsa',
                            'cancel' => 'Pembayaran dibatalkan',
                            default => 'Pembayaran gagal',
                        };
                        $this->sendCancellationEmail($pembayaran->pemesanan, $reason);
                        $this->notificationService->createCancellationNotification($pembayaran->pemesanan, $reason);
                    } catch (\Exception $e) {
                        Log::error('Failed to create cancellation notification', [
                            'booking_code' => $pembayaran->pemesanan->kode_booking,
                            'error' => $e->getMessage(),
                        ]);
                    }
                    
                    // Restore quota for failed/cancelled payments
                    // Check jadwal_id exists first (may be null for fishing bookings)
                    if ($pembayaran->pemesanan->jadwal_id) {
                        $pembayaran->pemesanan->jadwal->incrementKuota(
                            $pembayaran->pemesanan->jumlah_orang
                        );
                    }
                }
            });

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            // Log error with order_id, transaction_status, and exception details
            Log::error('Midtrans notification error', [
                'order_id' => $orderId ?? 'unknown',
                'transaction_status' => $transactionStatus ?? 'unknown',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            // Return JSON error response with 500 status
            return response()->json([
                'success' => false,
                'message' => 'Payment notification processing failed',
            ], 500);
        }
    }

    /**
     * Manual mark payment as paid (FOR TESTING ONLY - localhost/development)
     * This is a workaround for localhost where Midtrans webhooks don't work
     */
    public function markAsPaid($orderId)
    {
        // Only allow in non-production environment
        if (config('app.env') === 'production') {
            abort(403, 'This endpoint is only available in development environment');
        }

        try {
            // Find pembayaran record
            $pembayaran = Pembayaran::where('order_id', $orderId)->first();

            if (!$pembayaran) {
                return redirect()->back()->with('error', 'Order not found');
            }

            if (in_array($pembayaran->status, ['paid', 'success'])) {
                return redirect()->back()->with('info', 'Payment is already marked as paid');
            }

            // Update payment status to success
            $pembayaran->update([
                'status' => 'success',
                'payment_type' => 'manual_test',
                'paid_at' => now(),
            ]);

            // Update booking status to paid
            $pembayaran->pemesanan->update(['status' => 'paid']);
            
            // Send payment success email
            $this->sendPaymentSuccessEmail($pembayaran->pemesanan);

            return redirect()->route('booking.confirmation', $pembayaran->pemesanan->kode_booking)
                ->with('success', 'Payment marked as PAID successfully!');

        } catch (\Exception $e) {
            \Log::error('Mark as paid error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to mark as paid: ' . $e->getMessage());
        }
    }

    public function checkPaymentStatus($orderId)
    {
        try {
            // Configure Midtrans
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production');
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            // Get transaction status from Midtrans API
            $status = \Midtrans\Transaction::status($orderId);

            // Find pembayaran record
            $pembayaran = Pembayaran::where('order_id', $orderId)->first();

            if (!$pembayaran) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found',
                ], 404);
            }

            // Update payment status based on transaction status
            $transactionStatus = $status->transaction_status;
            $fraudStatus = $status->fraud_status ?? null;
            $paymentType = $status->payment_type;
            $transactionId = $status->transaction_id;

            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'accept') {
                    $pembayaran->update([
                        'status' => 'success',
                        'payment_type' => $paymentType,
                        'transaction_id' => $transactionId,
                        'paid_at' => now(),
                    ]);
                    $pembayaran->pemesanan->update(['status' => 'paid']);
                }
            } elseif ($transactionStatus == 'settlement') {
                $pembayaran->update([
                    'status' => 'success',
                    'payment_type' => $paymentType,
                    'transaction_id' => $transactionId,
                    'paid_at' => now(),
                ]);
                $pembayaran->pemesanan->update(['status' => 'paid']);
                
                // Send payment success email
                $this->sendPaymentSuccessEmail($pembayaran->pemesanan);
                
                // Create notification for successful payment
                try {
                    $this->notificationService->createPaymentNotification($pembayaran->pemesanan);
                } catch (\Exception $e) {
                    Log::error('Failed to create payment notification', [
                        'booking_code' => $pembayaran->pemesanan->kode_booking,
                        'error' => $e->getMessage(),
                    ]);
                }
            } elseif ($transactionStatus == 'pending') {
                $pembayaran->update([
                    'status' => 'pending',
                    'payment_type' => $paymentType,
                    'transaction_id' => $transactionId,
                ]);
            } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                $updated = Pembayaran::whereKey($pembayaran->getKey())
                    ->whereNotIn('status', ['success', 'failed'])
                    ->update([
                        'status' => 'failed',
                        'payment_type' => $paymentType,
                        'transaction_id' => $transactionId,
                    ]);
                if (!$updated) {
                    return response()->json([
                        'success' => true,
                        'order_id' => $orderId,
                        'transaction_status' => $transactionStatus,
                        'payment_status' => $pembayaran->status,
                        'booking_status' => $pembayaran->pemesanan->status,
                    ]);
                }

                $pembayaran->pemesanan->update(['status' => 'cancelled']);
                
                // Create notification for cancellation
                try {
                    $reason = match($transactionStatus) {
                        'deny' => 'Pembayaran ditolak',
                        'expire' => 'Pembayaran kadaluarsa',
                        'cancel' => 'Pembayaran dibatalkan',
                        default => 'Pembayaran gagal',
                    };
                    $this->sendCancellationEmail($pembayaran->pemesanan, $reason);
                    $this->notificationService->createCancellationNotification($pembayaran->pemesanan, $reason);
                } catch (\Exception $e) {
                    Log::error('Failed to create cancellation notification', [
                        'booking_code' => $pembayaran->pemesanan->kode_booking,
                        'error' => $e->getMessage(),
                    ]);
                }
                
                // Restore quota - only if jadwal_id exists
                if ($pembayaran->pemesanan->jadwal_id) {
                    $pembayaran->pemesanan->jadwal->incrementKuota($pembayaran->pemesanan->jumlah_orang);
                }
            }

            return response()->json([
                'success' => true,
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
                'payment_status' => $pembayaran->status,
                'booking_status' => $pembayaran->pemesanan->status,
            ]);

        } catch (\Exception $e) {
            Log::error('Midtrans check payment status error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get today's booking count for social proof
     */
    public function bookingsToday()
    {
        try {
            $count = Pemesanan::whereDate('created_at', today())
                ->whereIn('status', ['paid', 'pending'])
                ->count();

            return response()->json([
                'success' => true,
                'count' => $count,
                'last_updated' => now()->toIso8601String(),
            ], 200);

        } catch (\Exception $e) {
            Log::error('Bookings today error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch bookings count',
            ], 500);
        }
    }

    /**
     * Display booking confirmation page
     */
    public function confirmation(string $kodeBooking)
    {
        // Post-migration: Use direct paketWisata relationship instead of jadwal->paket chain
        // Migration: add_paket_wisata_id_and_backfill_jadwal_id
        $booking = Pemesanan::with(['paketWisata', 'jadwal', 'pembayaran'])
            ->where('kode_booking', $kodeBooking)
            ->first();

        // Return 404 if booking code not found
        if (!$booking) {
            abort(404, 'Booking tidak ditemukan');
        }

        // Prepare navigation data
        $navigationService = app(\App\Services\NavigationService::class);
        $navigation = $navigationService->getMainNavigation();
        $cta = $navigationService->getCTA();

        // Prepare SEO data
        $seoData = [
            'title' => 'Konfirmasi Booking - ' . $booking->kode_booking . ' | Godong Ijo',
            'description' => 'Konfirmasi booking ' . $booking->paketWisata->nama_paket . ' - ' . $booking->kode_booking,
            'keywords' => ['booking confirmation', 'godong ijo', 'wisata'],
            'og' => [
                'type' => 'website',
                'url' => url()->current(),
                'title' => 'Booking Confirmed - ' . $booking->kode_booking,
                'description' => 'Your booking at Godong Ijo has been confirmed.',
                'image' => asset('images/og-image.svg'),
            ],
        ];

        // Return confirmation view with all necessary data
        return view('booking.confirmation', compact('booking', 'navigation', 'cta', 'seoData'));
    }

    /**
     * Download E-Ticket PDF
     */
    public function downloadETicket(string $kodeBooking)
    {
        try {
            $eticketService = new \App\Services\ETicketService();
            return $eticketService->download($kodeBooking);
            
        } catch (\Exception $e) {
            Log::error('E-Ticket download error: ' . $e->getMessage());
            
            // Fallback: redirect to confirmation page with error message
            return redirect()->route('booking.confirmation', $kodeBooking)
                ->with('error', 'Gagal mengunduh e-ticket. Silakan coba lagi atau hubungi kami.');
        }
    }
    
    /**
     * Store fishing booking
     */
    public function storeFishingBooking(\App\Http\Requests\FishingBookingRequest $request)
    {
        try {
            // Get validated data
            $validated = $request->validated();
            
            // Start database transaction
            DB::beginTransaction();

            try {
                // Generate unique booking code using UUID to prevent race conditions
                // Format: GOD-YYYYMMDD-UNIQUE6
                $kodeBooking = 'GOD-' . now()->format('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));

                // Calculate price based on fishing type
                $estimasiTotal = $validated['jenis_pemancingan'] === 'kiloan'
                    ? null
                    : $this->calculateFishingPrice($validated);

                // Prepare package-specific data
                $packageSpecificData = [
                    'jenis_pemancingan' => $validated['jenis_pemancingan'],
                    'jumlah_joran' => $validated['jumlah_joran'],
                    'jam_kunjungan' => $validated['jam_kunjungan'],
                    'tanggal_kunjungan' => $validated['tanggal_kunjungan'],
                    'setuju_aturan' => (bool) ($validated['setuju_aturan'] ?? false),
                ];

                // Add type-specific data
                if ($validated['jenis_pemancingan'] === 'tarikan') {
                    $packageSpecificData['durasi'] = $validated['durasi'];
                    $packageSpecificData['tambahan_jam'] = $validated['tambahan_jam'] ?? 0;
                }

                if ($validated['jenis_pemancingan'] === 'sewa_joran' || ($validated['perlu_sewa_alat'] ?? false)) {
                    $packageSpecificData['ukuran_joran'] = $validated['ukuran_joran'] ?? null;
                    $packageSpecificData['perlu_sewa_alat'] = $validated['perlu_sewa_alat'] ?? false;
                }

                // Add umpan data if any
                if (($validated['qty_komet'] ?? 0) > 0 || ($validated['qty_umpan_jadi'] ?? 0) > 0) {
                    $packageSpecificData['umpan'] = [
                        'anak_ikan_komet' => $validated['qty_komet'] ?? 0,
                        'umpan_jadi_godongijo' => $validated['qty_umpan_jadi'] ?? 0,
                    ];
                }

                // Find Fishing Lake package (fail-fast if not found)
                // Changed from firstOrCreate to explicit lookup to avoid silent auto-creation
                // of catalog data from customer-facing controller
                $fishingPaket = PaketWisata::where('jenis_paket', 'Fishing Lake')
                    ->where('is_active', true)
                    ->first();
                
                if (!$fishingPaket) {
                    throw new \App\Exceptions\BookingException(
                        'Paket Fishing Lake tidak tersedia. Silakan hubungi administrator.',
                        ['jenis_paket' => 'Fishing Lake']
                    );
                }

                // Fishing quota is shared per visit date and measured in fishing rods.
                $jadwal = Jadwal::where('paket_id', $fishingPaket->id)
                    ->whereDate('tanggal', $validated['tanggal_kunjungan'])
                    ->lockForUpdate()
                    ->first();

                if (!$jadwal) {
                    $jadwal = Jadwal::create([
                        'paket_id' => $fishingPaket->id,
                        'tanggal' => $validated['tanggal_kunjungan'],
                        'kuota_tersedia' => $fishingPaket->kuota,
                    ]);
                }

                // Attach older fishing bookings that predate date-based quota tracking.
                $legacyBookings = Pemesanan::where('paket_wisata_id', $fishingPaket->id)
                    ->whereNull('jadwal_id')
                    ->whereDate('tanggal_kunjungan', $validated['tanggal_kunjungan'])
                    ->where('status', '!=', 'cancelled')
                    ->lockForUpdate()
                    ->get();

                foreach ($legacyBookings as $legacyBooking) {
                    $legacyBooking->update(['jadwal_id' => $jadwal->id]);
                    $jadwal->decrementKuota((int) $legacyBooking->jumlah_orang);
                }

                if (!$jadwal->isAvailable((int) $validated['jumlah_joran'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Kuota Fishing Lake tidak mencukupi untuk tanggal yang dipilih',
                        'available_quota' => $jadwal->kuota_tersedia,
                        'requested' => (int) $validated['jumlah_joran'],
                    ], 400);
                }

                // Create pemesanan record
                $pemesanan = Pemesanan::create([
                    'kode_booking' => $kodeBooking,
                    'user_id' => null, // Guest booking
                    'jadwal_id' => $jadwal->id,
                    'paket_wisata_id' => $fishingPaket->id, // Post-migration: direct relationship
                    'nama_lengkap' => $validated['nama_lengkap'],
                    'email' => $validated['email'],
                    'no_hp' => $validated['no_hp'],
                    'tanggal_kunjungan' => $validated['tanggal_kunjungan'],
                    'jam_kunjungan' => $validated['jam_kunjungan'],
                    'package_specific_data' => $packageSpecificData,
                    'catatan' => null,
                    'jumlah_orang' => $validated['jumlah_joran'], // Use jumlah_joran as jumlah_orang for compatibility
                    'total_harga' => $estimasiTotal,
                    'status' => 'pending',
                ]);

                $jadwal->decrementKuota((int) $validated['jumlah_joran']);

                // Generate Order ID for Midtrans
                $orderId = 'FISHING-' . $pemesanan->id . '-' . time();

                // Check payment mode
                $paymentMode = config('midtrans.payment_mode', 'live');
                $snapToken = null;

                $grossAmount = (float) ($estimasiTotal ?? 0);

                if ($paymentMode === 'simulation' || $estimasiTotal == 0 || !$this->isMidtransConfigured()) {
                    if (!$this->isMidtransConfigured() && $paymentMode !== 'simulation') {
                        Log::warning('Midtrans server key missing — falling back to simulation mode for fishing booking', [
                            'kode_booking' => $kodeBooking,
                            'order_id' => $orderId,
                        ]);
                    }
                    // SIMULATION MODE or Kiloan (no fixed price)
                    $snapToken = 'SIMULATION-' . bin2hex(random_bytes(16));
                    
                    Log::info('Fishing Booking - Simulation Mode or Kiloan', [
                        'kode_booking' => $kodeBooking,
                        'jenis' => $validated['jenis_pemancingan'],
                        'estimasi' => $estimasiTotal,
                    ]);
                } else {
                    // LIVE MODE: Use real Midtrans
                    $params = [
                        'transaction_details' => [
                            'order_id' => $orderId,
                            'gross_amount' => $grossAmount,
                        ],
                        'item_details' => [
                            [
                                'id' => 'fishing-' . $validated['jenis_pemancingan'],
                                'price' => $grossAmount,
                                'quantity' => 1,
                                'name' => 'Paket Pemancingan ' . ucfirst($validated['jenis_pemancingan']),
                            ],
                        ],
                        'customer_details' => [
                            'first_name' => $validated['nama_lengkap'],
                            'email' => $validated['email'],
                            'phone' => $validated['no_hp'],
                        ],
                        'enabled_payments' => [
                            'credit_card',
                            'bca_va',
                            'bni_va',
                            'bri_va',
                            'permata_va',
                            'other_va',
                            'gopay',
                            'shopeepay',
                            'qris',
                        ],
                        'callbacks' => [
                            'finish' => route('booking.confirmation', ['kode_booking' => $kodeBooking, 'from_payment' => '1']),
                        ],
                    ];

                    try {
                        $snapToken = Snap::getSnapToken($params);
                    } catch (\Exception $e) {
                        Log::error('Midtrans error: ' . $e->getMessage());
                        $snapToken = null;
                    }
                }

                // Create pembayaran record
                // For kiloan fishing, gross_amount is 0 (price determined after weighing fish)
                Pembayaran::create([
                    'pemesanan_id' => $pemesanan->id,
                    'order_id' => $orderId,
                    'gross_amount' => $estimasiTotal ?? 0,
                    'snap_token' => $snapToken,
                    'status' => 'pending',
                ]);

                DB::commit();

                $this->sendBookingConfirmationEmail($pemesanan);

                try {
                    $this->notificationService->createBookingNotification($pemesanan);
                } catch (\Exception $e) {
                    Log::error('Failed to create fishing booking notification', [
                        'booking_code' => $pemesanan->kode_booking,
                        'error' => $e->getMessage(),
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Booking berhasil dibuat',
                    'kode_booking' => $pemesanan->kode_booking,
                    'order_id' => $orderId,
                    'snap_token' => $snapToken,
                    'estimasi_total' => $estimasiTotal,
                    'jenis_pemancingan' => $validated['jenis_pemancingan'],
                    'redirect_url' => route('booking.confirmation', $kodeBooking),
                ], 200);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data yang Anda masukkan tidak valid',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('Fishing booking error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan. Silakan coba lagi.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Calculate fishing booking price
     */
    private function calculateFishingPrice(array $data): float
    {
        $umpanTotal = 0;
        $sewaTotal = 0;
        $mancingTotal = 0;

        // Calculate umpan (bait) total
        $umpanTotal = (($data['qty_komet'] ?? 0) * 11000) + (($data['qty_umpan_jadi'] ?? 0) * 11000);

        // Calculate sewa (rod rental) total
        if ($data['jenis_pemancingan'] === 'sewa_joran' || ($data['perlu_sewa_alat'] ?? false)) {
            $ukuranJoran = $data['ukuran_joran'] ?? null;
            if ($ukuranJoran) {
                $hargaUkuran = $ukuranJoran === 'standar' ? 20000 : 
                              ($ukuranJoran === 'besar' ? 50000 : 0);
                $sewaTotal = $hargaUkuran * ($data['jumlah_joran'] ?? 1);
            }
        }

        // Calculate fishing total based on type
        switch ($data['jenis_pemancingan']) {
            case 'tarikan':
                $durasi = $data['durasi'] ?? null;
                if ($durasi) {
                    $hargaDurasi = $durasi === '2' ? 80000 : 
                                  ($durasi === '4' ? 110000 : 0);
                    $tambahanJam = $data['tambahan_jam'] ?? 0;
                    $mancingTotal = ($hargaDurasi + ($tambahanJam * 40000)) * ($data['jumlah_joran'] ?? 1);
                }
                break;

            case 'jackpot':
                $mancingTotal = 210000 * ($data['jumlah_joran'] ?? 1);
                break;

            case 'sewa_joran':
            case 'kiloan':
                $mancingTotal = 0; // No fixed price for these types
                break;
        }

        // Total
        $total = $mancingTotal + $sewaTotal + $umpanTotal;

        // For kiloan, return 0 (price calculated at weighing)
        if ($data['jenis_pemancingan'] === 'kiloan') {
            return 0;
        }

        return $total;
    }

    /**
     * Send payment success email
     * 
     * @param Pemesanan $pemesanan
     * @return void
     */
    protected function sendPaymentSuccessEmail(Pemesanan $pemesanan): void
    {
        if (!SystemSetting::enabled('email_notification')) {
            return;
        }

        try {
            $this->emailService->sendPaymentSuccess($pemesanan);
        } catch (\Exception $e) {
            // Log error but don't fail the transaction
            Log::error('Failed to send payment success email', [
                'booking_code' => $pemesanan->kode_booking,
                'error' => $e->getMessage(),
            ]);
        }
    }
    
    /**
     * Send booking confirmation email
     * 
     * @param Pemesanan $pemesanan
     * @return void
     */
    protected function sendBookingConfirmationEmail(Pemesanan $pemesanan): void
    {
        if (!SystemSetting::enabled('email_notification')) {
            return;
        }

        try {
            $this->emailService->sendBookingConfirmation($pemesanan);
        } catch (\Exception $e) {
            // Log error but don't fail the transaction
            Log::error('Failed to send booking confirmation email', [
                'booking_code' => $pemesanan->kode_booking,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function sendCancellationEmail(Pemesanan $pemesanan, string $reason): void
    {
        if (!SystemSetting::enabled('email_notification')) {
            return;
        }

        try {
            $this->emailService->sendCancellationNotification($pemesanan, $reason);
        } catch (\Exception $e) {
            Log::error('Failed to send booking cancellation email', [
                'booking_code' => $pemesanan->kode_booking,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
