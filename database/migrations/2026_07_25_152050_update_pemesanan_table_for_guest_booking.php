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
            // Drop existing foreign key constraint on user_id
            $table->dropForeign(['user_id']);
            
            // Make user_id nullable to support guest bookings (Requirement 7.2)
            $table->foreignId('user_id')->nullable()->change();
            
            // Re-add foreign key with SET NULL on delete
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
            
            // Add new columns for guest booking information (Requirements 7.1, 7.3, 7.4)
            $table->string('nama_lengkap', 255)->after('jadwal_id');
            $table->string('email', 255)->after('nama_lengkap');
            $table->string('no_hp', 20)->after('email');
            
            // Add indexes for performance (Requirement 7.5)
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemesanan', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
            
            // Drop guest booking columns
            $table->dropColumn(['nama_lengkap', 'email', 'no_hp']);
            
            // Drop foreign key
            $table->dropForeign(['user_id']);
            
            // Make user_id NOT NULL again
            $table->foreignId('user_id')->nullable(false)->change();
            
            // Re-add foreign key with cascade delete
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }
};
