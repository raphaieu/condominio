<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PremiumUnlock extends Model
{
    public const SOURCE_MANUAL = 'manual';

    public const SOURCE_TEST_MODE = 'test_mode';

    public const SOURCE_FUTURE_PAYMENT = 'future_payment';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_REVOKED = 'revoked';

    protected $fillable = [
        'user_id',
        'condominium_result_id',
        'source',
        'status',
        'unlocked_at',
        'expires_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'unlocked_at' => 'datetime',
            'expires_at' => 'datetime',
            'metadata' => 'array',
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

    public function isActive(): bool
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }
}
