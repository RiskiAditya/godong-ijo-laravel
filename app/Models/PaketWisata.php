<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaketWisata extends Model
{
    use HasFactory;
    
    protected $table = 'paket_wisata';
    
    protected $fillable = [
        'nama_paket',
        'slug',
        'jenis_paket',
        'deskripsi',
        'foto',
        'harga',
        'kuota',
        'is_active',
        'fasilitas',
        'diskon_persen',
        'booking_config',
    ];
    
    protected $casts = [
        'harga' => 'decimal:2',
        'is_active' => 'boolean',
        'fasilitas' => 'array',
        'diskon_persen' => 'integer',
        'booking_config' => 'array',
    ];
    
    /**
     * Get the final price after discount
     * 
     * @return float
     */
    public function getFinalPriceAttribute(): float
    {
        $diskon = $this->diskon_persen ?? 0;
        return $this->harga * (100 - $diskon) / 100;
    }
    
    /**
     * Check if package has discount
     * 
     * @return bool
     */
    public function getHasDiscountAttribute(): bool
    {
        return isset($this->diskon_persen) && $this->diskon_persen > 0;
    }
    
    /**
     * Ensure diskon_persen is never null
     * 
     * @param mixed $value
     * @return int
     */
    public function getDiskonPersenAttribute($value): int
    {
        return $value ?? 0;
    }
    
    /**
     * Get jadwal for this paket
     */
    public function jadwal()
    {
        return $this->hasMany(Jadwal::class, 'paket_id');
    }
    
    /**
     * Get bookings through jadwal
     */
    public function bookings()
    {
        return $this->hasManyThrough(Pemesanan::class, Jadwal::class, 'paket_id', 'jadwal_id');
    }
    
    /**
     * Alias for bookings relationship (Indonesian naming)
     */
    public function pemesanan()
    {
        return $this->hasMany(Pemesanan::class, 'paket_wisata_id');
    }
    
    /**
     * Boot method to register model event listeners
     * 
     * Requirements: 10.3 - Cache invalidation on package CRUD operations
     */
    protected static function booted()
    {
        // Auto-generate slug before creating if not provided
        static::creating(function ($package) {
            if (empty($package->slug)) {
                $package->slug = \Illuminate\Support\Str::slug($package->nama_paket);
                
                // Ensure uniqueness
                $originalSlug = $package->slug;
                $counter = 1;
                while (static::where('slug', $package->slug)->exists()) {
                    $package->slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }
        });
        
        // Auto-update slug when nama_paket changes
        static::updating(function ($package) {
            if ($package->isDirty('nama_paket')) {
                $package->slug = \Illuminate\Support\Str::slug($package->nama_paket);

                // Ensure uniqueness
                $originalSlug = $package->slug;
                $counter = 1;
                while (static::where('slug', $package->slug)->where('id', '!=', $package->id)->exists()) {
                    $package->slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }
        });
        
        // Clear category cache when package is saved (created or updated)
        static::saved(function ($package) {
            $currentJenisPaket = $package->jenis_paket;
            $cacheKeys = ['packages.category.' . md5($currentJenisPaket)];

            if ($package->wasChanged('jenis_paket')) {
                $previousJenisPaket = $package->getOriginal('jenis_paket');
                $cacheKeys[] = 'packages.category.' . md5($previousJenisPaket);
            }

            foreach (array_unique($cacheKeys) as $cacheKey) {
                \Cache::forget($cacheKey);
                \Log::info("Cache cleared for package category", ['key' => $cacheKey]);
            }
        });
        
        // Clear category cache when package is deleted
        static::deleted(function ($package) {
            $cacheKeys = [
                'packages.category.' . md5($package->jenis_paket),
            ];

            foreach (array_unique($cacheKeys) as $cacheKey) {
                \Cache::forget($cacheKey);
                \Log::info("Cache cleared for deleted package category", ['key' => $cacheKey]);
            }
        });
    }
}
