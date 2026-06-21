<?php

namespace App\Services\Scoring;

class NeighborhoodCatalog
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        return config('neighborhoods.neighborhoods', []);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findById(string $id): ?array
    {
        foreach ($this->all() as $neighborhood) {
            if ($neighborhood['id'] === $id) {
                return $neighborhood;
            }
        }

        return null;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findByName(string $name): ?array
    {
        foreach ($this->all() as $neighborhood) {
            if ($neighborhood['name'] === $name) {
                return $neighborhood;
            }
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    public function findByScore(float $score): array
    {
        $scoreNeighborhoods = array_values(array_filter(
            $this->all(),
            fn (array $n) => ($n['score_min'] ?? null) !== null
                && ! ($n['inactive'] ?? false)
                && ! ($n['disconnected'] ?? false)
                && ! isset($n['niche'])
        ));

        usort($scoreNeighborhoods, fn (array $a, array $b) => ($b['score_min'] ?? 0) <=> ($a['score_min'] ?? 0));

        foreach ($scoreNeighborhoods as $neighborhood) {
            $min = (float) ($neighborhood['score_min'] ?? 0);
            $max = (float) ($neighborhood['score_max'] ?? 100);

            if ($score >= $min && $score <= $max) {
                return $neighborhood;
            }
        }

        return $this->findById('iniciantes') ?? $scoreNeighborhoods[array_key_last($scoreNeighborhoods)];
    }

    /**
     * @return array<string, mixed>
     */
    public function inactiveNeighborhood(): array
    {
        return $this->findById('esquecido') ?? ['name' => 'Bairro Esquecido', 'tag' => 'Inativos'];
    }

    /**
     * @return array<string, mixed>
     */
    public function disconnectedNeighborhood(): array
    {
        return $this->findById('cemiterio') ?? ['name' => 'Cemitério das Contas', 'tag' => 'R.I.P.'];
    }

    /**
     * @return array<string, mixed>
     */
    public function adultNicheNeighborhood(): array
    {
        return $this->findById('sem_filtro') ?? ['name' => 'Bairro Sem Filtro', 'tag' => 'Sem Filtro'];
    }

    public function promptStyleFor(string $neighborhoodName): string
    {
        $neighborhood = $this->findByName($neighborhoodName);

        return $neighborhood['prompt_style'] ?? 'fotografia arquitetônica realista, estética imobiliária premium brasileira';
    }

    public function tagFor(string $neighborhoodName): string
    {
        $neighborhood = $this->findByName($neighborhoodName);

        return $neighborhood['tag'] ?? 'Morador';
    }
}
