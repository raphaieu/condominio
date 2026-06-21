<?php

namespace App\Services\ImageGeneration;

use App\Models\CondominiumResult;
use App\Models\ThreadsAccount;
use App\Models\ThreadsProfileSnapshot;
use App\Services\Scoring\ContentNicheDetector;
use App\Services\Scoring\NeighborhoodCatalog;

class ImagePromptBuilder
{
    public function __construct(
        protected NeighborhoodCatalog $neighborhoodCatalog,
        protected ContentNicheDetector $nicheDetector,
    ) {}

    public function build(CondominiumResult $result, ?ThreadsAccount $account = null, ?ThreadsProfileSnapshot $snapshot = null): string
    {
        $result->loadMissing('threadsAccount');
        $account ??= $result->threadsAccount;
        $snapshot ??= $account?->latestProfileSnapshot;

        $handle = $account ? '@'.$account->username : 'morador';
        $name = $account?->name ?? $handle;
        $neighborhood = $result->neighborhood;
        $persona = $this->neighborhoodCatalog->tagFor($neighborhood);
        $style = $this->neighborhoodCatalog->promptStyleFor($neighborhood);

        if ($account) {
            $persona = $this->nicheDetector->dominantNiche($account, $persona);
        }

        $influence = $this->tierLabel($snapshot?->followers_count ?? 0, [
            [100_000, 'Muito alto'],
            [10_000, 'Alto'],
            [1_000, 'Médio'],
            [100, 'Baixo'],
        ], 'Muito baixo');

        $engagementRate = $snapshot && $snapshot->views > 0
            ? $snapshot->totalEngagement() / $snapshot->views
            : 0;

        $engagement = $this->tierLabel($engagementRate, [
            [0.08, 'Muito alto'],
            [0.04, 'Alto'],
            [0.015, 'Médio'],
            [0.005, 'Baixo'],
        ], 'Muito baixo');

        $frequency = $this->tierLabel($snapshot?->posts_count ?? 0, [
            [50, 'Muito alta'],
            [20, 'Alta'],
            [8, 'Média'],
            [3, 'Baixa'],
        ], 'Muito baixa');

        $niche = $persona;

        return <<<PROMPT
Crie uma imagem vertical 1024x1536 de uma residência fictícia chamada "{$result->property_type}" localizada no bairro simbólico "{$neighborhood}" dentro do "Condomínio Threads".

A casa deve representar visualmente este perfil digital:
- Morador: {$name} ({$handle})
- Score digital: {$result->score}
- Classe digital: {$result->social_class}
- Valor simbólico estimado: {$result->formattedEstimatedValue()}
- Bairro: {$neighborhood}
- Endereço simbólico: {$result->symbolic_address}
- Perfil do morador: {$persona}
- Nível de influência: {$influence}
- Nível de engajamento: {$engagement}
- Frequência de postagem: {$frequency}
- Nicho predominante: {$niche}

Direção de arte:
- fotografia arquitetônica realista;
- estética imobiliária premium brasileira;
- iluminação cinematográfica de fim de tarde;
- fachada principal bem enquadrada;
- sem pessoas;
- sem texto;
- sem placas;
- sem logos;
- sem marcas;
- sem interface de aplicativo;
- detalhes sutis inspirados em cultura digital e redes sociais;
- imagem bonita, compartilhável e com aparência de campanha visual premium.

Variação visual do bairro: {$style}.
PROMPT;
    }

    /**
     * @param  array<int, array{0: float|int, 1: string}>  $tiers
     */
    protected function tierLabel(float|int $value, array $tiers, string $fallback): string
    {
        foreach ($tiers as [$threshold, $label]) {
            if ($value >= $threshold) {
                return $label;
            }
        }

        return $fallback;
    }
}
