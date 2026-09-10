<?php

namespace App\Console\Commands;

use App\Models\Pemesanan;
use App\Services\PaymentStatusService;
use Illuminate\Console\Command;

class ExpirePendingBookings extends Command
{
    protected $signature = 'bookings:expire-pending {--hours=24 : Age in hours before a pending booking expires}';

    protected $description = 'Expire old pending bookings and release their reserved quota';

    public function __construct(private PaymentStatusService $paymentStatusService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $hours = max(1, (int) $this->option('hours'));
        $cutoff = now()->subHours($hours);
        $processed = 0;

        Pemesanan::query()
            ->with('pembayaran')
            ->where('status', 'pending')
            ->where('created_at', '<=', $cutoff)
            ->whereHas('pembayaran', fn ($query) => $query->whereIn('status', ['pending']))
            ->chunkById(100, function ($bookings) use (&$processed) {
                foreach ($bookings as $booking) {
                    if (! $booking->pembayaran) {
                        continue;
                    }

                    $this->paymentStatusService->apply($booking->pembayaran, 'expire');
                    $processed++;
                }
            });

        $this->info("Expired {$processed} pending booking(s).");

        return self::SUCCESS;
    }
}
