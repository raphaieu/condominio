<?php

namespace App\Providers;

use App\Services\ImageGeneration\Contracts\ImageProviderInterface;
use App\Services\ImageGeneration\Providers\MockImageProvider;
use App\Services\ImageGeneration\Providers\OpenAIImageProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ImageProviderInterface::class, function () {
            $provider = config('services.premium_image.provider', 'mock');

            return match ($provider) {
                'openai' => new OpenAIImageProvider,
                default => new MockImageProvider,
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $appUrl = config('app.url');

        if (is_string($appUrl) && str_starts_with($appUrl, 'https://')) {
            URL::forceScheme('https');
        }
    }
}
