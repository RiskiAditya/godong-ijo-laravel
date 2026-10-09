<?php

namespace App\Services;

use App\Models\Pemesanan;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Log;

class BookingEmailNotificationService
{
    public function __construct(private EmailService $emailService)
    {
    }

    public function confirmation(Pemesanan $booking): bool
    {
        return $this->sendTracked(
            $booking,
            'booking_confirmation',
            'booking_confirmation_email_sent_at',
            'booking_confirmation_email_attempts',
            'booking_confirmation_email_last_attempt_at',
            'booking_confirmation_email_tracking_enabled',
            fn (): bool => $this->emailService->sendBookingConfirmation($booking),
        );
    }

    public function paymentSuccess(Pemesanan $booking): bool
    {
        return $this->sendTracked(
            $booking,
            'payment_success',
            'payment_success_email_sent_at',
            'payment_success_email_attempts',
            'payment_success_email_last_attempt_at',
            'payment_success_email_tracking_enabled',
            fn (): bool => $this->emailService->sendPaymentSuccess($booking),
        );
    }

    public function cancellation(Pemesanan $booking, string $reason): bool
    {
        if (!SystemSetting::enabled('email_notification')) {
            Log::warning('Cancellation email skipped because email notifications are disabled', [
                'booking_code' => $booking->kode_booking,
            ]);

            return false;
        }

        try {
            return $this->emailService->sendCancellationNotification($booking, $reason);
        } catch (\Throwable $exception) {
            Log::error('Failed to send booking cancellation email', [
                'booking_code' => $booking->kode_booking,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    private function sendTracked(
        Pemesanan $booking,
        string $type,
        string $sentAtColumn,
        string $attemptsColumn,
        string $lastAttemptAtColumn,
        string $trackingColumn,
        callable $send,
    ): bool {
        if ($booking->getAttribute($sentAtColumn)) {
            return true;
        }

        if (! SystemSetting::enabled('email_notification')) {
            Log::warning(ucfirst(str_replace('_', ' ', $type)).' email skipped because email notifications are disabled', [
                'booking_code' => $booking->kode_booking,
            ]);

            return false;
        }

        $booking->newQuery()
            ->whereKey($booking->getKey())
            ->increment($attemptsColumn, 1, [
                $trackingColumn => true,
                $lastAttemptAtColumn => now(),
            ]);

        try {
            if (! $send()) {
                return false;
            }

            $booking->forceFill([$sentAtColumn => now()])->save();

            return true;
        } catch (\Throwable $exception) {
            Log::error('Failed to send tracked booking email', [
                'type' => $type,
                'booking_code' => $booking->kode_booking,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }
}
