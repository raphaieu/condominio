<?php

namespace App\Http\Controllers;

use App\Models\ThreadsAccount;
use App\Services\CondominiumResultService;
use App\Support\SessionContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ResultController extends Controller
{
    public function __construct(
        protected CondominiumResultService $resultService,
    ) {}

    public function show(): View|RedirectResponse
    {
        $account = SessionContext::resolveAccount();
        $result = $account?->latestResult;

        if (! $account || ! $result) {
            return redirect('/')->with('error', 'Conecte sua conta Threads para ver seu resultado.');
        }

        return view('result.show', [
            'account' => $account,
            'result' => $result,
        ]);
    }

    public function recalculate(): RedirectResponse
    {
        $account = SessionContext::resolveAccount();

        if (! $account) {
            return redirect('/')->with('error', 'Conecte sua conta Threads primeiro.');
        }

        $this->resultService->recalculate($account);

        return redirect()->route('result.show')->with('success', 'Resultado recalculado com sucesso!');
    }

    public function public(string $username): View
    {
        $account = ThreadsAccount::query()
            ->where('username', $username)
            ->whereNull('disconnected_at')
            ->firstOrFail();

        $result = $account->condominiumResults()
            ->where('is_public', true)
            ->latest('generated_at')
            ->firstOrFail();

        return view('result.public', [
            'account' => $account,
            'result' => $result,
        ]);
    }
}
