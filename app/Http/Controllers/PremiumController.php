<?php

namespace App\Http\Controllers;

use App\Support\SessionContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PremiumController extends Controller
{
    public function show(): View|RedirectResponse
    {
        $result = SessionContext::currentResult();

        if (! $result) {
            return redirect('/')->with('error', 'Gere seu resultado antes de acessar o premium.');
        }

        return view('premium.show', [
            'result' => $result,
            'account' => SessionContext::currentThreadsAccount(),
            'premiumPrice' => config('services.mercado_pago.premium_price', 9.90),
        ]);
    }
}
