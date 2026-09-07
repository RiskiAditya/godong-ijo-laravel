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
            if (!Schema::hasColumn('pemesanan', 'jam_kunjungan')) {
                $table->string('jam_kunjungan', 5)->nullable()->after('tanggal_kunjungan');
            }

            if (Schema::hasColumn('pemesanan', 'total_harga')) {
                $table->decimal('total_harga', 12, 2)->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemesanan', function (Blueprint $table) {
            if (Schema::hasColumn('pemesanan', 'jam_kunjungan')) {
                $table->dropColumn('jam_kunjungan');
            }
        });
    }
};
