<?php

namespace App\Http\Controllers;

use App\Http\Requests\FishingBookingRequest;
use App\Models\PaketWisata;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use App\Services\BookingCreationService;
use App\Services\BookingEmailNotificationService;
use App\Services\ETicketService;
use App\Services\MidtransConfigService;
use App\Services\NavigationService;
use App\Services\NotificationService;
use App\Services\PaymentStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Midtrans\Config;
use Midtrans\Transaction;

class BookingController extends Controller
{
    public function __construct(
        private BookingEmailNotificationService $bookingEmailNotifications,
        private PaymentStatusService $paymentStatusService,
        private NotificationService $notificationService,
        private ?BookingCreationService $bookingCreationService = null,
        private ?MidtransConfigService $midtransConfigService = null,
    ) {
        $this->bookingCreationService ??= app(BookingCreationService::class);
        $this->midtransConfigService ??= app(MidtransConfigService::class);
        $this->midtransConfigService->configure();
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
            if (! $paket->is_active) {
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

            // Reuse an unfinished booking instead of reserving the same quota again
            // when a customer retries the payment form.
            $requestedOption = data_get($validated, 'package_specific_data.private_room_option');
            $existingBooking = Pemesanan::with(['pembayaran', 'paketWisata'])
                ->where('paket_wisata_id', $paket->id)
                ->where('email', $validated['email'])
                ->where(function ($query) use ($validated) {
                    $query->whereDate('tanggal_kunjungan', $validated['tanggal_kunjungan'])
                        ->orWhereHas('jadwal', fn ($jadwalQuery) => $jadwalQuery->whereDate('tanggal', $validated['tanggal_kunjungan']));
                })
                ->where('status', 'pending')
                ->whereHas('pembayaran', fn ($query) => $query->where('status', 'pending'))
                ->latest('id')
                ->get()
                ->first(function (Pemesanan $booking) use ($requestedOption) {
                    return data_get($booking->package_specific_data, 'private_room_option') === $requestedOption;
                });

            if ($existingBooking) {
                if (! $existingBooking->pembayaran?->snap_token) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Booking pending sudah ditemukan, tetapi token pembayaran tidak tersedia. Silakan hubungi admin dengan kode booking '.$existingBooking->kode_booking.'.',
                    ], 409);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Booking sebelumnya ditemukan. Silakan lanjutkan pembayaran.',
                    'data' => [
                        'pemesanan_id' => $existingBooking->id,
                        'kode_booking' => $existingBooking->kode_booking,
                        'order_id' => $existingBooking->pembayaran->order_id,
                        'snap_token' => $existingBooking->pembayaran->snap_token,
                        'gross_amount' => $existingBooking->pembayaran->gross_amount,
                        'paket_nama' => $existingBooking->paketWisata?->nama_paket ?? $paket->nama_paket,
                        'tanggal_kunjungan' => $existingBooking->tanggal_kunjungan?->toDateString() ?? $validated['tanggal_kunjungan'],
                        'jumlah_orang' => $existingBooking->jumlah_orang,
                        'payment_mode' => config('midtrans.payment_mode', 'live'),
                        'email_sent' => true,
                        'redirect_url' => route('booking.confirmation', $existingBooking->kode_booking),
                    ],
                ], 200);
            }

            $created = $this->bookingCreationService->createGuestBooking($validated);
            $pemesanan = $created['pemesanan'];
            $orderId = $created['order_id'];
            $snapToken = $created['snap_token'];
            $totalHarga = $created['gross_amount'];
            $paymentMode = $created['payment_mode'];
            $kodeBooking = $created['kode_booking'];

            $emailSent = true;
            try {
                $this->bookingEmailNotifications->confirmation($pemesanan);
            } catch (\Exception $e) {
                $emailSent = false;
                Log::error('Failed to send booking confirmation email', [
                    'booking_code' => $pemesanan->kode_booking,
                    'error' => $e->getMessage(),
                ]);
            }

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

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data yang Anda masukkan tidak valid',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Throwable $e) {
            if ($e instanceof \RuntimeException && str_contains($e->getMessage(), 'Kuota tidak mencukupi')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kuota untuk tanggal tersebut sudah penuh. Silakan pilih tanggal atau jumlah peserta lain.',
                ], 409);
            }

            Log::channel('stderr')->error('Booking error', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan. Silakan coba lagi.',
                'error' => $e instanceof \RuntimeException
                    ? $e->getMessage()
                    : null,
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

        if (! $booking) {
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
                'tanggal_kunjungan' => $booking->jadwal?->tanggal?->format('d M Y')
                    ?? $booking->tanggal_kunjungan?->format('d M Y'),
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
            $serverKey = config('midtrans.server_key');
            $grossAmount = $notification['gross_amount'] ?? null;
            $expectedSignature = hash('sha512', $orderId.$transactionStatus.$grossAmount.$serverKey);

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

            $pembayaran = Pembayaran::where('order_id', $orderId)->first();

            if (! $pembayaran) {
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

            $this->paymentStatusService->apply(
                $pembayaran,
                $transactionStatus,
                $fraudStatus,
                $paymentType,
                $transactionId,
            );

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Midtrans notification error', [
                'order_id' => $orderId ?? 'unknown',
                'transaction_status' => $transactionStatus ?? 'unknown',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

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
            $pembayaran = Pembayaran::where('order_id', $orderId)->first();

            if (! $pembayaran) {
                return redirect()->back()->with('error', 'Order not found');
            }

            if (in_array($pembayaran->status, ['success', 'failed'], true)) {
                return redirect()->back()->with('info', 'Payment is already processed');
            }

            $transactionId = 'manual_'.$orderId.'_'.now()->format('YmdHis');
            $applied = $this->paymentStatusService->apply(
                $pembayaran,
                'capture',
                'accept',
                'manual_test',
                $transactionId,
            );

            if ($applied === null) {
                return redirect()->back()->with('info', 'Payment status is already up to date');
            }

            return redirect()->route('booking.confirmation', $pembayaran->pemesanan->kode_booking)
                ->with('success', 'Payment marked as PAID successfully!');

        } catch (\Exception $e) {
            \Log::error('Mark as paid error: '.$e->getMessage());

            return redirect()->back()->with('error', 'Failed to mark as paid: '.$e->getMessage());
        }
    }

    public function checkPaymentStatus($orderId)
    {
        try {
            // Configure Midtrans
            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production');
            Config::$isSanitized = true;
            Config::$is3ds = true;

            // Get transaction status from Midtrans API
            $status = Transaction::status($orderId);

            // Find pembayaran record
            $pembayaran = Pembayaran::where('order_id', $orderId)->first();

            if (! $pembayaran) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found',
                ], 404);
            }

            $transactionStatus = $status->transaction_status;
            $fraudStatus = $status->fraud_status ?? null;
            $paymentType = $status->payment_type;
            $transactionId = $status->transaction_id;

            $this->paymentStatusService->apply(
                $pembayaran,
                $transactionStatus,
                $fraudStatus,
                $paymentType,
                $transactionId,
            );

            return response()->json([
                'success' => true,
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
                'payment_status' => $pembayaran->status,
                'booking_status' => $pembayaran->pemesanan->status,
            ]);

        } catch (\Exception $e) {
            Log::error('Midtrans check payment status error: '.$e->getMessage());

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
            Log::error('Bookings today error: '.$e->getMessage());

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
        if (! $booking) {
            abort(404, 'Booking tidak ditemukan');
        }

        // Prepare navigation data
        $navigationService = app(NavigationService::class);
        $navigation = $navigationService->getMainNavigation();
        $cta = $navigationService->getCTA();

        // Prepare SEO data
        $seoData = [
            'title' => 'Konfirmasi Booking - '.$booking->kode_booking.' | Godong Ijo',
            'description' => 'Konfirmasi booking '.($booking->paketWisata?->nama_paket ?? $booking->jadwal?->paket?->nama_paket ?? 'Paket wisata').' - '.$booking->kode_booking,
            'keywords' => ['booking confirmation', 'godong ijo', 'wisata'],
            'og' => [
                'type' => 'website',
                'url' => url()->current(),
                'title' => 'Booking Confirmed - '.$booking->kode_booking,
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
            $eticketService = new ETicketService;

            return $eticketService->download($kodeBooking);

        } catch (\Throwable $e) {
            Log::error('E-Ticket download error: '.$e->getMessage());

            // Fallback: redirect to confirmation page with error message
            return redirect()->route('booking.confirmation', $kodeBooking)
                ->with('error', 'Gagal mengunduh e-ticket. Silakan coba lagi atau hubungi kami.');
        }
    }

    /**
     * Store fishing booking
     */
    public function storeFishingBooking(FishingBookingRequest $request)
    {
        try {
            // Get validated data
            $validated = $request->validated();

            $created = $this->bookingCreationService->createFishingBooking($validated);
            $pemesanan = $created['pemesanan'];
            $kodeBooking = $created['kode_booking'];

            $this->bookingEmailNotifications->confirmation($pemesanan);

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
                'order_id' => $created['order_id'],
                'snap_token' => $created['snap_token'],
                'estimasi_total' => $created['estimasi_total'],
                'jenis_pemancingan' => $created['jenis_pemancingan'],
                'redirect_url' => route('booking.confirmation', $kodeBooking),
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data yang Anda masukkan tidak valid',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('Fishing booking error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan. Silakan coba lagi.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
