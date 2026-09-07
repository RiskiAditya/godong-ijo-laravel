<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;
    
    protected $table = 'jadwal';
    
    protected $fillable = [
        'paket_id',
        'tanggal',
        'kuota_tersedia',
    ];
    
    protected $casts = [
        'tanggal' => 'date',
    ];
    
    /**
     * Get paket wisata
     */
    public function paket()
    {
        return $this->belongsTo(PaketWisata::class, 'paket_id');
    }
    
    /**
     * Get bookings for this jadwal
     */
    public function bookings()
    {
        return $this->hasMany(Pemesanan::class, 'jadwal_id');
    }
    
    /**
     * Decrement kuota
     */
    public function decrementKuota(int $jumlah): void
    {
        $this->decrement('kuota_tersedia', $jumlah);
    }
    
    /**
     * Increment kuota (restore quota)
     */
    public function incrementKuota(int $jumlah): void
    {
        $this->increment('kuota_tersedia', $jumlah);
    }
    
    /**
     * Check if kuota is available
     */
    public function isAvailable(int $jumlah): bool
    {
        return $this->kuota_tersedia >= $jumlah;
    }
}
