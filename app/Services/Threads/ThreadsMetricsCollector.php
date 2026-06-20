<?php

namespace App\Services\Threads;

use App\Models\ThreadsAccount;
use App\Models\ThreadsProfileSnapshot;
use Illuminate\Support\Carbon;

class ThreadsMetricsCollector
{
    public function __construct(
        protected ThreadsClient $client,
    ) {}

    public function collect(ThreadsAccount $account): ThreadsProfileSnapshot
    {
        $accessToken = $account->access_token;

        if (! $accessToken) {
            throw new \RuntimeException('Conta Threads sem access token.');
        }

        $insights = $this->client->getAccountInsights($accessToken);
        $threads = $this->client->getUserThreads($accessToken);

        $metrics = $this->parseInsights($insights);

        $snapshot = $account->profileSnapshots()->create([
            'followers_count' => $metrics['followers_count'],
            'views' => $metrics['views'],
            'likes' => $metrics['likes'],
            'replies' => $metrics['replies'],
            'reposts' => $metrics['reposts'],
            'quotes' => $metrics['quotes'],
            'clicks' => $metrics['clicks'],
            'posts_count' => count($threads),
            'captured_at' => now(),
        ]);

        foreach ($threads as $threadData) {
            $this->syncPost($account, $threadData, $accessToken);
        }

        return $snapshot;
    }

    protected function syncPost(ThreadsAccount $account, array $threadData, string $accessToken): void
    {
        $mediaId = $threadData['id'] ?? null;

        if (! $mediaId) {
            return;
        }

        $post = $account->posts()->updateOrCreate(
            ['threads_media_id' => $mediaId],
            [
                'text' => $threadData['text'] ?? null,
                'permalink' => $threadData['permalink'] ?? null,
                'media_type' => $threadData['media_type'] ?? null,
                'published_at' => isset($threadData['timestamp'])
                    ? Carbon::parse($threadData['timestamp'])
                    : null,
            ]
        );

        $insights = $this->client->getMediaInsights($mediaId, $accessToken);
        $metrics = $this->parseInsights($insights);

        $post->snapshots()->create([
            'views' => $metrics['views'],
            'likes' => $metrics['likes'],
            'replies' => $metrics['replies'],
            'reposts' => $metrics['reposts'],
            'quotes' => $metrics['quotes'],
            'captured_at' => now(),
        ]);
    }

    /**
     * @param  array<int, array{name: string, values: array<int, array{value: int}>}>  $insights
     * @return array<string, int>
     */
    protected function parseInsights(array $insights): array
    {
        $defaults = [
            'followers_count' => 0,
            'views' => 0,
            'likes' => 0,
            'replies' => 0,
            'reposts' => 0,
            'quotes' => 0,
            'clicks' => 0,
        ];

        foreach ($insights as $insight) {
            $name = $insight['name'] ?? null;
            $value = $insight['values'][0]['value'] ?? 0;

            if ($name && array_key_exists($name, $defaults)) {
                $defaults[$name] = (int) $value;
            }
        }

        return $defaults;
    }
}
