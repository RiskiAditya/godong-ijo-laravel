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
        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->onDelete('cascade');
            $table->string('type', 50); // 'booking', 'payment', 'cancellation'
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable(); // Extra data (booking details, etc)
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            // Foreign key to pemesanan table (using 'id' not 'id_pemesanan')
            $table->foreign('booking_id')->references('id')->on('pemesanan')->onDelete('set null');
            
            // Indexes for performance
            $table->index(['admin_id', 'is_read', 'created_at']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_notifications');
    }
};
