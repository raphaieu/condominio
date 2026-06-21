<?php

namespace Tests\Unit;

use App\Models\ThreadsAccount;
use App\Models\ThreadsProfileSnapshot;
use App\Models\User;
use App\Services\Scoring\ContentNicheDetector;
use App\Services\Scoring\NeighborhoodCatalog;
use App\Services\Scoring\PropertyClassifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyClassifierTest extends TestCase
{
    use RefreshDatabase;

    public function test_assigns_premium_neighborhood_for_high_score(): void
    {
        $classifier = new PropertyClassifier(
            new NeighborhoodCatalog,
            new ContentNicheDetector(new NeighborhoodCatalog),
        );

        $result = $classifier->classify(92, 'elite_user');

        $this->assertSame('Bairro Premium', $result['neighborhood']);
    }

    public function test_assigns_inactive_neighborhood_when_no_posts(): void
    {
        $user = User::factory()->create();
        $account = ThreadsAccount::query()->create([
            'user_id' => $user->id,
            'threads_user_id' => 't3',
            'username' => 'lurker',
            'connected_at' => now(),
        ]);

        $snapshot = ThreadsProfileSnapshot::query()->create([
            'threads_account_id' => $account->id,
            'followers_count' => 10,
            'views' => 100,
            'likes' => 5,
            'replies' => 0,
            'reposts' => 0,
            'quotes' => 0,
            'clicks' => 0,
            'posts_count' => 0,
            'captured_at' => now(),
        ]);

        $classifier = new PropertyClassifier(
            new NeighborhoodCatalog,
            new ContentNicheDetector(new NeighborhoodCatalog),
        );

        $result = $classifier->classify(50, 'lurker', $account, $snapshot);

        $this->assertSame('Bairro Esquecido', $result['neighborhood']);
    }
}
