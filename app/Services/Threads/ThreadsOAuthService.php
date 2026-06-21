<?php

namespace App\Services\Threads;

use App\Exceptions\ThreadsApiException;
use App\Support\ThreadsSafeLogger;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ThreadsOAuthService
{
    public const OAUTH_STATE_SESSION_KEY = 'threads_oauth_state';

    public const SCOPES = 'threads_basic,threads_manage_insights';

    public function __construct(
        protected ThreadsClient $client,
    ) {}

    public function isMockMode(): bool
    {
        return (bool) config('services.threads.mock');
    }

    public function getAuthorizationUrl(): string
    {
        if ($this->isMockMode()) {
            return route('auth.threads.callback', ['mock' => 1]);
        }

        $state = Str::random(40);
        session([self::OAUTH_STATE_SESSION_KEY => $state]);

        $params = http_build_query([
            'client_id' => config('services.threads.app_id'),
            'redirect_uri' => config('services.threads.redirect_uri'),
            'scope' => self::SCOPES,
            'response_type' => 'code',
            'state' => $state,
        ]);

        return 'https://threads.net/oauth/authorize?'.$params;
    }

    public function validateState(?string $state): bool
    {
        if ($this->isMockMode()) {
            return true;
        }

        $expected = session()->pull(self::OAUTH_STATE_SESSION_KEY);

        return is_string($expected)
            && is_string($state)
            && hash_equals($expected, $state);
    }

    /**
     * @return array{access_token: string, user_id: string, expires_in?: int}
     */
    public function exchangeCodeForShortLivedToken(string $code): array
    {
        if ($this->isMockMode()) {
            return $this->client->mockTokenResponse();
        }

        $response = $this->client->http()->asForm()->timeout(30)->post('https://graph.threads.net/oauth/access_token', [
            'client_id' => config('services.threads.app_id'),
            'client_secret' => config('services.threads.app_secret'),
            'grant_type' => 'authorization_code',
            'redirect_uri' => config('services.threads.redirect_uri'),
            'code' => $code,
        ]);

        if ($response->failed()) {
            $summary = ThreadsSafeLogger::summarizeMetaError($response);

            Log::error('Threads short-lived token exchange failed', $summary);

            throw new ThreadsApiException(
                'Falha ao trocar código por token.',
                $response->status(),
                is_string($summary['fbtrace_id'] ?? null) ? $summary['fbtrace_id'] : null,
                $summary,
            );
        }

        return $response->json();
    }

    /**
     * @return array{access_token: string, token_type?: string, expires_in?: int}
     */
    public function exchangeForLongLivedToken(string $shortLivedToken): array
    {
        if ($this->isMockMode()) {
            return [
                'access_token' => $shortLivedToken,
                'token_type' => 'bearer',
                'expires_in' => 5184000,
            ];
        }

        $response = $this->client->http()->timeout(30)->get('https://graph.threads.net/access_token', [
            'grant_type' => 'th_exchange_token',
            'client_secret' => config('services.threads.app_secret'),
            'access_token' => $shortLivedToken,
        ]);

        if ($response->failed()) {
            $summary = ThreadsSafeLogger::summarizeMetaError($response);

            Log::warning('Threads long-lived token exchange failed, using short-lived token', $summary);

            throw new ThreadsApiException(
                'Falha ao obter token de longa duração.',
                $response->status(),
                is_string($summary['fbtrace_id'] ?? null) ? $summary['fbtrace_id'] : null,
                $summary,
            );
        }

        return $response->json();
    }

    /**
     * @return array{access_token: string, token_type?: string, expires_in?: int}
     */
    public function refreshLongLivedToken(string $accessToken): array
    {
        if ($this->isMockMode()) {
            return [
                'access_token' => $accessToken,
                'token_type' => 'bearer',
                'expires_in' => 5184000,
            ];
        }

        $response = $this->client->http()->timeout(30)->get('https://graph.threads.net/refresh_access_token', [
            'grant_type' => 'th_refresh_token',
            'access_token' => $accessToken,
        ]);

        if ($response->failed()) {
            $summary = ThreadsSafeLogger::summarizeMetaError($response);

            Log::warning('Threads token refresh failed', $summary);

            throw new ThreadsApiException(
                'Falha ao renovar token de acesso.',
                $response->status(),
                is_string($summary['fbtrace_id'] ?? null) ? $summary['fbtrace_id'] : null,
                $summary,
            );
        }

        return $response->json();
    }

    /**
     * @return array{access_token: string, user_id: string, expires_in?: int}
     */
    public function resolveAccessToken(string $code): array
    {
        $shortLived = $this->exchangeCodeForShortLivedToken($code);

        try {
            $longLived = $this->exchangeForLongLivedToken($shortLived['access_token']);

            return [
                'access_token' => $longLived['access_token'],
                'user_id' => (string) ($shortLived['user_id'] ?? ''),
                'expires_in' => $longLived['expires_in'] ?? $shortLived['expires_in'] ?? null,
            ];
        } catch (ThreadsApiException) {
            return $shortLived;
        }
    }

    /**
     * @return array{access_token: string, user_id: string, username: string, name: string, threads_profile_picture_url?: string, threads_biography?: string, is_verified?: bool}
     */
    public function fetchProfile(string $accessToken): array
    {
        return $this->client->getMe($accessToken);
    }
}
