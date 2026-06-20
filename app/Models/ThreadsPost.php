<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ThreadsPost extends Model
{
    protected $fillable = [
        'threads_account_id',
        'threads_media_id',
        'text',
        'permalink',
        'media_type',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function threadsAccount(): BelongsTo
    {
        return $this->belongsTo(ThreadsAccount::class);
    }

    public function snapshots(): HasMany
    {
        return $this->hasMany(ThreadsPostSnapshot::class);
    }

    public function latestSnapshot(): HasOne
    {
        return $this->hasOne(ThreadsPostSnapshot::class)->latestOfMany('captured_at');
    }
}
