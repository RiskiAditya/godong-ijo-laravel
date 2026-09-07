<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pemesanan;
use App\Services\EmailService;

class SendBookingEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send-booking 
                            {kode_booking : Booking code (e.g., BK20260125001)} 
                            {type : Email type (booking, payment, cancellation)}
                            {--reason= : Cancellation reason (required for cancellation type)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email notification for existing booking in database';

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
        $kodeBooking = $this->argument('kode_booking');
        $type = $this->argument('type');
        $reason = $this->option('reason');

        // Find booking in database
        $this->info("Searching for booking: {$kodeBooking}...");
        
        $pemesanan = Pemesanan::with(['paketWisata', 'jadwal.paket', 'pembayaran'])
            ->where('kode_booking', $kodeBooking)
            ->first();

        if (!$pemesanan) {
            $this->error("✗ Booking not found: {$kodeBooking}");
            $this->info("  Please check the booking code and try again.");
            return 1;
        }

        // Validate email exists
        if (empty($pemesanan->email)) {
            $this->error("✗ No email address found for this booking.");
            $this->info("  This booking doesn't have an email address (might be a walk-in or fishing booking).");
            return 1;
        }

        // Display booking info
        $this->info("\n📋 Booking Details:");
        $this->line("  Code        : {$pemesanan->kode_booking}");
        $this->line("  Customer    : {$pemesanan->nama_lengkap}");
        $this->line("  Email       : {$pemesanan->email}");
        $this->line("  Package     : " . ($pemesanan->paketWisata->nama_paket ?? 'N/A'));
        $this->line("  Status      : {$pemesanan->status}");
        $this->line("  Total       : Rp " . number_format($pemesanan->total_harga, 0, ',', '.'));

        // Confirm before sending
        if (!$this->confirm("\n📧 Send {$type} email to {$pemesanan->email}?", true)) {
            $this->info("Email sending cancelled.");
            return 0;
        }

        try {
            // Send email based on type
            $this->info("\nSending {$type} email...");
            
            switch ($type) {
                case 'booking':
                    // Validate booking email requirements
                    if (!$pemesanan->paketWisata && !$pemesanan->jadwal) {
                        $this->error("✗ Cannot send booking confirmation: Missing package or schedule data");
                        return 1;
                    }
                    
                    $result = $this->emailService->sendBookingConfirmation($pemesanan);
                    break;
                    
                case 'payment':
                    // Validate payment email requirements
                    if (!$pemesanan->pembayaran) {
                        $this->error("✗ Cannot send payment email: No payment record found");
                        $this->info("  This booking doesn't have payment data yet.");
                        return 1;
                    }
                    
                    if ($pemesanan->status !== 'paid') {
                        $this->warn("⚠ Warning: Booking status is '{$pemesanan->status}', not 'paid'");
                        if (!$this->confirm("Continue anyway?", false)) {
                            return 0;
                        }
                    }
                    
                    $result = $this->emailService->sendPaymentSuccess($pemesanan);
                    break;
                    
                case 'cancellation':
                    // Validate cancellation reason
                    if (empty($reason)) {
                        $this->error("✗ Cancellation reason is required");
                        $this->info("  Use: --reason=\"Your reason here\"");
                        return 1;
                    }
                    
                    if ($pemesanan->status !== 'cancelled') {
                        $this->warn("⚠ Warning: Booking status is '{$pemesanan->status}', not 'cancelled'");
                        if (!$this->confirm("Continue anyway?", false)) {
                            return 0;
                        }
                    }
                    
                    $result = $this->emailService->sendCancellationNotification($pemesanan, $reason);
                    break;
                    
                default:
                    $this->error("✗ Invalid email type. Use: booking, payment, or cancellation");
                    return 1;
            }

            if ($result) {
                $this->newLine();
                $this->info("✓ {$type} email sent successfully!");
                $this->line("  To: {$pemesanan->email}");
                $this->line("  Booking: {$pemesanan->kode_booking}");
                $this->newLine();
                $this->info("📬 Check the inbox (and spam folder if needed)");
            } else {
                $this->error("✗ Failed to send email.");
                $this->info("  Check Laravel logs for error details: storage/logs/laravel.log");
                return 1;
            }

            return 0;

        } catch (\App\Exceptions\EmailRateLimitException $e) {
            $this->error("✗ Rate Limit Exceeded");
            $this->info("  Daily email limit reached. Try again tomorrow or upgrade to Google Workspace.");
            return 1;
            
        } catch (\App\Exceptions\EmailValidationException $e) {
            $this->error("✗ Validation Error: " . $e->getMessage());
            $this->info("  The booking data is missing required fields for this email type.");
            return 1;
            
        } catch (\Exception $e) {
            $this->error("✗ Error: " . $e->getMessage());
            $this->info("  Check Laravel logs for details: storage/logs/laravel.log");
            return 1;
        }
    }
}
