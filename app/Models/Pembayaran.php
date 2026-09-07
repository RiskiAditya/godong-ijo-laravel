<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;
    
    protected $table = 'pembayaran';
    
    protected $fillable = [
        'pemesanan_id',
        'order_id',
        'transaction_id',
        'payment_type',
        'gross_amount',
        'status',
        'snap_token',
        'midtrans_response',
    ];
    
    protected $casts = [
        'gross_amount' => 'decimal:2',
        'midtrans_response' => 'array',
    ];
    
    /**
     * Get pemesanan
     */
    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class, 'pemesanan_id');
    }
    
    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'success' => 'success',
            'pending' => 'warning',
            'failed' => 'danger',
            'expired' => 'secondary',
            default => 'secondary',
        };
    }
    
    /**
     * Get status label in Indonesian
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'success' => 'Berhasil',
            'pending' => 'Pending',
            'failed' => 'Gagal',
            'expired' => 'Kadaluarsa',
            default => $this->status,
        };
    }
}
