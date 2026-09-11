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
        if (!SystemSetting::enabled('email_notification')) {
            Log::warning('Booking confirmation email skipped because email notifications are disabled', [
                'booking_code' => $booking->kode_booking,
            ]);

            return false;
        }

        try {
            return $this->emailService->sendBookingConfirmation($booking);
        } catch (\Throwable $exception) {
            Log::error('Failed to send booking confirmation email', [
                'booking_code' => $booking->kode_booking,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    public function paymentSuccess(Pemesanan $booking): bool
    {
        if (!SystemSetting::enabled('email_notification')) {
            Log::warning('Payment success email skipped because email notifications are disabled', [
                'booking_code' => $booking->kode_booking,
            ]);

            return false;
        }

        try {
            return $this->emailService->sendPaymentSuccess($booking);
        } catch (\Throwable $exception) {
            Log::error('Failed to send payment success email', [
                'booking_code' => $booking->kode_booking,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
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
}
