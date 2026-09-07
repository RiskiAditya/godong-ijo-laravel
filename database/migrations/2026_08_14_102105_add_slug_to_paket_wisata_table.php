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
            $table->string('slug')->unique()->nullable()->after('nama_paket');
        });
        
        // Generate slugs for existing records
        DB::statement("UPDATE paket_wisata SET slug = LOWER(REPLACE(REPLACE(nama_paket, ' ', '-'), '&', 'and')) WHERE slug IS NULL");
        
        // Make slug required after populating existing records
        Schema::table('paket_wisata', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paket_wisata', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
