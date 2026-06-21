<?php

namespace App\Services\ImageGeneration;

use App\Models\CondominiumResult;
use App\Services\Scoring\NeighborhoodCatalog;

class HouseDescriptionBuilder
{
    public function __construct(
        protected NeighborhoodCatalog $neighborhoodCatalog,
    ) {}

    public function build(CondominiumResult $result): string
    {
        $tag = $this->neighborhoodCatalog->tagFor($result->neighborhood);
        $style = $this->neighborhoodCatalog->promptStyleFor($result->neighborhood);

        return sprintf(
            'Sua casa simbólica no %s reflete um perfil %s no Condomínio Threads. '
            .'O imóvel tipo %s combina com a energia do bairro — %s. '
            .'Tudo isso é uma leitura lúdica das suas métricas, feita para compartilhar.',
            $result->neighborhood,
            mb_strtolower($tag),
            $result->property_type,
            mb_strtolower(rtrim($style, '.'))
        );
    }

    public function shareText(CondominiumResult $result, ?string $imageUrl = null): string
    {
        $lines = [
            '🏘️ Minha casa no Condomínio Threads!',
            '',
            "{$result->property_type} no {$result->neighborhood}",
            "📍 {$result->symbolic_address}",
            "⭐ Score: ".number_format($result->score, 0).'/100',
            "💰 Valor simbólico: {$result->formattedEstimatedValue()}",
        ];

        if ($imageUrl) {
            $lines[] = '';
            $lines[] = "🖼️ {$imageUrl}";
        }

        $lines[] = '';
        $lines[] = 'Descubra a sua em '.config('app.url');

        return implode("\n", $lines);
    }
}
