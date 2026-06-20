<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ThreadsAccount extends Model
{
    protected $fillable = [
        'user_id',
        'threads_user_id',
        'username',
        'name',
        'avatar_url',
        'biography',
        'is_verified',
        'access_token',
        'token_expires_at',
        'connected_at',
        'disconnected_at',
    ];

    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
            'access_token' => 'encrypted',
            'token_expires_at' => 'datetime',
            'connected_at' => 'datetime',
            'disconnected_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function profileSnapshots(): HasMany
    {
        return $this->hasMany(ThreadsProfileSnapshot::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(ThreadsPost::class);
    }

    public function condominiumResults(): HasMany
    {
        return $this->hasMany(CondominiumResult::class);
    }

    public function latestProfileSnapshot(): HasOne
    {
        return $this->hasOne(ThreadsProfileSnapshot::class)->latestOfMany('captured_at');
    }

    public function latestResult(): HasOne
    {
        return $this->hasOne(CondominiumResult::class)->latestOfMany('generated_at');
    }

    public function isConnected(): bool
    {
        return $this->disconnected_at === null;
    }
}
