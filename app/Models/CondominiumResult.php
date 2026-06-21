<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CondominiumResult extends Model
{
    protected $fillable = [
        'user_id',
        'threads_account_id',
        'score',
        'property_type',
        'neighborhood',
        'symbolic_address',
        'social_class',
        'estimated_value',
        'description',
        'is_public',
        'generated_at',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'is_public' => 'boolean',
            'generated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function threadsAccount(): BelongsTo
    {
        return $this->belongsTo(ThreadsAccount::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function premiumUnlocks(): HasMany
    {
        return $this->hasMany(PremiumUnlock::class);
    }

    public function imageGenerations(): HasMany
    {
        return $this->hasMany(ImageGeneration::class);
    }

    public function latestImageGeneration(): HasOne
    {
        return $this->hasOne(ImageGeneration::class)->latestOfMany();
    }

    public function latestCompletedGeneration(): HasOne
    {
        return $this->hasOne(ImageGeneration::class)
            ->where('status', ImageGeneration::STATUS_COMPLETED)
            ->latestOfMany();
    }

    public function generatedAssets(): HasMany
    {
        return $this->hasMany(GeneratedAsset::class);
    }

    public function facadeAsset(): ?GeneratedAsset
    {
        $generation = $this->relationLoaded('latestCompletedGeneration')
            ? $this->latestCompletedGeneration
            : $this->latestCompletedGeneration()->first();

        if (! $generation) {
            return null;
        }

        $assets = $generation->relationLoaded('generatedAssets')
            ? $generation->generatedAssets
            : $generation->generatedAssets()->get();

        return $assets->firstWhere('type', GeneratedAsset::TYPE_FACADE);
    }

    public function formattedEstimatedValue(): string
    {
        return 'R$ '.number_format($this->estimated_value, 0, ',', '.');
    }
}
