<?php

namespace App\Support;

use App\Models\CondominiumResult;
use App\Models\ThreadsAccount;

class SessionContext
{
    public const THREADS_ACCOUNT_KEY = 'threads_account_id';

    public static function setThreadsAccount(ThreadsAccount $account): void
    {
        session([self::THREADS_ACCOUNT_KEY => $account->id]);
    }

    public static function currentThreadsAccount(): ?ThreadsAccount
    {
        $id = session(self::THREADS_ACCOUNT_KEY);

        if (! $id) {
            return null;
        }

        return ThreadsAccount::query()
            ->whereNull('disconnected_at')
            ->find($id);
    }

    public static function currentResult(): ?CondominiumResult
    {
        $account = self::currentThreadsAccount();

        return $account?->latestResult;
    }

    public static function forget(): void
    {
        session()->forget(self::THREADS_ACCOUNT_KEY);
    }
}
