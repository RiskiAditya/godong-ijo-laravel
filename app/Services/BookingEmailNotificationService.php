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

    public function confirmation(Pemesanan $booking): void
    {
        if (!SystemSetting::enabled('email_notification')) {
            return;
        }

        try {
            $this->emailService->sendBookingConfirmation($booking);
        } catch (\Throwable $exception) {
            Log::error('Failed to send booking confirmation email', [
                'booking_code' => $booking->kode_booking,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    public function paymentSuccess(Pemesanan $booking): void
    {
        if (!SystemSetting::enabled('email_notification')) {
            return;
        }

        try {
            $this->emailService->sendPaymentSuccess($booking);
        } catch (\Throwable $exception) {
            Log::error('Failed to send payment success email', [
                'booking_code' => $booking->kode_booking,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    public function cancellation(Pemesanan $booking, string $reason): void
    {
        if (!SystemSetting::enabled('email_notification')) {
            return;
        }

        try {
            $this->emailService->sendCancellationNotification($booking, $reason);
        } catch (\Throwable $exception) {
            Log::error('Failed to send booking cancellation email', [
                'booking_code' => $booking->kode_booking,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
