<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generated_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('image_generation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('condominium_result_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // facade, story_card, square_card
            $table->string('disk');
            $table->string('path');
            $table->string('public_url')->nullable();
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['condominium_result_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generated_assets');
    }
};
