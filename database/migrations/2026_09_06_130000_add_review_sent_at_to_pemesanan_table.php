<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('pemesanan', 'review_sent_at')) {
            Schema::table('pemesanan', function (Blueprint $table) {
                $table->timestamp('review_sent_at')->nullable()->after('reminder_sent_at');
            });
        }

        $indexExists = collect(Schema::getIndexes('pemesanan'))
            ->contains(fn (array $index) => $index['name'] === 'pemesanan_review_sent_at_index');

        if (!$indexExists) {
            Schema::table('pemesanan', function (Blueprint $table) {
                $table->index('review_sent_at');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pemesanan', 'review_sent_at')) {
            Schema::table('pemesanan', function (Blueprint $table) {
                $table->dropIndex(['review_sent_at']);
                $table->dropColumn('review_sent_at');
            });
        }
    }
};
