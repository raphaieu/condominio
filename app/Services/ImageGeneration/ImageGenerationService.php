<?php

namespace App\Services\ImageGeneration;

use App\Jobs\GenerateResidentHouseImageJob;
use App\Models\CondominiumResult;
use App\Models\GeneratedAsset;
use App\Models\ImageGeneration;
use App\Models\User;
use App\Services\Premium\PremiumAccessService;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ImageGenerationService
{
    public function __construct(
        protected PremiumAccessService $premiumAccessService,
    ) {}

    public function requestGeneration(User $user, CondominiumResult $result): ImageGeneration
    {
        if (! $this->premiumAccessService->canGenerate($user, $result)) {
            throw new RuntimeException('Geração premium não liberada para este resultado.');
        }

        $active = ImageGeneration::query()
            ->where('condominium_result_id', $result->id)
            ->whereIn('status', [ImageGeneration::STATUS_PENDING, ImageGeneration::STATUS_PROCESSING])
            ->latest()
            ->first();

        if ($active) {
            return $active;
        }

        $completed = ImageGeneration::query()
            ->where('condominium_result_id', $result->id)
            ->where('status', ImageGeneration::STATUS_COMPLETED)
            ->exists();

        if ($completed) {
            return ImageGeneration::query()
                ->where('condominium_result_id', $result->id)
                ->where('status', ImageGeneration::STATUS_COMPLETED)
                ->latest()
                ->firstOrFail();
        }

        $generation = ImageGeneration::query()->create([
            'user_id' => $user->id,
            'condominium_result_id' => $result->id,
            'status' => ImageGeneration::STATUS_PENDING,
            'provider' => config('services.premium_image.provider', 'mock'),
        ]);

        GenerateResidentHouseImageJob::dispatch($generation->id);

        return $generation;
    }

    public function retry(ImageGeneration $generation): ImageGeneration
    {
        if (! $generation->canRetry()) {
            throw new RuntimeException('Esta geração não pode ser repetida.');
        }

        $generation->update([
            'status' => ImageGeneration::STATUS_PENDING,
            'error_message' => null,
            'started_at' => null,
            'completed_at' => null,
        ]);

        GenerateResidentHouseImageJob::dispatch($generation->id);

        return $generation->fresh();
    }

    /**
     * @return array{
     *     status: string,
     *     generation_id: ?int,
     *     error_message: ?string,
     *     asset: ?array<string, mixed>,
     *     can_generate: bool,
     *     can_retry: bool
     * }
     */
    public function getStatus(User $user, CondominiumResult $result): array
    {
        $generation = $result->latestImageGeneration;

        $asset = null;
        $assets = [];

        if ($generation?->status === ImageGeneration::STATUS_COMPLETED) {
            $assets = GeneratedAsset::query()
                ->where('image_generation_id', $generation->id)
                ->get()
                ->map(fn (GeneratedAsset $asset) => [
                    'type' => $asset->type,
                    'url' => $asset->url(),
                    'width' => $asset->width,
                    'height' => $asset->height,
                ])
                ->values()
                ->all();

            $facade = collect($assets)->firstWhere('type', GeneratedAsset::TYPE_FACADE);

            if ($facade) {
                $asset = $facade;
            }
        }

        return [
            'status' => $generation?->status ?? 'none',
            'generation_id' => $generation?->id,
            'error_message' => $generation?->error_message,
            'asset' => $asset,
            'assets' => $assets ?? [],
            'can_generate' => $this->premiumAccessService->canGenerate($user, $result)
                && (! $generation || $generation->status === ImageGeneration::STATUS_FAILED),
            'can_retry' => $generation?->canRetry() ?? false,
        ];
    }

    public function forceNewGeneration(User $user, CondominiumResult $result): ImageGeneration
    {
        $generation = DB::transaction(function () use ($user, $result) {
            ImageGeneration::query()
                ->where('condominium_result_id', $result->id)
                ->whereIn('status', [ImageGeneration::STATUS_PENDING, ImageGeneration::STATUS_PROCESSING])
                ->update(['status' => ImageGeneration::STATUS_FAILED, 'error_message' => 'Substituída por nova geração.']);

            return ImageGeneration::query()->create([
                'user_id' => $user->id,
                'condominium_result_id' => $result->id,
                'status' => ImageGeneration::STATUS_PENDING,
                'provider' => config('services.premium_image.provider', 'mock'),
            ]);
        });

        GenerateResidentHouseImageJob::dispatch($generation->id);

        return $generation;
    }
}
