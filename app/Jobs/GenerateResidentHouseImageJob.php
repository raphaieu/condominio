<?php

namespace App\Jobs;

use App\Models\GeneratedAsset;
use App\Models\ImageGeneration;
use App\Services\ImageGeneration\Contracts\ImageProviderInterface;
use App\Services\ImageGeneration\ImagePromptBuilder;
use App\Services\ImageGeneration\ShareCardGenerator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class GenerateResidentHouseImageJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    /** @var array<int, int> */
    public array $backoff = [10, 30, 60];

    public function __construct(
        public int $imageGenerationId,
    ) {}

    public function handle(
        ImagePromptBuilder $promptBuilder,
        ImageProviderInterface $imageProvider,
        ShareCardGenerator $shareCardGenerator,
    ): void {
        $generation = ImageGeneration::query()->find($this->imageGenerationId);

        if (! $generation || $generation->status === ImageGeneration::STATUS_COMPLETED) {
            return;
        }

        $generation->update([
            'status' => ImageGeneration::STATUS_PROCESSING,
            'provider' => $imageProvider->name(),
            'started_at' => now(),
            'error_message' => null,
        ]);

        try {
            $generation->loadMissing(['condominiumResult.threadsAccount']);

            $result = $generation->condominiumResult;
            $account = $result->threadsAccount;
            $snapshot = $account?->latestProfileSnapshot;

            $prompt = $promptBuilder->build($result, $account, $snapshot);

            $generation->update([
                'prompt' => $prompt,
                'model' => config('services.openai.image_model'),
            ]);

            $image = $imageProvider->generate($prompt);

            $disk = config('services.premium_image.disk', 'public');
            $extension = Str::contains($image['mime'], 'jpeg') ? 'jpg' : 'png';
            $path = sprintf(
                'generated/%d/%d/facade.%s',
                $generation->user_id,
                $generation->id,
                $extension,
            );

            Storage::disk($disk)->put($path, $image['binary'], 'public');

            $publicUrl = Storage::disk($disk)->url($path);

            $facadeAsset = GeneratedAsset::query()->create([
                'image_generation_id' => $generation->id,
                'user_id' => $generation->user_id,
                'condominium_result_id' => $generation->condominium_result_id,
                'type' => GeneratedAsset::TYPE_FACADE,
                'disk' => $disk,
                'path' => $path,
                'public_url' => $publicUrl,
                'width' => $image['width'],
                'height' => $image['height'],
                'metadata' => [
                    'mime' => $image['mime'],
                    'model' => $image['model'],
                ],
            ]);

            try {
                $shareCardGenerator->generateFor($generation, $facadeAsset, $result);
            } catch (Throwable $cardException) {
                Log::warning('Falha ao gerar cards de compartilhamento', [
                    'generation_id' => $generation->id,
                    'message' => $cardException->getMessage(),
                ]);
            }

            $generation->update([
                'status' => ImageGeneration::STATUS_COMPLETED,
                'completed_at' => now(),
                'model' => $image['model'] ?? $generation->model,
            ]);
        } catch (Throwable $exception) {
            Log::error('Falha na geração de imagem premium', [
                'generation_id' => $generation->id,
                'message' => $exception->getMessage(),
            ]);

            $generation->update([
                'status' => ImageGeneration::STATUS_FAILED,
                'error_message' => 'Não foi possível gerar sua casa agora. Tente novamente em instantes.',
                'completed_at' => now(),
            ]);

            throw $exception;
        }
    }
}
