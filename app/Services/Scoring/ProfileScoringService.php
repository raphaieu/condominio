<?php

namespace App\Services\Scoring;

use App\Models\ThreadsAccount;
use App\Models\ThreadsProfileSnapshot;

class ProfileScoringService
{
    public function calculate(ThreadsProfileSnapshot $snapshot, ?ThreadsAccount $account = null): float
    {
        $followersScore = $this->logScore($snapshot->followers_count, 30);
        $viewsScore = $this->logScore($snapshot->views, 25);
        $engagementScore = $this->engagementScore($snapshot);
        $consistencyScore = $this->consistencyScore($snapshot->posts_count);
        $verifiedBonus = ($account?->is_verified ?? false) ? 5 : 0;

        $total = $followersScore + $viewsScore + $engagementScore + $consistencyScore + $verifiedBonus;

        return round(min(100, max(0, $total)), 2);
    }

    protected function logScore(int $value, float $weight): float
    {
        if ($value <= 0) {
            return 0;
        }

        // log10 normalizado: 10 -> ~33%, 1000 -> ~66%, 100000 -> ~100% do peso
        $normalized = min(1, log10($value + 1) / 5);

        return $normalized * $weight;
    }

    protected function engagementScore(ThreadsProfileSnapshot $snapshot): float
    {
        $views = max(1, $snapshot->views);
        $engagement = $snapshot->totalEngagement();
        $rate = $engagement / $views;

        // Taxa de engajamento típica: 0.5% a 10%
        $normalized = min(1, $rate / 0.10);

        return $normalized * 25;
    }

    protected function consistencyScore(int $postsCount): float
    {
        if ($postsCount <= 0) {
            return 0;
        }

        // 20+ posts = score máximo de consistência
        $normalized = min(1, $postsCount / 20);

        return $normalized * 15;
    }
}
