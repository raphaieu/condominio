<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class GeneratedAsset extends Model
{
    public const TYPE_FACADE = 'facade';

    public const TYPE_STORY_CARD = 'story_card';

    public const TYPE_SQUARE_CARD = 'square_card';

    protected $fillable = [
        'image_generation_id',
        'user_id',
        'condominium_result_id',
        'type',
        'disk',
        'path',
        'public_url',
        'width',
        'height',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function imageGeneration(): BelongsTo
    {
        return $this->belongsTo(ImageGeneration::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function condominiumResult(): BelongsTo
    {
        return $this->belongsTo(CondominiumResult::class);
    }

    public function url(): string
    {
        if ($this->public_url) {
            return $this->public_url;
        }

        return Storage::disk($this->disk)->url($this->path);
    }
}
