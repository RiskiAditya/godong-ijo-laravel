<?php

namespace App\Services;

use App\Models\AdminNotification;
use App\Models\Pemesanan;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Create a new booking notification
     * 
     * @param Pemesanan $booking
     * @return AdminNotification|null
     */
    public function createBookingNotification(Pemesanan $booking): ?AdminNotification
    {
        if (!SystemSetting::enabled('booking_notification')) {
            return null;
        }

        try {
            $paketName = $booking->paketWisata->nama_paket ?? 'Paket tidak diketahui';
            
            return AdminNotification::create([
                'admin_id' => null, // Broadcast to all admins
                'type' => 'booking',
                'title' => 'Booking Baru Masuk',
                'message' => "Booking baru dari {$booking->nama_pemesan} untuk paket {$paketName}",
                'data' => [
                    'booking_code' => $booking->kode_booking,
                    'customer_name' => $booking->nama_pemesan,
                    'package_name' => $paketName,
                    'total_price' => $booking->total_harga,
                    'booking_date' => $booking->tanggal_berkunjung,
                ],
                'booking_id' => $booking->id,
                'is_read' => false,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create booking notification', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Create a payment success notification
     * 
     * @param Pemesanan $booking
     * @return AdminNotification|null
     */
    public function createPaymentNotification(Pemesanan $booking): ?AdminNotification
    {
        if (!SystemSetting::enabled('booking_notification')) {
            return null;
        }

        try {
            $paketName = $booking->paketWisata->nama_paket ?? 'Paket tidak diketahui';
            
            return AdminNotification::create([
                'admin_id' => null, // Broadcast to all admins
                'type' => 'payment',
                'title' => 'Pembayaran Berhasil',
                'message' => "Pembayaran untuk booking {$booking->kode_booking} telah dikonfirmasi",
                'data' => [
                    'booking_code' => $booking->kode_booking,
                    'customer_name' => $booking->nama_pemesan,
                    'package_name' => $paketName,
                    'total_price' => $booking->total_harga,
                    'payment_method' => $booking->pembayaran->metode_pembayaran ?? 'N/A',
                ],
                'booking_id' => $booking->id,
                'is_read' => false,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create payment notification', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Create a cancellation notification
     * 
     * @param Pemesanan $booking
     * @param string $reason
     * @return AdminNotification|null
     */
    public function createCancellationNotification(Pemesanan $booking, string $reason = ''): ?AdminNotification
    {
        if (!SystemSetting::enabled('booking_notification')) {
            return null;
        }

        try {
            $paketName = $booking->paketWisata->nama_paket ?? 'Paket tidak diketahui';
            
            return AdminNotification::create([
                'admin_id' => null, // Broadcast to all admins
                'type' => 'cancellation',
                'title' => 'Booking Dibatalkan',
                'message' => "Booking {$booking->kode_booking} telah dibatalkan" . ($reason ? ": {$reason}" : ''),
                'data' => [
                    'booking_code' => $booking->kode_booking,
                    'customer_name' => $booking->nama_pemesan,
                    'package_name' => $paketName,
                    'cancellation_reason' => $reason,
                ],
                'booking_id' => $booking->id,
                'is_read' => false,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create cancellation notification', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Mark a notification as read
     * 
     * @param int $notificationId
     * @return bool
     */
    public function markAsRead(int $notificationId): bool
    {
        try {
            $notification = AdminNotification::find($notificationId);
            
            if (!$notification) {
                return false;
            }
            
            return $notification->markAsRead();
        } catch (\Exception $e) {
            Log::error('Failed to mark notification as read', [
                'notification_id' => $notificationId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Mark all notifications as read
     * 
     * @param int|null $adminId
     * @return int Number of notifications marked as read
     */
    public function markAllAsRead(?int $adminId = null): int
    {
        try {
            $query = AdminNotification::unread();
            
            if ($adminId !== null) {
                $query->where('admin_id', $adminId);
            } else {
                // For broadcast notifications (admin_id is null)
                $query->whereNull('admin_id');
            }
            
            return $query->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to mark all notifications as read', [
                'admin_id' => $adminId,
                'error' => $e->getMessage(),
            ]);
            return 0;
        }
    }

    /**
     * Get unread notification count
     * 
     * @param int|null $adminId
     * @return int
     */
    public function getUnreadCount(?int $adminId = null): int
    {
        try {
            $query = AdminNotification::unread();
            
            if ($adminId !== null) {
                $query->where('admin_id', $adminId);
            } else {
                // For broadcast notifications (admin_id is null)
                $query->whereNull('admin_id');
            }
            
            return $query->count();
        } catch (\Exception $e) {
            Log::error('Failed to get unread notification count', [
                'admin_id' => $adminId,
                'error' => $e->getMessage(),
            ]);
            return 0;
        }
    }

    /**
     * Get recent notifications
     * 
     * @param int $limit
     * @param int|null $adminId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentNotifications(int $limit = 10, ?int $adminId = null)
    {
        try {
            $query = AdminNotification::with('booking.paketWisata');
            
            if ($adminId !== null) {
                $query->where('admin_id', $adminId);
            } else {
                // For broadcast notifications (admin_id is null)
                $query->whereNull('admin_id');
            }
            
            return $query->recent($limit)->get();
        } catch (\Exception $e) {
            Log::error('Failed to get recent notifications', [
                'admin_id' => $adminId,
                'limit' => $limit,
                'error' => $e->getMessage(),
            ]);
            return collect([]);
        }
    }

    /**
     * Delete old read notifications (cleanup)
     * 
     * @param int $daysOld
     * @return int Number of deleted notifications
     */
    public function deleteOldNotifications(int $daysOld = 30): int
    {
        try {
            return AdminNotification::where('is_read', true)
                ->where('read_at', '<', now()->subDays($daysOld))
                ->delete();
        } catch (\Exception $e) {
            Log::error('Failed to delete old notifications', [
                'days_old' => $daysOld,
                'error' => $e->getMessage(),
            ]);
            return 0;
        }
    }
}
