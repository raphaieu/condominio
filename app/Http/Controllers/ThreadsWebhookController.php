<?php

namespace App\Http\Controllers;

use App\Models\ThreadsAccount;
use App\Support\ThreadsSafeLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ThreadsWebhookController extends Controller
{
    public function deauthorize(Request $request): JsonResponse
    {
        // TODO: Validar signed_request do webhook Meta.
        Log::info('Meta deauthorize webhook received', [
            'payload' => ThreadsSafeLogger::sanitizePayload($request->all()),
        ]);

        $threadsUserId = $request->input('user_id')
            ?? $request->input('threads_user_id')
            ?? data_get($request->all(), 'entry.0.id');

        if ($threadsUserId) {
            ThreadsAccount::query()
                ->where('threads_user_id', $threadsUserId)
                ->whereNull('disconnected_at')
                ->update([
                    'disconnected_at' => now(),
                    'access_token' => null,
                ]);
        }

        return response()->json(['success' => true]);
    }

    public function dataDeletion(Request $request): JsonResponse
    {
        // TODO: Validar signed_request do webhook Meta.
        Log::info('Meta data deletion webhook received', [
            'payload' => ThreadsSafeLogger::sanitizePayload($request->all()),
        ]);

        $confirmationCode = Str::upper(Str::random(12));

        $statusUrl = route('legal.data-deletion.status', [
            'confirmationCode' => $confirmationCode,
        ]);

        return response()->json([
            'url' => $statusUrl,
            'confirmation_code' => $confirmationCode,
        ]);
    }
}
