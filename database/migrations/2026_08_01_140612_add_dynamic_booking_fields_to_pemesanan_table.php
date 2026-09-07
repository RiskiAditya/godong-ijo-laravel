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
            // Add JSON column for package-specific data storage
            $table->json('package_specific_data')->nullable()->after('no_hp');
            
            // Add booking date column
            $table->date('tanggal_kunjungan')->nullable()->after('package_specific_data');

            // Add visit time column for fishing bookings
            $table->string('jam_kunjungan', 5)->nullable()->after('tanggal_kunjungan');
            
            // Add notes/special requests column
            $table->text('catatan')->nullable()->after('jam_kunjungan');
            
            // Add index for query performance on tanggal_kunjungan
            $table->index('tanggal_kunjungan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemesanan', function (Blueprint $table) {
            // Drop index first
            $table->dropIndex(['tanggal_kunjungan']);
            
            // Drop columns
            $table->dropColumn(['package_specific_data', 'tanggal_kunjungan', 'jam_kunjungan', 'catatan']);
        });
    }
};
