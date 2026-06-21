<?php

namespace App\Services;

use App\Models\CondominiumResult;
use App\Models\ThreadsAccount;
use App\Models\ThreadsProfileSnapshot;
use App\Services\Scoring\ProfileScoringService;
use App\Services\Scoring\PropertyClassifier;
use App\Services\Threads\ThreadsMetricsCollector;

class CondominiumResultService
{
    public function __construct(
        protected ThreadsMetricsCollector $metricsCollector,
        protected ProfileScoringService $scoringService,
        protected PropertyClassifier $propertyClassifier,
    ) {}

    public function generateForAccount(ThreadsAccount $account, ?ThreadsProfileSnapshot $snapshot = null): CondominiumResult
    {
        $snapshot ??= $this->metricsCollector->collect($account);

        $score = $this->scoringService->calculate($snapshot, $account);
        $classification = $this->propertyClassifier->classify(
            $score,
            $account->username,
            $account,
            $snapshot,
        );

        return $account->condominiumResults()->create([
            'user_id' => $account->user_id,
            'score' => $score,
            'property_type' => $classification['property_type'],
            'neighborhood' => $classification['neighborhood'],
            'symbolic_address' => $classification['symbolic_address'],
            'social_class' => $classification['social_class'],
            'estimated_value' => $classification['estimated_value'],
            'description' => $classification['description'],
            'is_public' => true,
            'generated_at' => now(),
        ]);
    }

    public function recalculate(ThreadsAccount $account): CondominiumResult
    {
        $snapshot = $account->latestProfileSnapshot;

        if (! $snapshot) {
            return $this->generateForAccount($account);
        }

        return $this->generateForAccount($account, $snapshot);
    }
}
