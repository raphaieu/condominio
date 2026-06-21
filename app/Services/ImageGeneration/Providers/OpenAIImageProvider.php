<?php

namespace App\Services\ImageGeneration\Providers;

use App\Services\ImageGeneration\Contracts\ImageProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class OpenAIImageProvider implements ImageProviderInterface
{
    public function generate(string $prompt): array
    {
        $apiKey = config('services.openai.api_key');
        $model = config('services.openai.image_model', 'gpt-image-1');
        $size = config('services.openai.image_size', '1024x1536');

        if (blank($apiKey)) {
            throw new RuntimeException('OPENAI_API_KEY não configurada.');
        }

        $response = Http::withToken($apiKey)
            ->timeout(180)
            ->post('https://api.openai.com/v1/images/generations', $this->buildPayload($model, $prompt, $size));

        if (! $response->successful()) {
            $message = data_get($response->json(), 'error.message', $response->body());

            if (Str::contains($message, ['dall-e-3', 'does not exist', 'invalid_value'])) {
                $message .= ' Use OPENAI_IMAGE_MODEL=gpt-image-1 (ou gpt-image-1.5) no .env.';
            }

            throw new RuntimeException('Falha na API OpenAI: '.$message);
        }

        $imageUrl = data_get($response->json(), 'data.0.url');
        $b64 = data_get($response->json(), 'data.0.b64_json');
        [$width, $height] = $this->parseSize($size);

        if ($b64) {
            $binary = base64_decode($b64, true);

            if ($binary === false) {
                throw new RuntimeException('Resposta inválida da API OpenAI.');
            }

            return [
                'binary' => $binary,
                'mime' => 'image/png',
                'width' => $width,
                'height' => $height,
                'model' => $model,
            ];
        }

        if (! is_string($imageUrl) || $imageUrl === '') {
            throw new RuntimeException('A API OpenAI não retornou imagem.');
        }

        $imageResponse = Http::timeout(60)->get($imageUrl);

        if (! $imageResponse->successful()) {
            throw new RuntimeException('Falha ao baixar imagem gerada.');
        }

        return [
            'binary' => $imageResponse->body(),
            'mime' => $imageResponse->header('Content-Type') ?: 'image/png',
            'width' => $width,
            'height' => $height,
            'model' => $model,
        ];
    }

    public function name(): string
    {
        return 'openai';
    }

    /**
     * @return array<string, mixed>
     */
    protected function buildPayload(string $model, string $prompt, string $size): array
    {
        $payload = [
            'model' => $model,
            'prompt' => $prompt,
            'n' => 1,
        ];

        if (str_starts_with($model, 'gpt-image')) {
            return array_merge($payload, [
                'size' => $size,
                'quality' => config('services.openai.image_quality', 'medium'),
                'output_format' => 'png',
            ]);
        }

        if ($model === 'dall-e-3') {
            return array_merge($payload, [
                'size' => $this->mapDallE3Size($size),
                'quality' => 'standard',
                'response_format' => 'b64_json',
            ]);
        }

        return array_merge($payload, [
            'size' => '1024x1024',
            'response_format' => 'b64_json',
        ]);
    }

    protected function mapDallE3Size(string $size): string
    {
        return match ($size) {
            '1536x1024' => '1792x1024',
            '1024x1536' => '1024x1792',
            default => '1024x1024',
        };
    }

    /**
     * @return array{0: int, 1: int}
     */
    protected function parseSize(string $size): array
    {
        if (preg_match('/^(\d+)x(\d+)$/', $size, $matches)) {
            return [(int) $matches[1], (int) $matches[2]];
        }

        return [1024, 1536];
    }
}
