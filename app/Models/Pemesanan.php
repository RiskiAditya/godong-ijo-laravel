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
        'paket_wisata_id', // Post-migration guarantee: always non-null after migration execution
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
        'reminder_sent_at',
        'review_sent_at',
    ];
    
    protected $casts = [
        'total_harga' => 'decimal:2',
        'package_specific_data' => 'array',
        'tanggal_kunjungan' => 'date',
        'reminder_sent_at' => 'datetime',
        'review_sent_at' => 'datetime',
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
     * Get paket wisata directly (preferred after migration)
     * 
     * Post-migration guarantee: paket_wisata_id is always non-null after migration execution.
     * This direct relationship is more efficient than accessing through jadwal->paket chain.
     * 
     * Migration: add_paket_wisata_id_and_backfill_jadwal_id
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function paketWisata()
    {
        return $this->belongsTo(PaketWisata::class, 'paket_wisata_id');
    }
    
    /**
     * Get pembayaran
     */
    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'pemesanan_id');
    }
    
    /**
     * Get e-tiket (one-to-one relationship)
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

    public function getPackageDisplayNameAttribute(): string
    {
        $packageName = $this->paketWisata?->nama_paket ?? 'Paket Wisata';
        $specificData = $this->package_specific_data ?? [];

        if (! empty($specificData['private_room_option'])) {
            $privateOption = \App\Support\PrivateRoomPackageCatalog::option($specificData['private_room_option']);

            return $privateOption['label'] ?? $packageName;
        }

        if (! empty($specificData['jenis_pemancingan'])) {
            return 'Fishing Lake - '.ucwords(str_replace('_', ' ', $specificData['jenis_pemancingan']));
        }

        return $packageName;
    }

    public function getPackageTypeAttribute(): string
    {
        $packageType = $this->paketWisata?->jenis_paket;

        return match ($packageType) {
            'Private Room' => 'private-room',
            'Fishing Lake' => 'fishing-lake',
            default => 'the-waterfall-resto',
        };
    }
    
    /**
     * Mark as cancelled
     */
    public function markAsCancelled(): void
    {
        $this->update(['status' => 'cancelled']);
    }
    
    /**
     * Validate that jadwal_id is not null
     * 
     * Throws BookingException if jadwal_id is null, providing recovery hint
     * for running the backfill migration.
     * 
     * Requirements: 2.2, 5.1, 5.2, 5.5
     * 
     * @return void
     * @throws \App\Exceptions\BookingException When jadwal_id is null
     */
    public function validateJadwalId(): void
    {
        if (is_null($this->jadwal_id)) {
            throw new \App\Exceptions\BookingException(
                "Booking {$this->kode_booking} has null jadwal_id",
                [
                    'kode_booking' => $this->kode_booking,
                    'pemesanan_id' => $this->id,
                    'recovery_hint' => 'Run migration to backfill jadwal_id from paket_wisata_id and tanggal_kunjungan',
                ]
            );
        }
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
