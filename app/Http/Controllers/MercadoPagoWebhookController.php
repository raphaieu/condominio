<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MercadoPagoWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        // TODO: Validar assinatura x-signature do Mercado Pago com MERCADO_PAGO_WEBHOOK_SECRET.
        Log::info('Mercado Pago webhook received', $request->all());

        $paymentId = $request->input('data.id')
            ?? $request->input('id')
            ?? data_get($request->all(), 'data.id');

        if (! $paymentId) {
            return response()->json(['received' => true]);
        }

        $payment = Payment::query()
            ->where('provider_payment_id', (string) $paymentId)
            ->first();

        if (! $payment) {
            return response()->json(['received' => true]);
        }

        $status = $request->input('action') === 'payment.updated'
            ? ($request->input('data.status') ?? 'pending')
            : ($request->input('status') ?? 'pending');

        // Em mock, permitir simular aprovação via query param ?mock_approve=1
        if (config('services.mercado_pago.mock') && $request->boolean('mock_approve')) {
            $status = 'approved';
        }

        if (in_array($status, ['approved', 'accredited'], true)) {
            $payment->update([
                'status' => 'approved',
                'raw_payload' => array_merge($payment->raw_payload ?? [], $request->all()),
            ]);

            $payment->order->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);
        }

        return response()->json(['received' => true]);
    }
}
