<?php

namespace App\Support;

use App\Models\CondominiumResult;
use App\Models\ThreadsAccount;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Cookie;

class SessionContext
{
    public const THREADS_ACCOUNT_KEY = 'threads_account_id';

    public const REMEMBER_COOKIE = 'condo_threads_account';

    public static function rememberAccount(ThreadsAccount $account): void
    {
        session([self::THREADS_ACCOUNT_KEY => $account->id]);

        Cookie::queue(
            self::REMEMBER_COOKIE,
            encrypt((string) $account->id),
            self::rememberMinutes(),
            '/',
            null,
            app()->isProduction(),
            true,
            false,
            'lax',
        );
    }

    public static function setThreadsAccount(ThreadsAccount $account): void
    {
        self::rememberAccount($account);
    }

    public static function resolveAccount(): ?ThreadsAccount
    {
        $account = self::accountFromSession();

        if ($account) {
            return $account;
        }

        return self::accountFromCookie();
    }

    public static function currentThreadsAccount(): ?ThreadsAccount
    {
        return self::resolveAccount();
    }

    public static function currentResult(): ?CondominiumResult
    {
        return self::resolveAccount()?->latestResult;
    }

    public static function forget(): void
    {
        session()->forget(self::THREADS_ACCOUNT_KEY);
        Cookie::queue(Cookie::forget(self::REMEMBER_COOKIE));
    }

    protected static function accountFromSession(): ?ThreadsAccount
    {
        $id = session(self::THREADS_ACCOUNT_KEY);

        if (! $id) {
            return null;
        }

        return self::findActiveAccount((int) $id);
    }

    protected static function accountFromCookie(): ?ThreadsAccount
    {
        $encrypted = Cookie::get(self::REMEMBER_COOKIE);

        if (! is_string($encrypted) || $encrypted === '') {
            return null;
        }

        try {
            $id = (int) decrypt($encrypted);
        } catch (DecryptException) {
            self::forget();

            return null;
        }

        $account = self::findActiveAccount($id);

        if ($account) {
            session([self::THREADS_ACCOUNT_KEY => $account->id]);
        }

        return $account;
    }

    protected static function findActiveAccount(int $id): ?ThreadsAccount
    {
        return ThreadsAccount::query()
            ->whereNull('disconnected_at')
            ->find($id);
    }

    protected static function rememberMinutes(): int
    {
        return (int) config('services.threads.remember_days', 30) * 24 * 60;
    }
}
