<?php

namespace App\Http\Controllers;

use App\Models\GeneratedAsset;
use App\Models\ImageGeneration;
use App\Services\ImageGeneration\HouseDescriptionBuilder;
use App\Services\ImageGeneration\ImageGenerationService;
use App\Services\Premium\PremiumAccessService;
use App\Support\SessionContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PremiumController extends Controller
{
    public function __construct(
        protected PremiumAccessService $premiumAccessService,
        protected ImageGenerationService $imageGenerationService,
        protected HouseDescriptionBuilder $houseDescriptionBuilder,
    ) {}

    public function show(): View|RedirectResponse
    {
        $result = SessionContext::currentResult();
        $account = SessionContext::currentThreadsAccount();

        if (! $result || ! $account) {
            return redirect('/')->with('error', 'Gere seu resultado antes de acessar o premium.');
        }

        $user = $account->user;
        $canGenerate = $this->premiumAccessService->canGenerate($user, $result);
        $unlock = $this->premiumAccessService->activeUnlock($user, $result);
        $generation = $result->latestImageGeneration;
        $statusPayload = $this->imageGenerationService->getStatus($user, $result);

        $facadeAsset = null;
        $shareAssets = collect();
        $houseDescription = null;
        $shareText = null;

        if ($generation?->status === ImageGeneration::STATUS_COMPLETED) {
            $allAssets = GeneratedAsset::query()
                ->where('image_generation_id', $generation->id)
                ->get();

            $facadeAsset = $allAssets->firstWhere('type', GeneratedAsset::TYPE_FACADE);
            $shareAssets = $allAssets->whereIn('type', [
                GeneratedAsset::TYPE_STORY_CARD,
                GeneratedAsset::TYPE_SQUARE_CARD,
            ]);

            $houseDescription = $this->houseDescriptionBuilder->build($result);
            $shareText = $this->houseDescriptionBuilder->shareText(
                $result,
                $facadeAsset?->url(),
            );
        }

        $uiState = $this->resolveUiState($canGenerate, $generation);

        return view('premium.show', [
            'result' => $result,
            'account' => $account,
            'premiumPrice' => config('services.mercado_pago.premium_price', 9.90),
            'canGenerate' => $canGenerate,
            'unlock' => $unlock,
            'generation' => $generation,
            'facadeAsset' => $facadeAsset,
            'shareAssets' => $shareAssets,
            'houseDescription' => $houseDescription,
            'shareText' => $shareText,
            'uiState' => $uiState,
            'testMode' => $this->premiumAccessService->isTestModeEnabled(),
            'statusPayload' => $statusPayload,
        ]);
    }

    protected function resolveUiState(bool $canGenerate, ?ImageGeneration $generation): string
    {
        if ($generation?->status === ImageGeneration::STATUS_COMPLETED) {
            return 'completed';
        }

        if (in_array($generation?->status, [ImageGeneration::STATUS_PENDING, ImageGeneration::STATUS_PROCESSING], true)) {
            return 'generating';
        }

        if ($generation?->status === ImageGeneration::STATUS_FAILED) {
            return 'failed';
        }

        if ($canGenerate) {
            return 'ready';
        }

        return 'locked';
    }
}
