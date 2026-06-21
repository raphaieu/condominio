<?php

namespace App\Http\Controllers;

use App\Models\ImageGeneration;
use App\Services\ImageGeneration\HouseDescriptionBuilder;
use App\Services\ImageGeneration\ImageGenerationService;
use App\Support\SessionContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class PremiumImageController extends Controller
{
    public function __construct(
        protected ImageGenerationService $imageGenerationService,
        protected HouseDescriptionBuilder $houseDescriptionBuilder,
    ) {}

    public function generate(Request $request): JsonResponse
    {
        $account = SessionContext::currentThreadsAccount();
        $result = SessionContext::currentResult();

        if (! $account || ! $result) {
            return response()->json(['message' => 'Sessão inválida.'], 403);
        }

        try {
            $generation = $this->imageGenerationService->requestGeneration($account->user, $result);
        } catch (RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 403);
        }

        return response()->json($this->buildPayload($account->user, $result, $generation));
    }

    public function status(Request $request): JsonResponse
    {
        $account = SessionContext::currentThreadsAccount();
        $result = SessionContext::currentResult();

        if (! $account || ! $result) {
            return response()->json(['message' => 'Sessão inválida.'], 403);
        }

        return response()->json($this->buildPayload($account->user, $result));
    }

    public function retry(Request $request): JsonResponse
    {
        $account = SessionContext::currentThreadsAccount();
        $result = SessionContext::currentResult();

        if (! $account || ! $result) {
            return response()->json(['message' => 'Sessão inválida.'], 403);
        }

        $generation = $result->latestImageGeneration;

        if (! $generation) {
            return response()->json(['message' => 'Nenhuma geração encontrada.'], 404);
        }

        try {
            $generation = $this->imageGenerationService->retry($generation);
        } catch (RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        return response()->json($this->buildPayload($account->user, $result, $generation));
    }

    /**
     * @return array<string, mixed>
     */
    protected function buildPayload($user, $result, ?ImageGeneration $generation = null): array
    {
        $status = $this->imageGenerationService->getStatus($user, $result);
        $generation ??= $result->latestImageGeneration;

        $houseDescription = null;
        $shareText = null;

        if ($status['status'] === ImageGeneration::STATUS_COMPLETED) {
            $houseDescription = $this->houseDescriptionBuilder->build($result);
            $shareText = $this->houseDescriptionBuilder->shareText($result, $status['asset']['url'] ?? null);
        }

        return array_merge($status, [
            'house_description' => $houseDescription,
            'share_text' => $shareText,
        ]);
    }
}
