<?php

namespace App\Services;

use App\Models\Jadwal;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\DB;

class PaymentStatusService
{
    public function __construct(
        private BookingEmailNotificationService $bookingEmailNotifications,
        private NotificationService $notificationService,
    ) {}

    public function apply(
        Pembayaran $pembayaran,
        string $transactionStatus,
        ?string $fraudStatus = null,
        ?string $paymentType = null,
        ?string $transactionId = null,
    ): ?Pembayaran {
        $status = $this->mapStatus($transactionStatus, $fraudStatus);
        if (in_array($pembayaran->status, ['success', 'failed', 'expired'], true) && $status === 'pending') {
            return null;
        }

        if ($pembayaran->status === $status && $transactionId === $pembayaran->transaction_id) {
            return null;
        }

        [$updatedPayment, $bookingToNotify] = DB::transaction(function () use (
            $pembayaran,
            $status,
            $paymentType,
            $transactionId,
        ): array {
            $lockedPayment = Pembayaran::query()
                ->with('pemesanan')
                ->lockForUpdate()
                ->findOrFail($pembayaran->id);
            $booking = $lockedPayment->pemesanan()
                ->with(['paketWisata', 'jadwal', 'pembayaran'])
                ->first();
            $wasSuccessful = $lockedPayment->status === 'success';
            $wasReleased = in_array($lockedPayment->status, ['failed', 'expired'], true);

            $lockedPayment->update([
                'status' => $status,
                'payment_type' => $paymentType ?? $lockedPayment->payment_type,
                'transaction_id' => $transactionId ?? $lockedPayment->transaction_id,
            ]);

            if ($status === 'success' && ! $wasSuccessful && $booking) {
                $booking->markAsPaid();
                $this->notificationService->createPaymentNotification($booking);
            }

            if (in_array($status, ['failed', 'expired'], true) && ! $wasReleased && $booking) {
                $jadwal = $booking->jadwal_id
                    ? Jadwal::query()->lockForUpdate()->find($booking->jadwal_id)
                    : null;

                if ($booking->status !== 'paid' && $booking->status !== 'cancelled' && $booking->status !== 'expired') {
                    $booking->update([
                        'status' => $status === 'expired' ? 'expired' : 'cancelled',
                    ]);

                    if ($jadwal) {
                        $jadwal->incrementKuota((int) $booking->jumlah_orang);
                    }
                }
            }

            return [
                $lockedPayment->fresh(),
                $status === 'success' && ! $wasSuccessful ? $booking : null,
            ];
        });

        if ($bookingToNotify) {
            $this->bookingEmailNotifications->paymentSuccess(
                $bookingToNotify->fresh(['paketWisata', 'jadwal', 'pembayaran']),
            );
        }

        return $updatedPayment;
    }

    private function mapStatus(string $transactionStatus, ?string $fraudStatus): string
    {
        return match ($transactionStatus) {
            'capture' => match ($fraudStatus) {
                'accept' => 'success',
                'deny' => 'failed',
                default => 'pending',
            },
            'settlement' => 'success',
            'cancel', 'deny' => 'failed',
            'expire' => 'expired',
            default => 'pending',
        };
    }
}
