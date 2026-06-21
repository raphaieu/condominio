<?php

namespace App\Http\Middleware;

use App\Support\SessionContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureThreadsSession
{
    public function handle(Request $request, Closure $next): Response
    {
        $account = SessionContext::currentThreadsAccount();
        $result = SessionContext::currentResult();

        if (! $account || ! $result) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Conecte sua conta e gere um resultado primeiro.'], 403);
            }

            return redirect('/')->with('error', 'Conecte sua conta e gere um resultado primeiro.');
        }

        $request->attributes->set('threads_account', $account);
        $request->attributes->set('condominium_result', $result);

        return $next($request);
    }
}
