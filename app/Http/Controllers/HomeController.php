<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $threadsMock = config('services.threads.mock');

        return view('home', [
            'threadsConnectUrl' => $threadsMock
                ? route('auth.threads.callback', ['mock' => 1])
                : route('auth.threads.redirect'),
            'premiumPrice' => (float) config('services.mercado_pago.premium_price', 9.90),
        ]);
    }
}
