<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ImageGeneration extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'user_id',
        'condominium_result_id',
        'status',
        'provider',
        'model',
        'prompt',
        'error_message',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function condominiumResult(): BelongsTo
    {
        return $this->belongsTo(CondominiumResult::class);
    }

    public function generatedAssets(): HasMany
    {
        return $this->hasMany(GeneratedAsset::class);
    }

    public function facadeAsset(): HasOne
    {
        return $this->hasOne(GeneratedAsset::class)->where('type', GeneratedAsset::TYPE_FACADE);
    }

    public function isActive(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_PROCESSING], true);
    }

    public function canRetry(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }
}
