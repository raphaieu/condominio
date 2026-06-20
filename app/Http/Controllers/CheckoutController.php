<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\Billing\MercadoPagoService;
use App\Support\SessionContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        protected MercadoPagoService $mercadoPagoService,
    ) {}

    public function createPix(Request $request): RedirectResponse
    {
        $result = SessionContext::currentResult();
        $account = SessionContext::currentThreadsAccount();

        if (! $result || ! $account) {
            return redirect('/')->with('error', 'Conecte sua conta e gere um resultado primeiro.');
        }

        $amount = (float) config('services.mercado_pago.premium_price', 9.90);

        $order = Order::query()->create([
            'user_id' => $account->user_id,
            'condominium_result_id' => $result->id,
            'type' => 'premium_image',
            'status' => 'pending',
            'amount' => $amount,
            'external_reference' => 'ct_'.Str::uuid(),
        ]);

        $pixData = $this->mercadoPagoService->createPixPayment($order);

        Payment::query()->create([
            'order_id' => $order->id,
            'provider' => 'mercado_pago',
            'provider_payment_id' => $pixData['provider_payment_id'],
            'status' => $pixData['status'],
            'pix_qr_code' => $pixData['pix_qr_code'],
            'pix_qr_code_base64' => $pixData['pix_qr_code_base64'],
            'raw_payload' => $pixData['raw_payload'],
        ]);

        return redirect()->route('checkout.status', $order);
    }

    public function status(Order $order): View
    {
        $payment = $order->latestPayment();

        return view('checkout.status', [
            'order' => $order,
            'payment' => $payment,
            'mercadoPagoMock' => config('services.mercado_pago.mock'),
        ]);
    }
}
