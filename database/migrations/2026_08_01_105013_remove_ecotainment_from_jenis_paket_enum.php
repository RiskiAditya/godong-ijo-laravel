<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update any existing 'Ecotainment' values to NULL before changing enum
        DB::statement("UPDATE paket_wisata SET jenis_paket = NULL WHERE jenis_paket = 'Ecotainment'");
        
        // Alter the enum to remove 'Ecotainment'
        DB::statement("ALTER TABLE paket_wisata MODIFY jenis_paket ENUM('The Waterfall Resto', 'Private Room', 'Fishing Lake') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add 'Ecotainment' back to enum
        DB::statement("ALTER TABLE paket_wisata MODIFY jenis_paket ENUM('The Waterfall Resto', 'Private Room', 'Fishing Lake', 'Ecotainment') NULL");
    }
};
