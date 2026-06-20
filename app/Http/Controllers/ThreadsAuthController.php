<?php

namespace App\Http\Controllers;

use App\Models\ThreadsAccount;
use App\Models\User;
use App\Services\CondominiumResultService;
use App\Services\Threads\ThreadsOAuthService;
use App\Support\SessionContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ThreadsAuthController extends Controller
{
    public function __construct(
        protected ThreadsOAuthService $oauthService,
        protected CondominiumResultService $resultService,
    ) {}

    public function redirect(): RedirectResponse
    {
        return redirect()->away($this->oauthService->getRedirectUrl());
    }

    public function callback(Request $request): RedirectResponse
    {
        $isMock = $this->oauthService->isMockMode() && $request->boolean('mock');

        if (! $isMock && ! $request->filled('code')) {
            return redirect('/')->with('error', 'Autorização cancelada ou código ausente.');
        }

        try {
            if ($isMock) {
                $account = $this->handleMockCallback();
            } else {
                $account = $this->handleRealCallback($request->string('code')->toString());
            }

            SessionContext::setThreadsAccount($account);
            $this->resultService->generateForAccount($account);

            return redirect()->route('result.show');
        } catch (\Throwable $e) {
            Log::error('Threads OAuth callback failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect('/')->with('error', 'Não foi possível conectar com o Threads. Tente novamente.');
        }
    }

    protected function handleMockCallback(): ThreadsAccount
    {
        $profile = $this->oauthService->fetchProfile('mock_token');
        $tokenData = $this->oauthService->exchangeCodeForToken('mock_code');

        return $this->persistAccount($profile, $tokenData);
    }

    /**
     * @return array{access_token: string, user_id: string, expires_in?: int}
     */
    protected function handleRealCallback(string $code): ThreadsAccount
    {
        $tokenData = $this->oauthService->exchangeCodeForToken($code);
        $profile = $this->oauthService->fetchProfile($tokenData['access_token']);

        return $this->persistAccount($profile, $tokenData);
    }

    /**
     * @param  array<string, mixed>  $profile
     * @param  array{access_token: string, user_id: string, expires_in?: int}  $tokenData
     */
    protected function persistAccount(array $profile, array $tokenData): ThreadsAccount
    {
        $threadsUserId = (string) ($profile['id'] ?? $tokenData['user_id']);

        $account = ThreadsAccount::query()->firstOrNew([
            'threads_user_id' => $threadsUserId,
        ]);

        if (! $account->exists) {
            $user = User::query()->create([
                'name' => $profile['name'] ?? $profile['username'] ?? 'Morador',
            ]);
            $account->user_id = $user->id;
        }

        $account->fill([
            'username' => $profile['username'] ?? null,
            'name' => $profile['name'] ?? null,
            'avatar_url' => $profile['threads_profile_picture_url'] ?? null,
            'biography' => $profile['threads_biography'] ?? null,
            'is_verified' => (bool) ($profile['is_verified'] ?? false),
            'access_token' => $tokenData['access_token'],
            'token_expires_at' => isset($tokenData['expires_in'])
                ? now()->addSeconds((int) $tokenData['expires_in'])
                : null,
            'connected_at' => now(),
            'disconnected_at' => null,
        ]);

        $account->save();

        return $account;
    }
}
