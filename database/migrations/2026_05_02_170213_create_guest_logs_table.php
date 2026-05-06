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
        Schema::create('guest_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guest_id')->nullable()->constrained()->cascadeOnDelete();
            $table->enum('action', [
                'imported',
                'viewed',
                'confirmed',
                'declined',
                'updated'
            ]);
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->index('guest_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guest_logs');
    }
};
