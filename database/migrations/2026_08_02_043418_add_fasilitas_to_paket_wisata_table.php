<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('paket_wisata', function (Blueprint $table) {
            $table->json('fasilitas')->nullable()->after('deskripsi');
            $table->integer('diskon_persen')->default(0)->after('harga');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paket_wisata', function (Blueprint $table) {
            $table->dropColumn(['fasilitas', 'diskon_persen']);
        });
    }
};
