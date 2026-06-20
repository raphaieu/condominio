@extends('layouts.app')

@section('title', 'Versão Premium — Condominio Threads')

@section('content')
<section class="max-w-3xl mx-auto px-4 py-12">
    <div class="text-center mb-10">
        <span class="inline-block px-3 py-1 rounded-full bg-condo-gold/20 text-condo-gold text-xs font-semibold uppercase tracking-wider mb-4">Premium</span>
        <h1 class="text-3xl sm:text-4xl font-bold">Libere sua casa personalizada</h1>
        <p class="text-slate-400 mt-4 max-w-xl mx-auto">
            A versão premium gera uma imagem exclusiva do seu imóvel simbólico no Condominio Threads,
            pronta para compartilhar nas redes.
        </p>
    </div>

    <x-card class="space-y-6">
        @if ($account)
            <div class="flex items-center gap-4 pb-6 border-b border-white/10">
                @if ($account->avatar_url)
                    <img src="{{ $account->avatar_url }}" alt="" class="h-12 w-12 rounded-full object-cover">
                @endif
                <div>
                    <p class="font-medium">@{{ $account->username }}</p>
                    <p class="text-sm text-slate-400">{{ $result->property_type }} · {{ $result->neighborhood }}</p>
                </div>
            </div>
        @endif

        <ul class="space-y-3 text-sm text-slate-300">
            <li class="flex items-start gap-3">
                <span class="text-teal-400 mt-0.5">✓</span>
                Imagem personalizada do seu imóvel simbólico (em breve)
            </li>
            <li class="flex items-start gap-3">
                <span class="text-teal-400 mt-0.5">✓</span>
                Alta resolução para stories e feed
            </li>
            <li class="flex items-start gap-3">
                <span class="text-teal-400 mt-0.5">✓</span>
                Pagamento instantâneo via Pix
            </li>
        </ul>

        <div class="flex items-baseline gap-2 pt-4">
            <span class="text-3xl font-bold text-condo-gold">R$ {{ number_format($premiumPrice, 2, ',', '.') }}</span>
            <span class="text-slate-500 text-sm">pagamento único</span>
        </div>

        <form action="{{ route('checkout.pix') }}" method="POST">
            @csrf
            <x-button type="submit" variant="gold" class="w-full sm:w-auto">
                Pagar com Pix
            </x-button>
        </form>

        <p class="text-xs text-slate-500">
            Ao pagar, você concorda com nossos <a href="{{ route('legal.terms') }}" class="text-teal-400 hover:underline">termos de serviço</a>.
        </p>
    </x-card>
</section>
@endsection
