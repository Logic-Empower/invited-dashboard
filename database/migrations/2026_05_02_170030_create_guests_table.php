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
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->integer('max_seats')->default(1);
            $table->integer('confirmed_seats')->default(0);
            $table->enum('status', [
                'pending',
                'viewed',
                'confirmed',
                'declined',
                'cancelled'
            ])->default('pending');
            $table->string('invite_code')->unique();
            $table->timestamp('viewed_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('status');
            $table->index('event_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
