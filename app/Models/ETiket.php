<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ETiket extends Model
{
    use HasFactory;

    protected $table = 'e_tiket';

    protected $fillable = [
        'pemesanan_id',
        'file_path',
        'diterbitkan_pada',
    ];

    protected $casts = [
        'diterbitkan_pada' => 'datetime',
    ];

    /**
     * E-tiket ini milik satu pemesanan.
     */
    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class);
    }
}
