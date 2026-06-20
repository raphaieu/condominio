<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThreadsPostSnapshot extends Model
{
    protected $fillable = [
        'threads_post_id',
        'views',
        'likes',
        'replies',
        'reposts',
        'quotes',
        'captured_at',
    ];

    protected function casts(): array
    {
        return [
            'captured_at' => 'datetime',
        ];
    }

    public function threadsPost(): BelongsTo
    {
        return $this->belongsTo(ThreadsPost::class);
    }
}
