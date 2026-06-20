<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('threads_post_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('threads_post_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('views')->default(0);
            $table->unsignedInteger('likes')->default(0);
            $table->unsignedInteger('replies')->default(0);
            $table->unsignedInteger('reposts')->default(0);
            $table->unsignedInteger('quotes')->default(0);
            $table->timestamp('captured_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('threads_post_snapshots');
    }
};
