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
        Schema::create('school_partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('level', ['TK', 'SD', 'SMP', 'SMA'])->comment('School level: TK (Kindergarten), SD (Elementary), SMP (Junior High), SMA (Senior High)');
            $table->string('logo_path')->nullable()->comment('Path to school logo image');
            $table->boolean('is_active')->default(true)->comment('Whether this partner should be displayed');
            $table->integer('order')->default(0)->comment('Display order (lower numbers appear first)');
            $table->timestamps();
            
            // Add indexes for efficient querying
            $table->index(['is_active', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_partners');
    }
};
