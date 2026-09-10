<?php

namespace App\Services;

use App\Models\Pemesanan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminBookingService
{
    public function __construct(private EmailService $emailService)
    {
    }

    /**
     * Build the booking query shared by the admin list and export endpoints.
     */
    public function query(Request $request, array $relations = []): Builder
    {
        $query = Pemesanan::with($relations);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('tanggal_dari')) {
            $query->where(function (Builder $query) use ($request) {
                $query->whereDate('tanggal_kunjungan', '>=', $request->input('tanggal_dari'))
                    ->orWhereHas('jadwal', function (Builder $jadwalQuery) use ($request) {
                        $jadwalQuery->whereDate('tanggal', '>=', $request->input('tanggal_dari'));
                    });
            });
        }

        if ($request->filled('tanggal_sampai')) {
            $query->where(function (Builder $query) use ($request) {
                $query->whereDate('tanggal_kunjungan', '<=', $request->input('tanggal_sampai'))
                    ->orWhereHas('jadwal', function (Builder $jadwalQuery) use ($request) {
                        $jadwalQuery->whereDate('tanggal', '<=', $request->input('tanggal_sampai'));
                    });
            });
        }

        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function (Builder $query) use ($search) {
                $query->where('kode_booking', 'like', '%' . $search . '%')
                    ->orWhere('nama_lengkap', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        return $query->latest();
    }

    /**
     * Update the booking status and apply quota / payment side effects.
     *
     * @return array{old_status: string, new_status: string, email_notice: string|null}
     */
    public function updateStatus(Pemesanan $booking, string $newStatus): array
    {
        $oldStatus = $booking->status;

        $isKiloan = ($booking->package_specific_data['jenis_pemancingan'] ?? null) === 'kiloan';
        if ($newStatus === 'paid' && $isKiloan && $booking->total_harga === null) {
            throw new \InvalidArgumentException('Booking kiloan belum memiliki harga final. Silakan finalisasi hasil timbangan terlebih dahulu.');
        }

        DB::transaction(function () use ($booking, $newStatus, $oldStatus) {
            if ($oldStatus === $newStatus) {
                return;
            }

            $booking->update(['status' => $newStatus]);

            if ($newStatus === 'paid' && $booking->pembayaran) {
                $booking->pembayaran->update([
                    'status' => 'success',
                    'paid_at' => now(),
                ]);
            }

            if (
                in_array($newStatus, ['cancelled', 'expired'], true)
                && ! in_array($oldStatus, ['cancelled', 'expired'], true)
                && $booking->jadwal_id
                && (! $booking->pembayaran || ! in_array($booking->pembayaran->status, ['failed', 'expired'], true))
            ) {
                $booking->jadwal->incrementKuota($booking->jumlah_orang);

                if ($booking->pembayaran) {
                    $booking->pembayaran->update(['status' => 'failed']);
                }
            }
        });

        $booking->refresh();

        $emailNotice = null;
        if ($oldStatus !== $booking->status && $booking->email) {
            try {
                $this->emailService->sendBookingStatusUpdate($booking, $oldStatus);
                $emailNotice = ' Email status telah dikirim ke pelanggan.';
            } catch (\Throwable $exception) {
                Log::error('Failed to send booking status email', [
                    'booking_code' => $booking->kode_booking,
                    'error' => $exception->getMessage(),
                ]);
                $emailNotice = ' Status berubah, tetapi email pelanggan gagal dikirim.';
            }
        }

        return [
            'old_status' => $oldStatus,
            'new_status' => $booking->status,
            'email_notice' => $emailNotice,
        ];
    }

    /**
     * Finalize a kiloan booking by storing the weighed amount and final total price.
     */
    public function finalizeKiloan(Pemesanan $booking, array $validated): Pemesanan
    {
        $isKiloan = ($booking->package_specific_data['jenis_pemancingan'] ?? null) === 'kiloan';
        if (! $isKiloan) {
            throw new \InvalidArgumentException('Hanya booking kiloan yang dapat diproses melalui finalisasi timbang.');
        }

        return DB::transaction(function () use ($booking, $validated) {
            $packageSpecificData = $booking->package_specific_data ?? [];

            if (isset($validated['berat_kg'])) {
                $packageSpecificData['berat_kg'] = (float) $validated['berat_kg'];
            }

            if (isset($validated['hasil_timbangan'])) {
                $packageSpecificData['hasil_timbangan'] = $validated['hasil_timbangan'];
            }

            $finalPrice = (float) $validated['total_harga'];

            $booking->update([
                'total_harga' => $finalPrice,
                'package_specific_data' => $packageSpecificData,
                'status' => 'pending',
            ]);

            if ($booking->pembayaran) {
                $booking->pembayaran->update([
                    'gross_amount' => $finalPrice,
                    'status' => 'pending',
                ]);
            }

            return $booking->fresh();
        });
    }

    /**
     * Send a manual admin email for a specific booking.
     */
    public function sendEmail(Pemesanan $booking, string $type, ?string $reason = null): bool
    {
        if (empty($booking->email)) {
            throw new \InvalidArgumentException('Booking ini tidak memiliki alamat email.');
        }

        return match ($type) {
            'payment' => $this->emailService->sendPaymentSuccess($booking),
            'cancellation' => $this->emailService->sendCancellationNotification(
                $booking,
                $reason ?? 'Booking dibatalkan oleh administrator.'
            ),
            default => $this->emailService->sendBookingConfirmation($booking),
        };
    }
}
