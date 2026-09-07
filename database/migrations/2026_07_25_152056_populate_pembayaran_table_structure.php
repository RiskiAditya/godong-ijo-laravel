<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Add columns to pembayaran table:
     * - pemesanan_id (foreign key with cascade delete)
     * - order_id (VARCHAR 255, unique)
     * - transaction_id (VARCHAR 255)
     * - payment_type (VARCHAR 50)
     * - gross_amount (DECIMAL 12,2)
     * - status (ENUM: pending, success, failed, expired)
     * - snap_token (TEXT)
     * - midtrans_response (JSON)
     * 
     * Validates: Requirements 8.1, 8.2, 8.3, 8.4, 8.5, 8.6
     */
    public function up(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            // Add pemesanan_id foreign key with cascade delete (Requirement 8.2)
            $table->foreignId('pemesanan_id')->after('id')->constrained('pemesanan')->onDelete('cascade');
            
            // Add order_id - unique identifier for the transaction (Requirement 8.3)
            $table->string('order_id', 255)->after('pemesanan_id')->unique();
            
            // Add transaction_id from Midtrans
            $table->string('transaction_id', 255)->after('order_id')->nullable();
            
            // Add payment_type (e.g., bank_transfer, credit_card, gopay)
            $table->string('payment_type', 50)->after('transaction_id')->nullable();
            
            // Add gross_amount - total payment amount (Requirement 8.6)
            $table->decimal('gross_amount', 12, 2)->after('payment_type');
            
            // Add status enum (Requirement 8.4)
            $table->enum('status', ['pending', 'success', 'failed', 'expired'])->after('gross_amount')->default('pending');
            
            // Add snap_token - token from Midtrans for payment popup
            $table->text('snap_token')->after('status')->nullable();
            
            // Add midtrans_response - complete JSON response from Midtrans (Requirement 8.5)
            $table->json('midtrans_response')->after('snap_token')->nullable();
            
            // Add indexes for better query performance (Requirement 8.3)
            $table->index('order_id');
            $table->index('transaction_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex(['order_id']);
            $table->dropIndex(['transaction_id']);
            $table->dropIndex(['status']);
            
            // Drop foreign key constraint
            $table->dropForeign(['pemesanan_id']);
            
            // Drop columns in reverse order
            $table->dropColumn([
                'midtrans_response',
                'snap_token',
                'status',
                'gross_amount',
                'payment_type',
                'transaction_id',
                'order_id',
                'pemesanan_id',
            ]);
        });
    }
};
