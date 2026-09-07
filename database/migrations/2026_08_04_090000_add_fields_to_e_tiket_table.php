<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Perbaikan desain tabel e_tiket.
 *
 * Sebelumnya tabel e_tiket hanya berisi id + timestamps, sehingga tidak bisa
 * dikaitkan ke pemesanan mana pun. Migration ini melengkapi tabel sesuai
 * kebutuhan fungsional SRS-F-20 & SRS-F-21 (menerbitkan & mengunduh e-tiket),
 * dan disesuaikan dengan cara ETicketService.php menghasilkan e-tiket
 * (di-generate dari relasi Pemesanan, bukan menyimpan data yang sudah
 * ada di tabel lain, agar tidak terjadi redundansi/duplikasi data).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('e_tiket', function (Blueprint $table) {
            // 1 pemesanan hanya boleh punya 1 e-tiket (relasi one-to-one)
            $table->foreignId('pemesanan_id')
                ->after('id')
                ->unique()
                ->constrained('pemesanan')
                ->cascadeOnDelete();

            // Lokasi file PDF hasil generate DomPDF (nullable: baru terisi
            // setelah PDF benar-benar dibuat, bukan saat baris pertama dibuat)
            $table->string('file_path')->nullable()->after('pemesanan_id');

            // Kapan e-tiket ini pertama kali diterbitkan
            $table->timestamp('diterbitkan_pada')->nullable()->after('file_path');
        });
    }

    public function down(): void
    {
        Schema::table('e_tiket', function (Blueprint $table) {
            $table->dropForeign(['pemesanan_id']);
            $table->dropColumn(['pemesanan_id', 'file_path', 'diterbitkan_pada']);
        });
    }
};
