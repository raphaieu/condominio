<?php

namespace App\Services\Threads;

class ThreadsOAuthService
{
    public function __construct(
        protected ThreadsClient $client,
    ) {}

    public function isMockMode(): bool
    {
        return (bool) config('services.threads.mock');
    }

    public function getRedirectUrl(): string
    {
        if ($this->isMockMode()) {
            return route('auth.threads.callback', ['mock' => 1]);
        }

        $params = http_build_query([
            'client_id' => config('services.threads.app_id'),
            'redirect_uri' => config('services.threads.redirect_uri'),
            'scope' => 'threads_basic,threads_content_publish,threads_manage_insights,threads_read_replies',
            'response_type' => 'code',
        ]);

        return 'https://threads.net/oauth/authorize?'.$params;
    }

    /**
     * @return array{access_token: string, user_id: string, expires_in?: int}
     */
    public function exchangeCodeForToken(string $code): array
    {
        if ($this->isMockMode()) {
            return $this->client->mockTokenResponse();
        }

        $response = $this->client->http()->asForm()->post('https://graph.threads.net/oauth/access_token', [
            'client_id' => config('services.threads.app_id'),
            'client_secret' => config('services.threads.app_secret'),
            'grant_type' => 'authorization_code',
            'redirect_uri' => config('services.threads.redirect_uri'),
            'code' => $code,
        ]);

        $response->throw();

        return $response->json();
    }

    /**
     * @return array{access_token: string, user_id: string, username: string, name: string, threads_profile_picture_url?: string, threads_biography?: string, is_verified?: bool}
     */
    public function fetchProfile(string $accessToken): array
    {
        return $this->client->getMe($accessToken);
    }
}
