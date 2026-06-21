<?php

namespace App\Services\ImageGeneration\Providers;

use App\Services\ImageGeneration\Contracts\ImageProviderInterface;
use Illuminate\Support\Facades\File;
use RuntimeException;

class MockImageProvider implements ImageProviderInterface
{
    public function generate(string $prompt): array
    {
        $fixture = public_path('images/premium-house.png');

        if (File::exists($fixture)) {
            $binary = File::get($fixture);
            $size = @getimagesize($fixture);

            return [
                'binary' => $binary,
                'mime' => $size['mime'] ?? 'image/png',
                'width' => $size[0] ?? 1024,
                'height' => $size[1] ?? 1536,
                'model' => 'mock-fixture',
            ];
        }

        $width = 1024;
        $height = 1536;

        if (! extension_loaded('gd')) {
            throw new RuntimeException('Extensão GD não disponível para gerar placeholder.');
        }

        $image = imagecreatetruecolor($width, $height);
        $background = imagecolorallocate($image, 30, 30, 40);
        $accent = imagecolorallocate($image, 232, 200, 105);
        imagefill($image, 0, 0, $background);
        imagefilledrectangle($image, 120, 400, 900, 1200, $accent);

        ob_start();
        imagepng($image);
        $binary = ob_get_clean();
        imagedestroy($image);

        return [
            'binary' => $binary ?: '',
            'mime' => 'image/png',
            'width' => $width,
            'height' => $height,
            'model' => 'mock-generated',
        ];
    }

    public function name(): string
    {
        return 'mock';
    }
}
