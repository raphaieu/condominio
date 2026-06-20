@extends('layouts.app')

@section('title', '@'.$account->username.' no Condominio Threads')

@section('content')
<section class="max-w-4xl mx-auto px-4 py-12" x-data="{ copied: false }">
    <div class="text-center mb-10">
        <p class="text-teal-400 text-sm uppercase tracking-widest mb-2">Condominio Threads</p>
        <h1 class="text-3xl sm:text-4xl font-bold">Carteira de morador(a) digital</h1>
    </div>

    <x-card class="relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-teal-500/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>

        <div class="relative flex flex-col items-center text-center pb-8 border-b border-white/10">
            @if ($account->avatar_url)
                <img src="{{ $account->avatar_url }}" alt="{{ $account->username }}" class="h-24 w-24 rounded-full ring-4 ring-condo-gold/40 object-cover mb-4">
            @endif
            <p class="text-2xl font-bold">@{{ $account->username }}</p>
            <p class="text-6xl font-black text-gradient-gold mt-4">{{ number_format($result->score, 0) }}</p>
            <p class="text-slate-400 text-sm">score no condomínio</p>
        </div>

        <div class="relative grid sm:grid-cols-2 gap-6 py-8 text-center sm:text-left">
            <div class="sm:text-center">
                <p class="text-xs text-slate-500 uppercase">Imóvel</p>
                <p class="text-lg font-semibold text-teal-300">{{ $result->property_type }}</p>
            </div>
            <div class="sm:text-center">
                <p class="text-xs text-slate-500 uppercase">Bairro</p>
                <p class="text-lg font-semibold">{{ $result->neighborhood }}</p>
            </div>
            <div class="sm:col-span-2 text-center">
                <p class="text-xs text-slate-500 uppercase">Endereço simbólico</p>
                <p class="text-slate-300 mt-1">{{ $result->symbolic_address }}</p>
            </div>
            <div class="sm:text-center">
                <p class="text-xs text-slate-500 uppercase">Classe digital</p>
                <p class="font-medium">{{ $result->social_class }}</p>
            </div>
            <div class="sm:text-center">
                <p class="text-xs text-slate-500 uppercase">Valor simbólico</p>
                <p class="text-xl font-bold text-condo-gold">{{ $result->formattedEstimatedValue() }}</p>
            </div>
        </div>

        @if ($result->description)
            <p class="text-center text-slate-400 text-sm italic px-4 pb-6">{{ $result->description }}</p>
        @endif

        <div class="text-center space-y-4">
            <p class="text-xs text-slate-500">Classificação simbólica e recreativa · Condominio Threads</p>

            <button
                type="button"
                @click="navigator.clipboard.writeText(window.location.href); copied = true; setTimeout(() => copied = false, 2000)"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-sm transition"
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
