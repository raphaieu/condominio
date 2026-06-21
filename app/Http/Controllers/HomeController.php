<?php

namespace App\Http\Controllers;

use App\Support\SessionContext;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $threadsMock = config('services.threads.mock');
        $account = SessionContext::resolveAccount();
        $alreadyConnected = $account !== null && $account->hasValidToken();

        return view('home', [
            'threadsConnectUrl' => $alreadyConnected
                ? route('result.show')
                : ($threadsMock
                    ? route('auth.threads.callback', ['mock' => 1])
                    : route('auth.threads.redirect')),
            'alreadyConnected' => $alreadyConnected,
            'premiumPrice' => (float) config('services.mercado_pago.premium_price', 9.90),
        ]);
    }
}
