@extends('layouts.app')

@section('title', 'Seu Resultado — Condomínio Threads')

@section('content')
<section class="max-w-4xl mx-auto px-6 py-12">
    <x-card class="overflow-hidden">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6 pb-8 border-b border-condo-border">
            @if ($account->avatar_url)
                <img src="{{ $account->avatar_url }}" alt="{{ $account->username }}" class="h-20 w-20 rounded-full ring-4 ring-condo-gold/30 object-cover">
            @else
                <div class="h-20 w-20 rounded-full bg-condo-gold/20 flex items-center justify-center text-2xl font-bold text-condo-gold-bright font-heading">
                    {{ strtoupper(substr($account->username ?? 'CT', 0, 2)) }}
                </div>
            @endif

            <div class="flex-1">
                <p class="app-section-label mb-1">Morador(a) do condomínio</p>
                <h1 class="text-2xl font-heading font-bold">{{ '@' . $account->username }}</h1>
                @if ($account->name)
                    <p class="text-condo-text-secondary">{{ $account->name }}</p>
                @endif
            </div>

            <div class="text-center sm:text-right">
                <p class="text-5xl font-heading font-bold text-gradient-gold">{{ number_format($result->score, 0) }}</p>
                <p class="text-xs text-condo-text-muted uppercase tracking-wider">Score digital</p>
            </div>
        </div>

        <div class="grid sm:grid-cols-2 gap-6 py-8">
            <div>
                <p class="text-xs text-condo-text-muted uppercase tracking-wider mb-1">Tipo de imóvel</p>
                <p class="text-xl font-semibold text-condo-gold-bright">{{ $result->property_type }}</p>
            </div>
            <div>
                <p class="text-xs text-condo-text-muted uppercase tracking-wider mb-1">Bairro</p>
                <p class="text-xl font-semibold">{{ $result->neighborhood }}</p>
            </div>
            <div class="sm:col-span-2">
                <p class="text-xs text-condo-text-muted uppercase tracking-wider mb-1">Endereço simbólico</p>
                <p class="text-condo-text-secondary">{{ $result->symbolic_address }}</p>
            </div>
            <div>
                <p class="text-xs text-condo-text-muted uppercase tracking-wider mb-1">Classe social digital</p>
                <p class="font-medium">{{ $result->social_class }}</p>
            </div>
            <div>
                <p class="text-xs text-condo-text-muted uppercase tracking-wider mb-1">Valor estimado (simbólico)</p>
                <p class="text-2xl font-heading font-bold text-condo-gold">{{ $result->formattedEstimatedValue() }}</p>
            </div>
        </div>

        @if ($result->description)
            <div class="rounded-xl bg-black/30 border border-condo-border p-5 mb-8">
                <p class="text-condo-text-secondary text-sm leading-relaxed italic">{{ $result->description }}</p>
            </div>
        @endif

        <div class="app-disclaimer mb-8">
            Resultado simbólico e recreativo. Não representa avaliação financeira, social ou patrimonial real.
        </div>

        <div class="flex flex-col sm:flex-row flex-wrap gap-4">
            @if ($account->username)
                <x-button href="{{ route('result.public', $account->username) }}" variant="secondary">
                    Ver página pública
                </x-button>
            @endif

            <x-button href="{{ route('premium.show') }}" variant="gold">
                Gerar minha casa com IA
            </x-button>

            <form action="{{ route('result.recalculate') }}" method="POST">
                @csrf
                <x-button type="submit" variant="secondary">
                    Recalcular score
                </x-button>
            </form>
        </div>
    </x-card>
</section>
@endsection
