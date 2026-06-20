<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('threads_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('threads_account_id')->constrained()->cascadeOnDelete();
            $table->string('threads_media_id')->nullable()->index();
            $table->text('text')->nullable();
            $table->text('permalink')->nullable();
            $table->string('media_type')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('threads_posts');
    }
};
