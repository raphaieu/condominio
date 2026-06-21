<?php

namespace App\Services\Scoring;

use App\Models\ThreadsAccount;
use Illuminate\Support\Collection;

class ContentNicheDetector
{
    public function __construct(
        protected NeighborhoodCatalog $catalog,
    ) {}

    public function isAdultNiche(ThreadsAccount $account): bool
    {
        $posts = $account->posts()->whereNotNull('text')->pluck('text');

        if ($posts->isEmpty()) {
            return false;
        }

        $threshold = (float) config('neighborhoods.niche_detector.threshold', 0.15);
        $keywords = config('neighborhoods.niche_detector.keywords', []);

        $matchingPosts = $posts->filter(function (string $text) use ($keywords) {
            $normalized = mb_strtolower($text);

            foreach ($keywords as $keyword) {
                if (str_contains($normalized, mb_strtolower($keyword))) {
                    return true;
                }
            }

            return false;
        });

        return ($matchingPosts->count() / $posts->count()) >= $threshold;
    }

    public function dominantNiche(ThreadsAccount $account, string $fallbackTag): string
    {
        if ($this->isAdultNiche($account)) {
            return $this->catalog->adultNicheNeighborhood()['tag'];
        }

        return $fallbackTag;
    }

    /**
     * @param  Collection<int, string>  $texts
     */
    public function matchRatio(Collection $texts): float
    {
        if ($texts->isEmpty()) {
            return 0.0;
        }

        $keywords = config('neighborhoods.niche_detector.keywords', []);
        $matching = $texts->filter(function (string $text) use ($keywords) {
            $normalized = mb_strtolower($text);

            foreach ($keywords as $keyword) {
                if (str_contains($normalized, mb_strtolower($keyword))) {
                    return true;
                }
            }

            return false;
        });

        return $matching->count() / $texts->count();
    }
}
