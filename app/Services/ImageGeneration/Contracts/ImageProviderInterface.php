<?php

namespace App\Services\ImageGeneration\Contracts;

interface ImageProviderInterface
{
    /**
     * @return array{
     *     binary: string,
     *     mime: string,
     *     width: int,
     *     height: int,
     *     model: ?string
     * }
     */
    public function generate(string $prompt): array;

    public function name(): string;
}
