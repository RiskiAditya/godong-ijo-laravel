<?php

namespace App\Console\Commands;

use App\Models\Pemesanan;
use App\Models\SystemSetting;
use App\Services\BookingEmailNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RetryBookingEmails extends Command
{
    protected $signature = 'email:retry-booking-notifications
                            {--max-attempts=5 : Maximum attempts per email type}
                            {--retry-after=15 : Minutes to wait between attempts}
                            {--dry-run : Preview eligible emails without sending}';

    protected $description = 'Retry unsent customer booking and payment emails';

    public function __construct(private BookingEmailNotificationService $bookingEmailNotifications)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        if (! SystemSetting::enabled('email_notification')) {
            $this->warn('Email notifications are disabled; no customer emails will be retried.');

            return self::SUCCESS;
        }

        $maxAttempts = max(1, (int) $this->option('max-attempts'));
        $retryBefore = now()->subMinutes(max(1, (int) $this->option('retry-after')));
        $sentCount = 0;
        $failedCount = 0;

        Pemesanan::query()
            ->with(['paketWisata', 'jadwal', 'pembayaran'])
            ->where(function ($query) use ($maxAttempts, $retryBefore) {
                $query->where(function ($confirmationQuery) use ($maxAttempts, $retryBefore) {
                    $confirmationQuery
                        ->where('booking_confirmation_email_tracking_enabled', true)
                        ->whereNull('booking_confirmation_email_sent_at')
                        ->where('booking_confirmation_email_attempts', '<', $maxAttempts)
                        ->whereIn('status', ['pending', 'paid'])
                        ->where(function ($attemptQuery) use ($retryBefore) {
                            $attemptQuery->whereNull('booking_confirmation_email_last_attempt_at')
                                ->orWhere('booking_confirmation_email_last_attempt_at', '<=', $retryBefore);
                        });
                })->orWhere(function ($paymentQuery) use ($maxAttempts, $retryBefore) {
                    $paymentQuery
                        ->where('payment_success_email_tracking_enabled', true)
                        ->whereNull('payment_success_email_sent_at')
                        ->where('payment_success_email_attempts', '<', $maxAttempts)
                        ->where('status', 'paid')
                        ->whereHas('pembayaran', fn ($query) => $query->where('status', 'success'))
                        ->where(function ($attemptQuery) use ($retryBefore) {
                            $attemptQuery->whereNull('payment_success_email_last_attempt_at')
                                ->orWhere('payment_success_email_last_attempt_at', '<=', $retryBefore);
                        });
                });
            })
            ->chunkById(100, function ($bookings) use (&$sentCount, &$failedCount, $maxAttempts, $retryBefore) {
                foreach ($bookings as $booking) {
                    foreach ([
                        'booking_confirmation_email_sent_at' => fn (): bool => $this->bookingEmailNotifications->confirmation($booking),
                        'payment_success_email_sent_at' => fn (): bool => $this->bookingEmailNotifications->paymentSuccess($booking),
                    ] as $sentAtColumn => $send) {
                        if (! $this->emailIsDue($booking, $sentAtColumn, $maxAttempts, $retryBefore)) {
                            continue;
                        }

                        if ($this->option('dry-run')) {
                            $this->line("[DRY RUN] {$sentAtColumn}: {$booking->kode_booking} -> {$booking->email}");
                            continue;
                        }

                        try {
                            if ($send()) {
                                $sentCount++;
                            } else {
                                $failedCount++;
                            }
                        } catch (\Throwable $exception) {
                            $failedCount++;
                            Log::error('Scheduled booking email retry failed', [
                                'type' => $sentAtColumn,
                                'booking_code' => $booking->kode_booking,
                                'error' => $exception->getMessage(),
                            ]);
                            $this->error("Failed {$sentAtColumn} for {$booking->kode_booking}: {$exception->getMessage()}");
                        }
                    }
                }
            });

        $this->info("Sent {$sentCount} customer email(s); {$failedCount} attempt(s) failed.");

        return $failedCount > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function emailIsDue(
        Pemesanan $booking,
        string $sentAtColumn,
        int $maxAttempts,
        \Illuminate\Support\Carbon $retryBefore,
    ): bool {
        if ($booking->getAttribute($sentAtColumn)) {
            return false;
        }

        if ($sentAtColumn === 'booking_confirmation_email_sent_at') {
            $trackingColumn = 'booking_confirmation_email_tracking_enabled';
            $attemptsColumn = 'booking_confirmation_email_attempts';
            $lastAttemptColumn = 'booking_confirmation_email_last_attempt_at';
            if (! in_array($booking->status, ['pending', 'paid'], true)) {
                return false;
            }
        } else {
            $trackingColumn = 'payment_success_email_tracking_enabled';
            $attemptsColumn = 'payment_success_email_attempts';
            $lastAttemptColumn = 'payment_success_email_last_attempt_at';
            if ($booking->status !== 'paid' || $booking->pembayaran?->status !== 'success') {
                return false;
            }
        }

        $lastAttempt = $booking->getAttribute($lastAttemptColumn);

        return $booking->getAttribute($trackingColumn)
            && $booking->getAttribute($attemptsColumn) < $maxAttempts
            && ($lastAttempt === null || $lastAttempt <= $retryBefore);
    }
}
