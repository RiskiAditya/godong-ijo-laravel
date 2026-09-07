<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminNotification extends Model
{
    use HasFactory;

    protected $table = 'admin_notifications';

    protected $fillable = [
        'admin_id',
        'type',
        'title',
        'message',
        'data',
        'booking_id',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship with Admin
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    /**
     * Relationship with Booking
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Pemesanan::class, 'booking_id', 'id');
    }

    /**
     * Scope: Get only unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope: Get recent notifications (ordered by created_at desc)
     */
    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    /**
     * Scope: Get notifications by type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(): bool
    {
        if ($this->is_read) {
            return true; // Already read
        }

        return $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Get icon based on notification type
     */
    public function getIconAttribute(): string
    {
        return match($this->type) {
            'booking' => '<path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"/>',
            'payment' => '<path d="M12 2v20m5-17v14a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2z"/>',
            'cancellation' => '<circle cx="12" cy="12" r="10"/><path d="m15 9-6 6m0-6 6 6"/>',
            default => '<circle cx="12" cy="12" r="10"/><path d="M12 16v-4m0-4h.01"/>',
        };
    }

    /**
     * Get color based on notification type
     */
    public function getColorAttribute(): string
    {
        return match($this->type) {
            'booking' => '#3B82F6', // blue
            'payment' => '#10B981', // green
            'cancellation' => '#EF4444', // red
            default => '#6B7280', // gray
        };
    }

    /**
     * Get time ago (human readable)
     */
    public function getTimeAgoAttribute(): string
    {
        $diff = $this->created_at->diffInMinutes(now());
        
        if ($diff < 1) {
            return 'Baru saja';
        } elseif ($diff < 60) {
            return $diff . ' menit lalu';
        } elseif ($diff < 1440) {
            $hours = floor($diff / 60);
            return $hours . ' jam lalu';
        } elseif ($diff < 10080) {
            $days = floor($diff / 1440);
            return $days . ' hari lalu';
        } else {
            return $this->created_at->format('d M Y');
        }
    }
}
