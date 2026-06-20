<?php

namespace App\Http\Controllers;

use App\Models\ThreadsAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ThreadsWebhookController extends Controller
{
    public function deauthorize(Request $request): JsonResponse
    {
        // TODO: Validar assinatura do webhook Meta (X-Hub-Signature-256).
        Log::info('Meta deauthorize webhook received', $request->all());

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
}
