@extends('layouts.app')

@section('title', 'Versão Premium — Condomínio Threads')

@section('content')
<section class="max-w-3xl mx-auto px-6 py-12">
    <div class="text-center mb-10">
        <span class="app-badge mb-4">Premium</span>
        <h1 class="text-3xl sm:text-4xl font-heading font-bold app-section-title">Libere sua casa personalizada</h1>
        <p class="text-condo-text-secondary mt-4 max-w-xl mx-auto">
            A versão premium gera uma imagem exclusiva do seu imóvel simbólico no Condomínio Threads,
            pronta para compartilhar nas redes.
        </p>
    </div>

    <x-card class="space-y-6">
        @if ($account)
            <div class="flex items-center gap-4 pb-6 border-b border-condo-border">
                @if ($account->avatar_url)
                    <img src="{{ $account->avatar_url }}" alt="" class="h-12 w-12 rounded-full object-cover ring-2 ring-condo-gold/30">
                @endif
                <div>
                    <p class="font-medium">{{ '@' . $account->username }}</p>
                    <p class="text-sm text-condo-text-secondary">{{ $result->property_type }} · {{ $result->neighborhood }}</p>
                </div>
            </div>
        @endif

        <ul class="space-y-3 text-sm text-condo-text-secondary">
            <li class="flex items-start gap-3">
                <span class="text-condo-gold mt-0.5">✓</span>
                Imagem personalizada do seu imóvel simbólico (em breve)
            </li>
            <li class="flex items-start gap-3">
                <span class="text-condo-gold mt-0.5">✓</span>
                Alta resolução para stories e feed
            </li>
            <li class="flex items-start gap-3">
                <span class="text-condo-gold mt-0.5">✓</span>
                Pagamento instantâneo via Pix
            </li>
        </ul>

        <div class="flex items-baseline gap-2 pt-4">
            <span class="text-3xl font-heading font-bold text-condo-gold">R$ {{ number_format($premiumPrice, 2, ',', '.') }}</span>
            <span class="text-condo-text-muted text-sm">pagamento único</span>
        </div>

        <form action="{{ route('checkout.pix') }}" method="POST">
            @csrf
            <x-button type="submit" variant="gold" class="w-full sm:w-auto">
                Pagar com Pix
            </x-button>
        </form>

        <p class="text-xs text-condo-text-muted">
            Ao pagar, você concorda com nossos <a href="{{ route('legal.terms') }}" class="text-condo-gold hover:underline">termos de serviço</a>.
        </p>
    </x-card>
</section>
@endsection
