<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('condominium_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('threads_account_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('score', 5, 2);
            $table->string('property_type');
            $table->string('neighborhood');
            $table->string('symbolic_address');
            $table->string('social_class')->nullable();
            $table->unsignedInteger('estimated_value')->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(true);
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('condominium_results');
    }
};
