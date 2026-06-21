<?php

namespace Tests\Unit;

use App\Models\ThreadsAccount;
use App\Models\ThreadsPost;
use App\Models\User;
use App\Services\Scoring\ContentNicheDetector;
use App\Services\Scoring\NeighborhoodCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentNicheDetectorTest extends TestCase
{
    use RefreshDatabase;

    public function test_detects_adult_niche_when_threshold_met(): void
    {
        $user = User::factory()->create();
        $account = ThreadsAccount::query()->create([
            'user_id' => $user->id,
            'threads_user_id' => 't1',
            'username' => 'hotcreator',
            'connected_at' => now(),
        ]);

        ThreadsPost::query()->create([
            'threads_account_id' => $account->id,
            'threads_media_id' => 'm1',
            'text' => 'conteúdo sensual do dia',
            'published_at' => now(),
        ]);

        ThreadsPost::query()->create([
            'threads_account_id' => $account->id,
            'threads_media_id' => 'm2',
            'text' => 'post normal',
            'published_at' => now(),
        ]);

        $detector = new ContentNicheDetector(new NeighborhoodCatalog);

        $this->assertTrue($detector->isAdultNiche($account));
    }

    public function test_does_not_detect_adult_niche_below_threshold(): void
    {
        $user = User::factory()->create();
        $account = ThreadsAccount::query()->create([
            'user_id' => $user->id,
            'threads_user_id' => 't2',
            'username' => 'regular',
            'connected_at' => now(),
        ]);

        for ($i = 0; $i < 5; $i++) {
            ThreadsPost::query()->create([
                'threads_account_id' => $account->id,
                'threads_media_id' => "m{$i}",
                'text' => 'post normal sobre tech',
                'published_at' => now(),
            ]);
        }

        ThreadsPost::query()->create([
            'threads_account_id' => $account->id,
            'threads_media_id' => 'm_hot',
            'text' => 'um post com palavra rara xyzabc',
            'published_at' => now(),
        ]);

        $detector = new ContentNicheDetector(new NeighborhoodCatalog);

        $this->assertFalse($detector->isAdultNiche($account));
    }
}
