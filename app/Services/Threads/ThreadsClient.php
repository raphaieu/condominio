<?php

namespace App\Services\Threads;

use App\Exceptions\ThreadsApiException;
use App\Support\ThreadsSafeLogger;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ThreadsClient
{
    public function http(): PendingRequest
    {
        return Http::timeout(30)->acceptJson();
    }

    protected function baseUrl(): string
    {
        return rtrim(config('services.threads.graph_base'), '/');
    }

    protected function isMock(): bool
    {
        return (bool) config('services.threads.mock');
    }

    /**
     * @return array{access_token: string, user_id: string, expires_in: int}
     */
    public function mockTokenResponse(): array
    {
        return [
            'access_token' => 'mock_access_token_'.Str::random(32),
            'user_id' => 'mock_user_'.Str::random(8),
            'expires_in' => 5184000,
        ];
    }

    public function getMe(string $accessToken): array
    {
        if ($this->isMock()) {
            return $this->mockMe();
        }

        return $this->getJson($this->baseUrl().'/me', [
            'fields' => 'id,username,name,threads_profile_picture_url,threads_biography,is_verified',
            'access_token' => $accessToken,
        ]);
    }

    public function getUserThreads(string $accessToken, int $limit = 25): array
    {
        if ($this->isMock()) {
            return $this->mockUserThreads();
        }

        $response = $this->getJson($this->baseUrl().'/me/threads', [
            'fields' => 'id,text,timestamp,permalink,media_type,media_url,shortcode,is_quote_post',
            'limit' => $limit,
            'access_token' => $accessToken,
        ]);

        return $response['data'] ?? [];
    }

    public function getAccountInsights(string $accessToken): array
    {
        if ($this->isMock()) {
            return $this->mockAccountInsights();
        }

        $response = $this->getJson($this->baseUrl().'/me/threads_insights', [
            'metric' => 'views,likes,replies,reposts,quotes,clicks,followers_count',
            'access_token' => $accessToken,
        ]);

        return $response['data'] ?? [];
    }

    public function getMediaInsights(string $mediaId, string $accessToken): array
    {
        if ($this->isMock()) {
            return $this->mockMediaInsights($mediaId);
        }

        $response = $this->getJson($this->baseUrl()."/{$mediaId}/insights", [
            'metric' => 'views,likes,replies,reposts,quotes',
            'access_token' => $accessToken,
        ]);

        return $response['data'] ?? [];
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    protected function getJson(string $url, array $query): array
    {
        $response = $this->http()->retry(2, 200, throw: false)->get($url, $query);

        if ($response->failed()) {
            $this->throwApiException($response);
        }

        $json = $response->json();

        return is_array($json) ? $json : [];
    }

    protected function throwApiException(Response $response): never
    {
        $summary = ThreadsSafeLogger::summarizeMetaError($response);

        Log::error('Threads API request failed', $summary);

        throw new ThreadsApiException(
            is_string($summary['message'] ?? null) ? $summary['message'] : 'Erro na API do Threads.',
            $response->status(),
            is_string($summary['fbtrace_id'] ?? null) ? $summary['fbtrace_id'] : null,
            $summary,
        );
    }

    protected function mockMe(): array
    {
        $suffix = Str::lower(Str::random(6));

        return [
            'id' => 'mock_user_'.$suffix,
            'username' => 'morador_'.$suffix,
            'name' => 'Morador do Condomínio',
            'threads_profile_picture_url' => 'https://ui-avatars.com/api/?name=CT&background=0d9488&color=fff&size=256',
            'threads_biography' => 'Residente digital do Condominio Threads. Métricas simbólicas, vibes reais.',
            'is_verified' => fake()->boolean(20),
        ];
    }

    protected function mockUserThreads(): array
    {
        $posts = [];

        for ($i = 1; $i <= fake()->numberBetween(3, 8); $i++) {
            $id = 'mock_media_'.Str::random(10);
            $posts[] = [
                'id' => $id,
                'text' => fake()->sentence(12),
                'media_type' => 'TEXT_POST',
                'permalink' => 'https://www.threads.net/@mock/post/'.$id,
                'timestamp' => now()->subDays($i)->toIso8601String(),
                'media_url' => null,
                'shortcode' => Str::random(8),
                'is_quote_post' => false,
            ];
        }

        return $posts;
    }

    protected function mockAccountInsights(): array
    {
        return [
            ['name' => 'followers_count', 'values' => [['value' => fake()->numberBetween(120, 85000)]]],
            ['name' => 'views', 'values' => [['value' => fake()->numberBetween(5000, 500000)]]],
            ['name' => 'likes', 'values' => [['value' => fake()->numberBetween(200, 25000)]]],
            ['name' => 'replies', 'values' => [['value' => fake()->numberBetween(50, 5000)]]],
            ['name' => 'reposts', 'values' => [['value' => fake()->numberBetween(10, 2000)]]],
            ['name' => 'quotes', 'values' => [['value' => fake()->numberBetween(5, 800)]]],
            ['name' => 'clicks', 'values' => [['value' => fake()->numberBetween(20, 1500)]]],
        ];
    }

    protected function mockMediaInsights(string $mediaId): array
    {
        return [
            ['name' => 'views', 'values' => [['value' => fake()->numberBetween(100, 50000)]]],
            ['name' => 'likes', 'values' => [['value' => fake()->numberBetween(10, 5000)]]],
            ['name' => 'replies', 'values' => [['value' => fake()->numberBetween(2, 500)]]],
            ['name' => 'reposts', 'values' => [['value' => fake()->numberBetween(1, 300)]]],
            ['name' => 'quotes', 'values' => [['value' => fake()->numberBetween(0, 100)]]],
        ];
    }
}
