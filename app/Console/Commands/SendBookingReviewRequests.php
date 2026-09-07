<?php

namespace App\Console\Commands;

use App\Models\Pemesanan;
use App\Services\EmailService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendBookingReviewRequests extends Command
{
    protected $signature = 'email:send-review-requests {--date= : Visit date to process (Y-m-d), defaults to yesterday} {--dry-run : Preview without sending}';

    protected $description = 'Send one Google Review request after a completed visit';

    public function __construct(private EmailService $emailService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        if (!config('app.google_review_url')) {
            $this->warn('GOOGLE_REVIEW_URL is not configured; no review emails will be sent.');
            return self::SUCCESS;
        }

        $visitDate = Carbon::parse($this->option('date') ?: now()->subDay()->toDateString());
        $bookings = Pemesanan::with(['paketWisata', 'jadwal'])
            ->whereNull('review_sent_at')
            ->where('status', 'paid')
            ->whereNotNull('email')
            ->where(function ($query) use ($visitDate) {
                $query->whereDate('tanggal_kunjungan', $visitDate->toDateString())
                    ->orWhereHas('jadwal', fn ($jadwal) => $jadwal->whereDate('tanggal', $visitDate->toDateString()));
            })
            ->get();

        if ($bookings->isEmpty()) {
            $this->info("No eligible review requests for {$visitDate->toDateString()}.");
            return self::SUCCESS;
        }

        foreach ($bookings as $booking) {
            if ($this->option('dry-run')) {
                $this->line("[DRY RUN] {$booking->kode_booking} -> {$booking->email}");
                continue;
            }

            try {
                $this->emailService->sendBookingReviewRequest($booking);
                $this->info("Sent review request: {$booking->kode_booking}");
            } catch (\Throwable $exception) {
                $this->error("Failed {$booking->kode_booking}: {$exception->getMessage()}");
            }
        }

        return self::SUCCESS;
    }
}
