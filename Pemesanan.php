<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemesanan extends Model
{
    use HasFactory;
    
    protected $table = 'pemesanan';
    
    protected $fillable = [
        'kode_booking',
        'user_id',
        'jadwal_id',
        'nama_lengkap',
        'email',
        'no_hp',
        'package_specific_data',
        'tanggal_kunjungan',
        'jam_kunjungan',
        'catatan',
        'jumlah_orang',
        'total_harga',
        'status',
    ];
    
    protected $casts = [
        'total_harga' => 'decimal:2',
        'package_specific_data' => 'array',
        'tanggal_kunjungan' => 'date',
    ];
    
    /**
     * Get user (nullable for guest bookings)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get jadwal
     */
    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }
    
    /**
     * Get pembayaran
     */
    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'pemesanan_id');
    }
    
    /**
     * Get e-tiket (nullable: baru ada setelah diterbitkan)
     */
    public function eTiket()
    {
        return $this->hasOne(ETiket::class, 'pemesanan_id');
    }
    
    /**
     * Generate kode booking
     */
    public function generateKodeBooking(): string
    {
        return 'BK' . date('Ymd') . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }
    
    /**
     * Mark as paid
     */
    public function markAsPaid(): void
    {
        $this->update(['status' => 'paid']);
    }
    
    /**
     * Mark as cancelled
     */
    public function markAsCancelled(): void
    {
        $this->update(['status' => 'cancelled']);
    }
    
    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'paid' => 'success',
            'pending' => 'warning',
            'cancelled' => 'danger',
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
            'paid' => 'Lunas',
            'pending' => 'Pending',
            'cancelled' => 'Dibatalkan',
            'expired' => 'Kadaluarsa',
            default => $this->status,
        };
    }
}
