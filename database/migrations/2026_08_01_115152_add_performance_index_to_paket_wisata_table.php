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
            // Composite index for filtering queries by jenis_paket and is_active
            $table->index(['jenis_paket', 'is_active'], 'idx_jenis_paket_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paket_wisata', function (Blueprint $table) {
            $table->dropIndex('idx_jenis_paket_active');
        });
    }
};
