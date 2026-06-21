@extends('layouts.app')

@section('title', '@'.$account->username.' no Condomínio Threads')

@section('meta_description', '@'.$account->username.' conquistou um '.$result->property_type.' no '.$result->neighborhood.' com score '.number_format($result->score, 0).' no Condomínio Threads.')

@section('meta_og')
    <meta property="og:title" content="{{ '@' . $account->username }} — {{ $result->property_type }}">
    <meta property="og:description" content="Score {{ number_format($result->score, 0) }} · {{ $result->neighborhood }} · {{ $result->formattedEstimatedValue() }} simbólicos">
    <meta property="og:type" content="profile">
    <meta property="og:url" content="{{ route('result.public', $account->username) }}">
    @if ($account->avatar_url)
        <meta property="og:image" content="{{ $account->avatar_url }}">
    @endif
    <meta name="twitter:card" content="summary">
@endsection

@section('content')
<section class="max-w-4xl mx-auto px-6 py-12" x-data="{ copied: false }">
    <div class="text-center mb-10">
        <p class="app-section-label mb-2">Condomínio Threads</p>
        <h1 class="text-3xl sm:text-4xl font-heading font-bold app-section-title">Carteira de morador(a) digital</h1>
    </div>

    <x-card class="relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-condo-gold/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 pointer-events-none"></div>

        <div class="relative flex flex-col items-center text-center pb-8 border-b border-condo-border">
            @if ($account->avatar_url)
                <img src="{{ $account->avatar_url }}" alt="{{ $account->username }}" class="h-24 w-24 rounded-full ring-4 ring-condo-gold/40 object-cover mb-4">
            @endif
            <p class="text-2xl font-heading font-bold">{{ '@' . $account->username }}</p>
            <p class="text-6xl font-heading font-black text-gradient-gold mt-4">{{ number_format($result->score, 0) }}</p>
            <p class="text-condo-text-muted text-sm">score no condomínio</p>
        </div>

        <div class="relative grid sm:grid-cols-2 gap-6 py-8 text-center sm:text-left">
            <div class="sm:text-center">
                <p class="text-xs text-condo-text-muted uppercase">Imóvel</p>
                <p class="text-lg font-semibold text-condo-gold-bright">{{ $result->property_type }}</p>
            </div>
            <div class="sm:text-center">
                <p class="text-xs text-condo-text-muted uppercase">Bairro</p>
                <p class="text-lg font-semibold">{{ $result->neighborhood }}</p>
            </div>
            <div class="sm:col-span-2 text-center">
                <p class="text-xs text-condo-text-muted uppercase">Endereço simbólico</p>
                <p class="text-condo-text-secondary mt-1">{{ $result->symbolic_address }}</p>
            </div>
            <div class="sm:text-center">
                <p class="text-xs text-condo-text-muted uppercase">Classe digital</p>
                <p class="font-medium">{{ $result->social_class }}</p>
            </div>
            <div class="sm:text-center">
                <p class="text-xs text-condo-text-muted uppercase">Valor simbólico</p>
                <p class="text-xl font-heading font-bold text-condo-gold">{{ $result->formattedEstimatedValue() }}</p>
            </div>
        </div>

        @if ($result->description)
            <p class="text-center text-condo-text-secondary text-sm italic px-4 pb-6">{{ $result->description }}</p>
        @endif

        <div class="text-center space-y-4">
            <p class="text-xs text-condo-text-muted">Classificação simbólica e recreativa · Condomínio Threads</p>

            <button
                type="button"
                @click="navigator.clipboard.writeText(window.location.href); copied = true; setTimeout(() => copied = false, 2000)"
                class="app-btn app-btn-secondary text-sm"
            >
                <span x-text="copied ? 'Link copiado!' : 'Copiar link para compartilhar'"></span>
            </button>

            <div class="pt-4">
                <x-button href="{{ route('home') }}" variant="primary">
                    Descobrir meu imóvel
                </x-button>
            </div>
        </div>
    </x-card>
</section>
@endsection
