<?php

namespace App\Console\Commands;

use App\Models\Pemesanan;
use App\Services\EmailService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendBookingReminders extends Command
{
    protected $signature = 'email:send-reminders {--date= : Visit date to process (Y-m-d), defaults to tomorrow} {--dry-run : Preview without sending}';

    protected $description = 'Send one H-1 visit reminder email for eligible bookings';

    public function __construct(private EmailService $emailService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $visitDate = Carbon::parse($this->option('date') ?: now()->addDay()->toDateString());
        $bookings = Pemesanan::with(['paketWisata', 'jadwal'])
            ->whereNull('reminder_sent_at')
            ->whereNotIn('status', ['cancelled', 'expired'])
            ->whereNotNull('email')
            ->where(function ($query) use ($visitDate) {
                $query->whereDate('tanggal_kunjungan', $visitDate->toDateString())
                    ->orWhereHas('jadwal', fn ($jadwal) => $jadwal->whereDate('tanggal', $visitDate->toDateString()));
            })
            ->get();

        if ($bookings->isEmpty()) {
            $this->info("No eligible bookings for {$visitDate->toDateString()}.");
            return self::SUCCESS;
        }

        foreach ($bookings as $booking) {
            if ($this->option('dry-run')) {
                $this->line("[DRY RUN] {$booking->kode_booking} -> {$booking->email}");
                continue;
            }

            try {
                $this->emailService->sendBookingReminder($booking, $visitDate->format('d M Y'));
                $this->info("Sent reminder: {$booking->kode_booking}");
            } catch (\Throwable $exception) {
                $this->error("Failed {$booking->kode_booking}: {$exception->getMessage()}");
            }
        }

        return self::SUCCESS;
    }
}
