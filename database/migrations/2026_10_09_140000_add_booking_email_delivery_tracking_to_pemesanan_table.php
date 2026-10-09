<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pemesanan', function (Blueprint $table) {
            $table->boolean('booking_confirmation_email_tracking_enabled')->default(false);
            $table->boolean('payment_success_email_tracking_enabled')->default(false);
            $table->timestamp('booking_confirmation_email_sent_at')->nullable();
            $table->unsignedTinyInteger('booking_confirmation_email_attempts')->default(0);
            $table->timestamp('booking_confirmation_email_last_attempt_at')->nullable();
            $table->timestamp('payment_success_email_sent_at')->nullable();
            $table->unsignedTinyInteger('payment_success_email_attempts')->default(0);
            $table->timestamp('payment_success_email_last_attempt_at')->nullable();
            $table->index(
                ['booking_confirmation_email_tracking_enabled', 'status', 'booking_confirmation_email_sent_at'],
                'pemesanan_confirmation_email_retry_idx',
            );
            $table->index(
                ['payment_success_email_tracking_enabled', 'status', 'payment_success_email_sent_at'],
                'pemesanan_payment_email_retry_idx',
            );
        });
    }

    public function down(): void
    {
        Schema::table('pemesanan', function (Blueprint $table) {
            $table->dropIndex('pemesanan_confirmation_email_retry_idx');
            $table->dropIndex('pemesanan_payment_email_retry_idx');
            $table->dropColumn([
                'booking_confirmation_email_tracking_enabled',
                'payment_success_email_tracking_enabled',
                'booking_confirmation_email_sent_at',
                'booking_confirmation_email_attempts',
                'booking_confirmation_email_last_attempt_at',
                'payment_success_email_sent_at',
                'payment_success_email_attempts',
                'payment_success_email_last_attempt_at',
            ]);
        });
    }
};
