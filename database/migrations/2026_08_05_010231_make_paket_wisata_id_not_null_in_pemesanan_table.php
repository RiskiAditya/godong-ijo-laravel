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
        Schema::table('pemesanan', function (Blueprint $table) {
            // Change paket_wisata_id to NOT NULL
            // This is safe because:
            // 1. Previous migration backfilled all existing records
            // 2. All Pemesanan::create() calls now include paket_wisata_id
            $table->unsignedBigInteger('paket_wisata_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemesanan', function (Blueprint $table) {
            // Revert back to nullable
            $table->unsignedBigInteger('paket_wisata_id')->nullable()->change();
        });
    }
};
