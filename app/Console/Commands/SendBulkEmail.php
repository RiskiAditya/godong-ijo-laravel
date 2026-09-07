<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pemesanan;
use App\Services\EmailService;

class SendBulkEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:bulk 
                            {type : Email type (booking, payment)} 
                            {--status= : Filter by booking status (pending, paid, cancelled)}
                            {--limit=10 : Maximum number of emails to send}
                            {--dry-run : Preview without sending}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send bulk email notifications to multiple bookings';

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
        $status = $this->option('status');
        $limit = (int) $this->option('limit');
        $dryRun = $this->option('dry-run');

        // Validate email type
        if (!in_array($type, ['booking', 'payment'])) {
            $this->error("✗ Invalid email type. Use: booking or payment");
            $this->info("  (Bulk cancellation emails not supported - use email:send-booking instead)");
            return 1;
        }

        // Build query
        $this->info("Searching for bookings...\n");
        
        $query = Pemesanan::with(['paketWisata', 'jadwal.paket', 'pembayaran'])
            ->whereNotNull('email')
            ->where('email', '!=', '');

        // Filter by status
        if ($status) {
            $query->where('status', $status);
        } else {
            // Default filters based on type
            if ($type === 'booking') {
                $query->where('status', 'pending');
            } elseif ($type === 'payment') {
                $query->where('status', 'paid');
            }
        }

        // Apply limit
        $bookings = $query->limit($limit)->get();

        if ($bookings->isEmpty()) {
            $this->info("No bookings found with the specified criteria.");
            return 0;
        }

        // Display summary
        $this->info("📋 Found {$bookings->count()} booking(s):");
        $this->newLine();

        $table = [];
        foreach ($bookings as $booking) {
            $table[] = [
                $booking->kode_booking,
                $booking->nama_lengkap,
                $booking->email,
                $booking->status,
                'Rp ' . number_format($booking->total_harga, 0, ',', '.'),
            ];
        }

        $this->table(
            ['Booking Code', 'Customer', 'Email', 'Status', 'Amount'],
            $table
        );

        // Check rate limit
        $rateStatus = $this->emailService->getRateLimitStatus();
        $this->info("\n📊 Rate Limit Status:");
        $this->line("  Used: {$rateStatus->currentCount}/{$rateStatus->dailyLimit} emails today");
        $this->line("  Remaining: {$rateStatus->remainingQuota} emails");

        if ($bookings->count() > $rateStatus->remainingQuota) {
            $this->warn("\n⚠ Warning: Not enough quota to send all emails!");
            $this->info("  You can send {$rateStatus->remainingQuota} emails now, rest will be skipped.");
        }

        // Dry run mode
        if ($dryRun) {
            $this->info("\n🔍 DRY RUN MODE - No emails will be sent");
            $this->info("  Remove --dry-run flag to actually send emails");
            return 0;
        }

        // Confirm before sending
        if (!$this->confirm("\n📧 Send {$type} emails to {$bookings->count()} recipient(s)?", true)) {
            $this->info("Bulk email sending cancelled.");
            return 0;
        }

        // Send emails
        $this->newLine();
        $this->info("Sending emails...");
        $this->newLine();

        $successCount = 0;
        $failCount = 0;
        $skippedCount = 0;

        $progressBar = $this->output->createProgressBar($bookings->count());
        $progressBar->start();

        foreach ($bookings as $booking) {
            try {
                // Check if we still have quota
                if (!$this->emailService->getRateLimitStatus()->canSend) {
                    $skippedCount++;
                    $progressBar->advance();
                    continue;
                }

                // Send email based on type
                $result = false;
                
                switch ($type) {
                    case 'booking':
                        if ($booking->paketWisata || $booking->jadwal) {
                            $result = $this->emailService->sendBookingConfirmation($booking);
                        } else {
                            $skippedCount++;
                        }
                        break;
                        
                    case 'payment':
                        if ($booking->pembayaran) {
                            $result = $this->emailService->sendPaymentSuccess($booking);
                        } else {
                            $skippedCount++;
                        }
                        break;
                }

                if ($result) {
                    $successCount++;
                } else {
                    $failCount++;
                }

            } catch (\Exception $e) {
                $failCount++;
                // Continue with next booking
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Display results
        $this->info("✓ Bulk email sending completed!\n");
        $this->line("  Success  : {$successCount} email(s) sent");
        
        if ($failCount > 0) {
            $this->line("  Failed   : {$failCount} email(s) failed");
        }
        
        if ($skippedCount > 0) {
            $this->line("  Skipped  : {$skippedCount} email(s) skipped (missing data or rate limit)");
        }

        // Check final rate limit status
        $finalStatus = $this->emailService->getRateLimitStatus();
        $this->newLine();
        $this->info("📊 Final Rate Limit: {$finalStatus->currentCount}/{$finalStatus->dailyLimit} used");

        if ($failCount > 0) {
            $this->newLine();
            $this->info("Check logs for error details: storage/logs/laravel.log");
        }

        return 0;
    }
}
