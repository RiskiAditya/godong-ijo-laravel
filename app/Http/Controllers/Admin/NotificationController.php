<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get recent notifications for admin
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $limit = $request->get('limit', 10);
            
            // Get notifications (broadcast to all admins, admin_id = null)
            $notifications = $this->notificationService->getRecentNotifications($limit);
            
            // Format notifications for JSON response
            $formattedNotifications = $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'icon' => $notification->icon,
                    'color' => $notification->color,
                    'time_ago' => $notification->time_ago,
                    'is_read' => $notification->is_read,
                    'booking_id' => $notification->booking_id,
                    'booking_code' => $notification->data['booking_code'] ?? null,
                    'created_at' => $notification->created_at->toISOString(),
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $formattedNotifications,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch notifications', [
                'error' => $e->getMessage(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil notifikasi',
            ], 500);
        }
    }

    /**
     * Get unread notification count
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function unreadCount()
    {
        try {
            $count = $this->notificationService->getUnreadCount();
            
            return response()->json([
                'success' => true,
                'count' => $count,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get unread count', [
                'error' => $e->getMessage(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil jumlah notifikasi',
            ], 500);
        }
    }

    /**
     * Mark a notification as read
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead($id)
    {
        try {
            $success = $this->notificationService->markAsRead($id);
            
            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notifikasi tidak ditemukan',
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Notifikasi ditandai sudah dibaca',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to mark notification as read', [
                'notification_id' => $id,
                'error' => $e->getMessage(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menandai notifikasi',
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAllAsRead()
    {
        try {
            $count = $this->notificationService->markAllAsRead();
            
            return response()->json([
                'success' => true,
                'message' => "{$count} notifikasi ditandai sudah dibaca",
                'count' => $count,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to mark all notifications as read', [
                'error' => $e->getMessage(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menandai semua notifikasi',
            ], 500);
        }
    }
}

