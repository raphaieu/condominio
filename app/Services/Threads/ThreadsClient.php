<?php

namespace App\Services\Threads;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
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

        $response = $this->http()->get($this->baseUrl().'/me', [
            'fields' => 'id,username,name,threads_profile_picture_url,threads_biography,is_verified',
            'access_token' => $accessToken,
        ]);

        $response->throw();

        return $response->json();
    }

    public function getUserThreads(string $accessToken): array
    {
        if ($this->isMock()) {
            return $this->mockUserThreads();
        }

        // TODO: Confirm pagination and field names with Threads API docs.
        $response = $this->http()->get($this->baseUrl().'/me/threads', [
            'fields' => 'id,text,media_type,permalink,timestamp',
            'access_token' => $accessToken,
        ]);

        $response->throw();

        return $response->json('data', []);
    }

    public function getAccountInsights(string $accessToken): array
    {
        if ($this->isMock()) {
            return $this->mockAccountInsights();
        }

        // TODO: Map insight metrics to our snapshot fields once API access is approved.
        $response = $this->http()->get($this->baseUrl().'/me/threads_insights', [
            'metric' => 'views,likes,replies,reposts,quotes,clicks,followers_count',
            'access_token' => $accessToken,
        ]);

        $response->throw();

        return $response->json('data', []);
    }

    public function getMediaInsights(string $mediaId, string $accessToken): array
    {
        if ($this->isMock()) {
            return $this->mockMediaInsights($mediaId);
        }

        // TODO: Confirm media insights endpoint and metrics.
        $response = $this->http()->get($this->baseUrl()."/{$mediaId}/insights", [
            'metric' => 'views,likes,replies,reposts,quotes',
            'access_token' => $accessToken,
        ]);

        $response->throw();

        return $response->json('data', []);
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
