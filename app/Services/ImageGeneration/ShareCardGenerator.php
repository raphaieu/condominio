<?php

namespace App\Services\ImageGeneration;

use App\Models\CondominiumResult;
use App\Models\GeneratedAsset;
use App\Models\ImageGeneration;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ShareCardGenerator
{
    /**
     * @return array<int, GeneratedAsset>
     */
    public function generateFor(ImageGeneration $generation, GeneratedAsset $facade, CondominiumResult $result): array
    {
        if (! extension_loaded('gd')) {
            return [];
        }

        $binary = Storage::disk($facade->disk)->get($facade->path);

        if ($binary === null || $binary === '') {
            return [];
        }

        $assets = [];

        foreach ([
            GeneratedAsset::TYPE_STORY_CARD => [1080, 1920],
            GeneratedAsset::TYPE_SQUARE_CARD => [1080, 1080],
        ] as $type => [$width, $height]) {
            $cardBinary = $this->composeCard($binary, $result, $width, $height);
            $path = sprintf(
                'generated/%d/%d/%s.png',
                $generation->user_id,
                $generation->id,
                str_replace('_card', '', $type),
            );

            $disk = $facade->disk;
            Storage::disk($disk)->put($path, $cardBinary, 'public');

            $assets[] = GeneratedAsset::query()->create([
                'image_generation_id' => $generation->id,
                'user_id' => $generation->user_id,
                'condominium_result_id' => $generation->condominium_result_id,
                'type' => $type,
                'disk' => $disk,
                'path' => $path,
                'public_url' => Storage::disk($disk)->url($path),
                'width' => $width,
                'height' => $height,
                'metadata' => ['composed' => true],
            ]);
        }

        return $assets;
    }

    protected function composeCard(string $sourceBinary, CondominiumResult $result, int $width, int $height): string
    {
        $canvas = imagecreatetruecolor($width, $height);
        $dark = imagecolorallocate($canvas, 18, 18, 24);
        $gold = imagecolorallocate($canvas, 232, 200, 105);
        $white = imagecolorallocate($canvas, 245, 245, 245);
        $muted = imagecolorallocate($canvas, 180, 180, 190);
        $overlay = imagecolorallocate($canvas, 10, 10, 14);

        imagefill($canvas, 0, 0, $dark);

        $source = @imagecreatefromstring($sourceBinary);

        if ($source) {
            $srcW = imagesx($source);
            $srcH = imagesy($source);
            $imageHeight = (int) ($height * 0.62);
            imagecopyresampled($canvas, $source, 0, 0, 0, 0, $width, $imageHeight, $srcW, $srcH);
            imagedestroy($source);
        }

        $overlayY = (int) ($height * 0.58);
        imagefilledrectangle($canvas, 0, $overlayY, $width, $height, $overlay);

        $title = Str::limit($result->property_type, 40);
        $subtitle = Str::limit($result->neighborhood, 50);
        $score = 'Score '.number_format($result->score, 0).'/100';
        $value = $result->formattedEstimatedValue().' simbólicos';

        imagestring($canvas, 5, 48, $overlayY + 40, $title, $gold);
        imagestring($canvas, 4, 48, $overlayY + 80, $subtitle, $white);
        imagestring($canvas, 3, 48, $overlayY + 120, $score, $muted);
        imagestring($canvas, 3, 48, $overlayY + 145, $value, $muted);
        imagestring($canvas, 2, 48, $height - 48, 'Condominio Threads', $muted);

        ob_start();
        imagepng($canvas);
        $binary = ob_get_clean() ?: '';
        imagedestroy($canvas);

        return $binary;
    }
}
