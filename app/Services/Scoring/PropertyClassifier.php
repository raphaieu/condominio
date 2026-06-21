<?php

namespace App\Services\Scoring;

use App\Models\ThreadsAccount;
use App\Models\ThreadsProfileSnapshot;

class PropertyClassifier
{
    public function __construct(
        protected NeighborhoodCatalog $neighborhoodCatalog,
        protected ContentNicheDetector $nicheDetector,
    ) {}

    /**
     * @return array{
     *     property_type: string,
     *     neighborhood: string,
     *     symbolic_address: string,
     *     social_class: string,
     *     estimated_value: int,
     *     description: string,
     *     neighborhood_tag: string
     * }
     */
    public function classify(
        float $score,
        ?string $username = null,
        ?ThreadsAccount $account = null,
        ?ThreadsProfileSnapshot $snapshot = null,
    ): array {
        $neighborhood = $this->resolveNeighborhood($score, $account, $snapshot);
        $tier = $this->resolveTier($score);
        $handle = $username ? '@'.$username : 'morador';
        $neighborhoodName = $neighborhood['name'];
        $neighborhoodTag = $neighborhood['tag'] ?? 'Morador';

        return [
            'property_type' => $tier['property_type'],
            'neighborhood' => $neighborhoodName,
            'symbolic_address' => $this->buildAddress($neighborhoodName, $score, $handle),
            'social_class' => $tier['social_class'],
            'estimated_value' => $tier['estimated_value'],
            'description' => $this->buildDescription($tier, $neighborhoodName, $handle, $score),
            'neighborhood_tag' => $neighborhoodTag,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function resolveNeighborhood(
        float $score,
        ?ThreadsAccount $account,
        ?ThreadsProfileSnapshot $snapshot,
    ): array {
        if ($account && ! $account->isConnected()) {
            return $this->neighborhoodCatalog->disconnectedNeighborhood();
        }

        $postsCount = $snapshot?->posts_count;

        if ($postsCount === null && $account) {
            $postsCount = $account->posts()->count();
        }

        if ($postsCount !== null && $postsCount === 0) {
            return $this->neighborhoodCatalog->inactiveNeighborhood();
        }

        if ($account && $this->nicheDetector->isAdultNiche($account)) {
            return $this->neighborhoodCatalog->adultNicheNeighborhood();
        }

        return $this->neighborhoodCatalog->findByScore($score);
    }

    /**
     * @return array{property_type: string, social_class: string, estimated_value: int}
     */
    protected function resolveTier(float $score): array
    {
        return match (true) {
            $score <= 20 => [
                'property_type' => 'Barraco Simpático',
                'social_class' => 'Classe C Digital',
                'estimated_value' => random_int(45_000, 120_000),
            ],
            $score <= 40 => [
                'property_type' => 'Apartamento Funcional',
                'social_class' => 'Classe C+ Digital',
                'estimated_value' => random_int(180_000, 350_000),
            ],
            $score <= 60 => [
                'property_type' => 'Casa de Condomínio',
                'social_class' => 'Classe B Digital',
                'estimated_value' => random_int(420_000, 780_000),
            ],
            $score <= 75 => [
                'property_type' => 'Sobrado Premium',
                'social_class' => 'Classe B+ Digital',
                'estimated_value' => random_int(850_000, 1_500_000),
            ],
            $score <= 90 => [
                'property_type' => 'Mansão',
                'social_class' => 'Classe A Digital',
                'estimated_value' => random_int(1_800_000, 4_500_000),
            ],
            default => [
                'property_type' => 'Condomínio Próprio',
                'social_class' => 'Classe A+ Digital',
                'estimated_value' => random_int(5_000_000, 12_000_000),
            ],
        };
    }

    protected function buildAddress(string $neighborhood, float $score, string $handle): string
    {
        $block = chr(65 + ((int) $score % 26));
        $unit = str_pad((string) ((int) ($score * 13) % 999), 3, '0', STR_PAD_LEFT);

        return "{$neighborhood}, Bloco {$block}, Unidade {$unit} — {$handle}";
    }

    /**
     * @param  array{property_type: string, social_class: string, estimated_value: int}  $tier
     */
    protected function buildDescription(array $tier, string $neighborhood, string $handle, float $score): string
    {
        return sprintf(
            '%s chegou ao Condominio Threads com score %.0f e conquistou um %s no %s. '
            .'Sua classe social digital é %s, com valor simbólico estimado em R$ %s. '
            .'Tudo isso é brincadeira — uma leitura lúdica das suas métricas, não uma avaliação real.',
            $handle,
            $score,
            $tier['property_type'],
            $neighborhood,
            $tier['social_class'],
            number_format($tier['estimated_value'], 0, ',', '.')
        );
    }
}
