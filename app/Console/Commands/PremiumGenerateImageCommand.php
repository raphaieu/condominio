<?php

namespace App\Console\Commands;

use App\Models\CondominiumResult;
use App\Services\ImageGeneration\ImageGenerationService;
use App\Services\Premium\PremiumAccessService;
use Illuminate\Console\Command;

class PremiumGenerateImageCommand extends Command
{
    protected $signature = 'premium:generate-image {handle_or_user_id : Username do Threads (com ou sem @) ou ID do usuário} {--force : Força nova geração mesmo se já existir uma concluída}';

    protected $description = 'Dispara geração manual de imagem premium para debug';

    public function handle(
        PremiumAccessService $premiumAccessService,
        ImageGenerationService $imageGenerationService,
    ): int {
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

        if (! $premiumAccessService->canGenerate($user, $result)) {
            $premiumAccessService->unlockManually($user, $result);
            $this->warn('Unlock manual criado para permitir a geração.');
        }

        $generation = $this->option('force')
            ? $imageGenerationService->forceNewGeneration($user, $result)
            : $imageGenerationService->requestGeneration($user, $result);

        $this->info("Geração #{$generation->id} enfileirada (status: {$generation->status}).");

        return self::SUCCESS;
    }
}
