<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\Billing\MercadoPagoService;
use App\Services\ImageGeneration\ImageGenerationService;
use App\Services\Premium\PremiumAccessService;
use App\Support\SessionContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        protected MercadoPagoService $mercadoPagoService,
        protected PremiumAccessService $premiumAccessService,
        protected ImageGenerationService $imageGenerationService,
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
            'canConfirmMock' => $this->canConfirmMockPayment(),
        ]);
    }

    public function confirmMockPayment(Order $order): RedirectResponse
    {
        if (! $this->canConfirmMockPayment()) {
            abort(403);
        }

        $account = SessionContext::currentThreadsAccount();
        $result = SessionContext::currentResult();

        if (! $account || ! $result || $order->user_id !== $account->user_id) {
            return redirect('/')->with('error', 'Pedido inválido.');
        }

        if ($order->isPaid()) {
            return redirect()->route('premium.show', ['generating' => 1]);
        }

        $payment = $order->latestPayment();

        if ($payment) {
            $payment->update(['status' => 'approved']);
        }

        $order->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $this->premiumAccessService->unlockFromPayment($order);
        $this->imageGenerationService->requestGeneration($account->user, $result);

        return redirect()->route('premium.show', ['generating' => 1])
            ->with('success', 'Pagamento confirmado! Sua casa está sendo gerada.');
    }

    protected function canConfirmMockPayment(): bool
    {
        return config('services.mercado_pago.mock')
            || app()->environment('local');
    }
}
