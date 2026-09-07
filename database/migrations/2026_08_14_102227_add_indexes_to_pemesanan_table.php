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
            // Single column indexes for frequent queries
            $table->index('status', 'idx_pemesanan_status');
            $table->index('created_at', 'idx_pemesanan_created_at');
            $table->index('tanggal_kunjungan', 'idx_pemesanan_tanggal_kunjungan');
            
            // Composite index for admin dashboard filtering (status + created_at)
            $table->index(['status', 'created_at'], 'idx_pemesanan_status_created');
            
            // Foreign key indexes (if not already indexed)
            // Note: Foreign keys usually auto-create indexes, but explicit is better
            $table->index('user_id', 'idx_pemesanan_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemesanan', function (Blueprint $table) {
            // Drop indexes in reverse order
            $table->dropIndex('idx_pemesanan_user_id');
            $table->dropIndex('idx_pemesanan_status_created');
            $table->dropIndex('idx_pemesanan_tanggal_kunjungan');
            $table->dropIndex('idx_pemesanan_created_at');
            $table->dropIndex('idx_pemesanan_status');
        });
    }
};
