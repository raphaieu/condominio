<?php

namespace App\Support;

use App\Models\CondominiumResult;
use App\Models\GeneratedAsset;
use App\Models\ThreadsAccount;

class PublicResultShare
{
    /**
     * @return array{
     *     title: string,
     *     description: string,
     *     image: ?string,
     *     image_alt: ?string,
     *     image_width: ?int,
     *     image_height: ?int,
     *     twitter_card: string
     * }
     */
    public static function meta(
        ThreadsAccount $account,
        CondominiumResult $result,
        ?GeneratedAsset $facadeAsset = null,
    ): array {
        $handle = '@'.$account->username;
        $score = number_format($result->score, 0);

        $title = $facadeAsset
            ? "{$handle} — {$result->property_type} no {$result->neighborhood}"
            : "{$handle} — Score {$score} no Condomínio Threads";

        $description = self::buildDescription($account, $result, $facadeAsset !== null);

        return [
            'title' => $title,
            'description' => $description,
            'image' => self::absoluteUrl($facadeAsset?->url() ?? $account->avatar_url),
            'image_alt' => $facadeAsset
                ? "Casa de {$handle} no {$result->neighborhood}"
                : ($account->name ? "Perfil de {$handle}" : null),
            'image_width' => $facadeAsset?->width,
            'image_height' => $facadeAsset?->height,
            'twitter_card' => $facadeAsset ? 'summary_large_image' : 'summary',
        ];
    }

    protected static function buildDescription(
        ThreadsAccount $account,
        CondominiumResult $result,
        bool $hasHouseImage,
    ): string {
        $handle = '@'.$account->username;
        $score = number_format($result->score, 0);

        $parts = [
            "{$handle} · Score {$score}/100",
            "{$result->property_type} · {$result->neighborhood}",
            $result->symbolic_address,
            "{$result->social_class} · {$result->formattedEstimatedValue()}",
        ];

        if ($hasHouseImage) {
            $parts[] = 'Casa gerada com IA no Condomínio Threads';
        }

        $parts[] = 'Descubra o seu em '.config('app.name');

        return implode(' · ', array_filter($parts));
    }

    protected static function absoluteUrl(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        return url($url);
    }
}
