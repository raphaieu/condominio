<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('premium_unlocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('condominium_result_id')->constrained()->cascadeOnDelete();
            $table->string('source'); // manual, test_mode, future_payment
            $table->string('status')->default('active'); // active, revoked
            $table->timestamp('unlocked_at');
            $table->timestamp('expires_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'condominium_result_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('premium_unlocks');
    }
};
