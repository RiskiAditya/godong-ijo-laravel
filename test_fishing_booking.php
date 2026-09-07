<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PaketWisata;
use App\Models\Pemesanan;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\DB;

DB::beginTransaction();

try {
    // Find or create fishing paket
    $fishingPaket = PaketWisata::firstOrCreate(
        ['jenis_paket' => 'Fishing Lake'],
        [
            'nama_paket' => 'Paket Pemancingan',
            'deskripsi' => 'Paket pemancingan (tarikan/kiloan/sewa joran)',
            'harga' => 0,
            'kuota' => 50,
            'is_active' => true,
        ]
    );

    echo "Fishing Paket ID: {$fishingPaket->id}\n";

    // Create fishing booking
    $pemesanan = Pemesanan::create([
        'kode_booking' => 'GOD-TEST-' . time(),
        'user_id' => null,
        'jadwal_id' => null,
        'paket_wisata_id' => $fishingPaket->id,
        'nama_lengkap' => 'Test User Fishing',
        'email' => null,
        'no_hp' => '081234567890',
        'tanggal_kunjungan' => '2026-08-10',
        'jam_kunjungan' => '09:00',
        'package_specific_data' => json_encode(['jenis_pemancingan' => 'tarikan', 'jumlah_joran' => 2]),
        'jumlah_orang' => 2,
        'total_harga' => 50000,
        'status' => 'pending',
    ]);

    echo "Pemesanan ID: {$pemesanan->id}\n";

    // Create pembayaran
    $pembayaran = Pembayaran::create([
        'pemesanan_id' => $pemesanan->id,
        'order_id' => 'FISHING-' . $pemesanan->id . '-TEST',
        'gross_amount' => 50000,
        'snap_token' => 'TEST-TOKEN',
        'status' => 'pending',
    ]);

    echo "Pembayaran ID: {$pembayaran->id}\n";
    echo "Order ID: {$pembayaran->order_id}\n";

    DB::commit();

    // Verify the created booking
    $booking = Pemesanan::with('paketWisata')->find($pemesanan->id);
    
    echo "\n=== BOOKING DETAILS ===\n";
    echo "ID: {$booking->id}\n";
    echo "Kode Booking: {$booking->kode_booking}\n";
    echo "jadwal_id: " . ($booking->jadwal_id ?? 'NULL') . "\n";
    echo "paket_wisata_id: {$booking->paket_wisata_id}\n";
    echo "Paket Name: {$booking->paketWisata->nama_paket}\n";
    echo "Status: {$booking->status}\n";
    echo "Total Harga: {$booking->total_harga}\n";
    
    // Query raw dari database
    echo "\n=== RAW DATABASE QUERY ===\n";
    $raw = DB::select("SELECT id, kode_booking, jadwal_id, paket_wisata_id, nama_lengkap, status, total_harga FROM pemesanan WHERE id = ?", [$booking->id]);
    print_r($raw[0]);

} catch (\Exception $e) {
    DB::rollBack();
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
