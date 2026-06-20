@extends('layouts.app')

@section('title', 'Aguardando Pagamento — Condominio Threads')

@section('content')
<section class="max-w-lg mx-auto px-4 py-12" x-data="{
    copied: false,
    status: '{{ $order->status }}',
    poll() {
        if (this.status === 'paid') return;
        // Em produção, implementar polling ou websocket para status real.
    }
}">
    <x-card class="text-center space-y-6">
        <div>
            <p class="text-teal-400 text-sm uppercase tracking-wider mb-2">Pagamento Pix</p>
            <h1 class="text-2xl font-bold">
                @if ($order->isPaid())
                    Pagamento confirmado!
                @else
                    Aguardando confirmação
                @endif
            </h1>
            <p class="text-slate-400 text-sm mt-2">Pedido #{{ $order->id }} · R$ {{ number_format($order->amount, 2, ',', '.') }}</p>
        </div>

        @if ($order->isPaid())
            <div class="rounded-xl bg-teal-500/20 border border-teal-400/30 p-6">
                <p class="text-teal-200">Seu pagamento foi recebido. Em breve você receberá sua imagem premium!</p>
            </div>
            <x-button href="{{ route('result.show') }}" variant="primary">Voltar ao resultado</x-button>
        @elseif ($payment)
            @if ($payment->pix_qr_code_base64)
                <div class="mx-auto w-48 h-48 rounded-xl bg-white p-3">
                    <img
                        src="data:image/png;base64,{{ $payment->pix_qr_code_base64 }}"
                        alt="QR Code Pix"
                        class="w-full h-full object-contain"
                    >
                </div>
            @endif

            @if ($payment->pix_qr_code)
                <div class="text-left">
                    <p class="text-xs text-slate-500 uppercase mb-2">Pix copia e cola</p>
                    <div class="relative">
                        <textarea
                            readonly
                            rows="3"
                            class="w-full rounded-lg bg-black/30 border border-white/10 p-3 text-xs text-slate-300 font-mono resize-none"
                        >{{ $payment->pix_qr_code }}</textarea>
                        <button
                            type="button"
                            @click="navigator.clipboard.writeText(@js($payment->pix_qr_code)); copied = true; setTimeout(() => copied = false, 2000)"
                            class="mt-2 w-full py-2 rounded-lg bg-teal-500/20 hover:bg-teal-500/30 text-teal-300 text-sm transition"
                        >
                            <span x-text="copied ? 'Copiado!' : 'Copiar código Pix'"></span>
                        </button>
                    </div>
                </div>
            @endif

            @if ($mercadoPagoMock)
                <div class="rounded-lg bg-amber-500/10 border border-amber-400/20 p-4 text-left text-xs text-amber-200/80">
                    <p class="font-semibold mb-1">Modo mock ativo</p>
                    <p>Este é um QR Code e código Pix simulados para testes. Nenhum pagamento real será processado.</p>
                </div>
            @endif

            <div class="flex items-center justify-center gap-2 text-slate-400 text-sm">
                <svg class="animate-spin h-4 w-4 text-teal-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Aguardando confirmação do pagamento...
            </div>
        @endif
    </x-card>
</section>
@endsection
