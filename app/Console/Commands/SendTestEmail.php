<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pemesanan;
use App\Models\PaketWisata;
use App\Models\Jadwal;
use App\Models\Pembayaran;
use App\Services\EmailService;
use Carbon\Carbon;
use App\Mail\BookingReviewMail;
use App\Mail\BookingStatusMail;
use Illuminate\Support\Facades\Mail;

class SendTestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test 
                            {type : Email type (booking, payment, cancellation, review, status)} 
                            {--email= : Recipient email address}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send test email with mock booking data';

    protected $emailService;

    /**
     * Create a new command instance.
     */
    public function __construct(EmailService $emailService)
    {
        parent::__construct();
        $this->emailService = $emailService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->argument('type');
        $recipientEmail = $this->option('email') ?? config('mail.test_address') ?? 'test@example.com';

        $this->info("Preparing to send {$type} test email to {$recipientEmail}...");

        try {
            // Generate mock booking data
            $mockPemesanan = $this->createMockBooking($type, $recipientEmail);

            // Send email based on type
            switch ($type) {
                case 'booking':
                    $result = $this->emailService->sendBookingConfirmation($mockPemesanan);
                    break;
                    
                case 'payment':
                    $result = $this->emailService->sendPaymentSuccess($mockPemesanan);
                    break;
                    
                case 'cancellation':
                    $result = $this->emailService->sendCancellationNotification(
                        $mockPemesanan,
                        'Test cancellation - Payment timeout exceeded'
                    );
                    break;

                case 'review':
                    $mockPemesanan->status = 'paid';
                    Mail::to($recipientEmail)->send(new BookingReviewMail($mockPemesanan));
                    $result = true;
                    break;

                case 'status':
                    $mockPemesanan->status = 'paid';
                    Mail::to($recipientEmail)->send(new BookingStatusMail($mockPemesanan, 'pending'));
                    $result = true;
                    break;
                    
                default:
                    $this->error("Invalid email type. Use: booking, payment, cancellation, review, or status");
                    return 1;
            }

            if ($result) {
                $this->info("✓ Test {$type} email sent successfully to {$recipientEmail}");
                $this->info("  Booking Code: {$mockPemesanan->kode_booking}");
                $this->info("  Check your inbox!");
            } else {
                $this->error("✗ Failed to send test email. Check logs for details.");
                return 1;
            }

            return 0;

        } catch (\Exception $e) {
            $this->error("✗ Error: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Create mock booking data for testing
     */
    protected function createMockBooking(string $type, string $email): Pemesanan
    {
        // Get or create test paket
        $paket = PaketWisata::where('is_active', true)->first();
        if (!$paket) {
            $paket = new PaketWisata([
                'nama_paket' => 'Test Package - Air Terjun Edukasi',
                'jenis_paket' => 'Education Tour',
                'harga' => 150000,
                'kuota' => 50,
                'is_active' => true,
            ]);
        }

        // Create test jadwal
        $jadwal = new Jadwal([
            'paket_id' => $paket->id,
            'tanggal' => Carbon::now()->addDays(7),
            'kuota_tersedia' => 50,
        ]);

        // Create mock pemesanan
        $pemesanan = new Pemesanan([
            'kode_booking' => 'TEST-' . now()->format('YmdHis') . '-' . rand(100, 999),
            'user_id' => null,
            'jadwal_id' => $jadwal->id ?? null,
            'paket_wisata_id' => $paket->id,
            'nama_lengkap' => 'Test Customer',
            'email' => $email,
            'no_hp' => '08123456789',
            'tanggal_kunjungan' => Carbon::now()->addDays(7)->format('Y-m-d'),
            'jumlah_orang' => 5,
            'total_harga' => 750000,
            'status' => $type === 'payment' ? 'paid' : ($type === 'cancellation' ? 'cancelled' : 'pending'),
        ]);

        // Set relationships
        $pemesanan->setRelation('paketWisata', $paket);
        $pemesanan->setRelation('jadwal', $jadwal);

        // Create mock pembayaran for payment test
        if ($type === 'payment') {
            $pembayaran = new Pembayaran([
                'pemesanan_id' => 1,
                'order_id' => 'TEST-ORDER-' . now()->timestamp,
                'gross_amount' => $pemesanan->total_harga,
                'snap_token' => 'test-snap-token',
                'status' => 'success',
                'payment_type' => 'bank_transfer',
                'transaction_id' => 'TXN-' . now()->timestamp,
                'paid_at' => now(),
            ]);
            $pemesanan->setRelation('pembayaran', $pembayaran);
        }

        return $pemesanan;
    }
}
