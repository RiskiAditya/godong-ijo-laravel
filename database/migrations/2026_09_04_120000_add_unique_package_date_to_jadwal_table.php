<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $duplicates = DB::table('jadwal')
            ->select('paket_id', 'tanggal', DB::raw('COUNT(*) as total'))
            ->groupBy('paket_id', 'tanggal')
            ->having('total', '>', 1)
            ->get();

        if ($duplicates->isNotEmpty()) {
            throw new RuntimeException(
                'Cannot add unique jadwal constraint: duplicate paket_id/tanggal records exist.'
            );
        }

        Schema::table('jadwal', function (Blueprint $table) {
            $table->unique(['paket_id', 'tanggal'], 'jadwal_paket_tanggal_unique');
        });
    }

    public function down(): void
    {
        Schema::table('jadwal', function (Blueprint $table) {
            $table->dropUnique('jadwal_paket_tanggal_unique');
        });
    }
};
