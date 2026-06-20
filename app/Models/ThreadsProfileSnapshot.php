<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThreadsProfileSnapshot extends Model
{
    protected $fillable = [
        'threads_account_id',
        'followers_count',
        'views',
        'likes',
        'replies',
        'reposts',
        'quotes',
        'clicks',
        'posts_count',
        'captured_at',
    ];

    protected function casts(): array
    {
        return [
            'captured_at' => 'datetime',
        ];
    }

    public function threadsAccount(): BelongsTo
    {
        return $this->belongsTo(ThreadsAccount::class);
    }

    public function totalEngagement(): int
    {
        return $this->likes + $this->replies + $this->reposts + $this->quotes + $this->clicks;
    }
}
