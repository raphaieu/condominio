<?php

namespace App\Services\Billing;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MercadoPagoService
{
    protected function isMock(): bool
    {
        return (bool) config('services.mercado_pago.mock');
    }

    /**
     * @return array{
     *     provider_payment_id: string,
     *     pix_qr_code: string,
     *     pix_qr_code_base64: string,
     *     status: string,
     *     raw_payload: array<string, mixed>
     * }
     */
    public function createPixPayment(Order $order): array
    {
        if ($this->isMock()) {
            return $this->mockPixPayment($order);
        }

        $accessToken = config('services.mercado_pago.access_token');

        // TODO: Confirmar payload final conforme documentação Mercado Pago Pix.
        $payload = [
            'transaction_amount' => (float) $order->amount,
            'description' => 'Condominio Threads — Imagem Premium',
            'payment_method_id' => 'pix',
            'payer' => [
                'email' => $order->user?->email ?? 'cliente@condominio-threads.test',
            ],
            'external_reference' => $order->external_reference,
            'notification_url' => route('webhooks.mercado-pago'),
        ];

        $response = Http::withToken($accessToken)
            ->acceptJson()
            ->post('https://api.mercadopago.com/v1/payments', $payload);

        $response->throw();

        $data = $response->json();

        return [
            'provider_payment_id' => (string) ($data['id'] ?? ''),
            'pix_qr_code' => $data['point_of_interaction']['transaction_data']['qr_code'] ?? '',
            'pix_qr_code_base64' => $data['point_of_interaction']['transaction_data']['qr_code_base64'] ?? '',
            'status' => $data['status'] ?? 'pending',
            'raw_payload' => $data,
        ];
    }

    /**
     * @return array{
     *     provider_payment_id: string,
     *     pix_qr_code: string,
     *     pix_qr_code_base64: string,
     *     status: string,
     *     raw_payload: array<string, mixed>
     * }
     */
    protected function mockPixPayment(Order $order): array
    {
        $paymentId = 'mock_mp_'.Str::random(12);
        $copiaCola = '00020126580014br.gov.bcb.pix0136'.Str::random(32).'5204000053039865802BR5925CONDOMINIO THREADS6009SAO PAULO62070503***6304'.Str::upper(Str::random(4));

        // QR Code placeholder em base64 (1x1 PNG teal)
        $qrBase64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

        return [
            'provider_payment_id' => $paymentId,
            'pix_qr_code' => $copiaCola,
            'pix_qr_code_base64' => $qrBase64,
            'status' => 'pending',
            'raw_payload' => [
                'id' => $paymentId,
                'status' => 'pending',
                'mock' => true,
                'order_id' => $order->id,
            ],
        ];
    }
}
