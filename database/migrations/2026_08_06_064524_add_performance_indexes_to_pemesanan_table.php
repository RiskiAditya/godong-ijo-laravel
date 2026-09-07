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
            // Composite index for bookingsToday() query performance
            // Optimizes: whereDate('created_at', today())->whereIn('status', [...])
            $table->index(['created_at', 'status'], 'idx_pemesanan_created_status');
            
            // Index for kode_booking lookups (confirmation page, status checks)
            // This is already indexed as unique in the original migration, but we ensure it's optimized
            if (!Schema::hasColumn('pemesanan', 'kode_booking')) {
                $table->index('kode_booking', 'idx_pemesanan_kode_booking');
            }
            
            // Index for jadwal_id for join performance
            $table->index('jadwal_id', 'idx_pemesanan_jadwal_id');
            
            // Index for paket_wisata_id for direct relationship queries
            $table->index('paket_wisata_id', 'idx_pemesanan_paket_wisata_id');
        });
        
        // Add index to pembayaran table for order_id lookups (webhook, payment status checks)
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->index('order_id', 'idx_pembayaran_order_id');
            $table->index('pemesanan_id', 'idx_pembayaran_pemesanan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemesanan', function (Blueprint $table) {
            $table->dropIndex('idx_pemesanan_created_status');
            if (Schema::hasColumn('pemesanan', 'kode_booking')) {
                $table->dropIndex('idx_pemesanan_kode_booking');
            }
            $table->dropIndex('idx_pemesanan_jadwal_id');
            $table->dropIndex('idx_pemesanan_paket_wisata_id');
        });
        
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropIndex('idx_pembayaran_order_id');
            $table->dropIndex('idx_pembayaran_pemesanan_id');
        });
    }
};
