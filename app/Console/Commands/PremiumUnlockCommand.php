<?php

namespace App\Console\Commands;

use App\Models\CondominiumResult;
use App\Models\PremiumUnlock;
use App\Services\Premium\PremiumAccessService;
use Illuminate\Console\Command;

class PremiumUnlockCommand extends Command
{
    protected $signature = 'premium:unlock {handle_or_user_id : Username do Threads (com ou sem @) ou ID do usuário}';

    protected $description = 'Libera geração de imagem premium manualmente para um usuário';

    public function handle(PremiumAccessService $premiumAccessService): int
    {
        $user = $premiumAccessService->resolveUserByHandleOrId($this->argument('handle_or_user_id'));

        if (! $user) {
            $this->error('Usuário não encontrado.');

            return self::FAILURE;
        }

        $result = CondominiumResult::query()
            ->where('user_id', $user->id)
            ->latest('generated_at')
            ->first();

        if (! $result) {
            $this->error('Nenhum resultado encontrado para este usuário.');

            return self::FAILURE;
        }

        $unlock = $premiumAccessService->unlockManually(
            $user,
            $result,
            PremiumUnlock::SOURCE_MANUAL,
        );

        $account = $user->activeThreadsAccount;
        $handle = $account ? '@'.$account->username : "user #{$user->id}";

        $this->info("Premium liberado para {$handle} (resultado #{$result->id}, unlock #{$unlock->id}).");

        return self::SUCCESS;
    }
}
