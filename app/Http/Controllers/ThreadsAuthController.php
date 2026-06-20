<?php

namespace App\Http\Controllers;

use App\Exceptions\ThreadsApiException;
use App\Models\ThreadsAccount;
use App\Models\User;
use App\Services\CondominiumResultService;
use App\Services\Threads\ThreadsOAuthService;
use App\Support\SessionContext;
use App\Support\ThreadsSafeLogger;
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
        return redirect()->away($this->oauthService->getAuthorizationUrl());
    }

    public function callback(Request $request): RedirectResponse
    {
        $isMock = $this->oauthService->isMockMode() && $request->boolean('mock');

        if (! $isMock) {
            if ($request->filled('error')) {
                Log::info('Threads OAuth denied by user', [
                    'error' => $request->string('error')->toString(),
                    'error_reason' => $request->string('error_reason')->toString() ?: null,
                ]);

                return redirect('/')->with('error', 'Autorização cancelada. Você pode tentar novamente quando quiser.');
            }

            if (! $request->filled('code')) {
                return redirect('/')->with('error', 'Autorização cancelada ou código ausente.');
            }

            if (! $this->oauthService->validateState($request->string('state')->toString() ?: null)) {
                Log::warning('Threads OAuth state mismatch');

                return redirect('/')->with('error', 'Sessão de login expirada ou inválida. Tente conectar novamente.');
            }
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
        } catch (ThreadsApiException $e) {
            Log::error('Threads OAuth callback API error', [
                'status' => $e->statusCode,
                'request_id' => $e->requestId,
                'error' => $e->errorSummary,
            ]);

            return redirect('/')->with('error', $e->userMessage());
        } catch (\Throwable $e) {
            Log::error('Threads OAuth callback failed', [
                'message' => $e->getMessage(),
                'exception' => $e::class,
            ]);

            return redirect('/')->with('error', 'Não foi possível conectar com o Threads. Tente novamente.');
        }
    }

    protected function handleMockCallback(): ThreadsAccount
    {
        $tokenData = $this->oauthService->resolveAccessToken('mock_code');
        $profile = $this->oauthService->fetchProfile($tokenData['access_token']);

        return $this->persistAccount($profile, $tokenData);
    }

    protected function handleRealCallback(string $code): ThreadsAccount
    {
        $tokenData = $this->oauthService->resolveAccessToken($code);
        $profile = $this->oauthService->fetchProfile($tokenData['access_token']);

        return $this->persistAccount($profile, $tokenData);
    }

    /**
     * @param  array<string, mixed>  $profile
     * @param  array{access_token: string, user_id: string, expires_in?: int|null}  $tokenData
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

        Log::info('Threads account connected', [
            'threads_user_id' => $threadsUserId,
            'username' => $account->username,
            'access_token' => ThreadsSafeLogger::maskToken($tokenData['access_token']),
        ]);

        return $account;
    }
}
