<?php

namespace App\Services\Premium;

use App\Models\CondominiumResult;
use App\Models\Order;
use App\Models\PremiumUnlock;
use App\Models\ThreadsAccount;
use App\Models\User;

class PremiumAccessService
{
    public function isTestModeEnabled(): bool
    {
        return (bool) config('services.premium_image.test_mode', false);
    }

    public function canGenerate(User $user, CondominiumResult $result): bool
    {
        if ($this->isTestModeEnabled()) {
            return true;
        }

        return $this->hasActiveUnlock($user, $result);
    }

    public function hasActiveUnlock(User $user, CondominiumResult $result): bool
    {
        return PremiumUnlock::query()
            ->where('user_id', $user->id)
            ->where('condominium_result_id', $result->id)
            ->where('status', PremiumUnlock::STATUS_ACTIVE)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->exists();
    }

    public function activeUnlock(User $user, CondominiumResult $result): ?PremiumUnlock
    {
        return PremiumUnlock::query()
            ->where('user_id', $user->id)
            ->where('condominium_result_id', $result->id)
            ->where('status', PremiumUnlock::STATUS_ACTIVE)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->latest('unlocked_at')
            ->first();
    }

    /**
     * @param  array<string, mixed>  $metadata
     */
    public function unlockManually(
        User $user,
        CondominiumResult $result,
        string $source = PremiumUnlock::SOURCE_MANUAL,
        array $metadata = [],
    ): PremiumUnlock {
        return $this->createUnlock($user, $result, $source, $metadata);
    }

    public function unlockFromPayment(Order $order): PremiumUnlock
    {
        $order->loadMissing('condominiumResult');

        $existing = PremiumUnlock::query()
            ->where('user_id', $order->user_id)
            ->where('condominium_result_id', $order->condominium_result_id)
            ->where('source', PremiumUnlock::SOURCE_FUTURE_PAYMENT)
            ->where('status', PremiumUnlock::STATUS_ACTIVE)
            ->where('metadata->order_id', $order->id)
            ->first();

        if ($existing) {
            return $existing;
        }

        return $this->createUnlock(
            $order->user,
            $order->condominiumResult,
            PremiumUnlock::SOURCE_FUTURE_PAYMENT,
            ['order_id' => $order->id],
        );
    }

    public function resolveUserByHandleOrId(string $handleOrId): ?User
    {
        $handleOrId = ltrim($handleOrId, '@');

        if (is_numeric($handleOrId)) {
            return User::query()->find((int) $handleOrId);
        }

        $account = ThreadsAccount::query()
            ->where('username', $handleOrId)
            ->whereNull('disconnected_at')
            ->first();

        return $account?->user;
    }

    /**
     * @param  array<string, mixed>  $metadata
     */
    protected function createUnlock(
        User $user,
        CondominiumResult $result,
        string $source,
        array $metadata = [],
    ): PremiumUnlock {
        PremiumUnlock::query()
            ->where('user_id', $user->id)
            ->where('condominium_result_id', $result->id)
            ->where('status', PremiumUnlock::STATUS_ACTIVE)
            ->update(['status' => PremiumUnlock::STATUS_REVOKED]);

        return PremiumUnlock::query()->create([
            'user_id' => $user->id,
            'condominium_result_id' => $result->id,
            'source' => $source,
            'status' => PremiumUnlock::STATUS_ACTIVE,
            'unlocked_at' => now(),
            'metadata' => $metadata ?: null,
        ]);
    }
}
