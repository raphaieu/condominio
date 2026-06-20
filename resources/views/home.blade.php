@extends('layouts.app')

@section('title', 'Condominio Threads — Seu imóvel simbólico no Threads')

@section('content')
<section class="max-w-6xl mx-auto px-4 py-16 lg:py-24">
    <div class="grid lg:grid-cols-2 gap-12 items-center">
        <div class="space-y-8">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-500/20 border border-teal-400/30 text-teal-300 text-xs font-medium uppercase tracking-wider">
                Experiência viral · Métricas reais · Resultado lúdico
            </div>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold leading-tight">
                Qual imóvel o <span class="text-gradient-gold">Threads</span> diria que você mora?
            </h1>

            <p class="text-lg text-slate-300 leading-relaxed max-w-xl">
                Conecte sua conta Threads, deixe a gente coletar suas métricas disponíveis via API
                e receba uma classificação simbólica: tipo de imóvel, bairro digital, endereço fictício
                e valor estimado — tudo no clima de condomínio tech.
            </p>

            <div class="flex flex-col sm:flex-row gap-4">
                @if ($threadsMock)
                    <x-button href="{{ route('auth.threads.callback', ['mock' => 1]) }}" variant="primary">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>
                        Entrar com Threads
                    </x-button>
                @else
                    <x-button href="{{ route('auth.threads.redirect') }}" variant="primary">
                        Entrar com Threads
                    </x-button>
                @endif

                <x-button href="{{ route('legal.terms') }}" variant="secondary">
                    Como funciona
                </x-button>
            </div>

            <p class="text-xs text-slate-500 max-w-md">
                Resultado simbólico e recreativo. Não representa avaliação financeira, social ou patrimonial real.
            </p>
        </div>

        <div class="relative">
            <div class="absolute -inset-4 bg-teal-500/10 rounded-3xl blur-2xl"></div>
            <x-card class="relative space-y-6">
                <div class="flex items-center gap-4">
                    <div class="h-16 w-16 rounded-full bg-gradient-to-br from-teal-400 to-sky-400 flex items-center justify-center text-2xl font-bold text-condo-dark">CT</div>
                    <div>
                        <p class="text-sm text-slate-400">Exemplo de resultado</p>
                        <p class="text-xl font-semibold">Casa de Condomínio</p>
                        <p class="text-teal-300">Parque das Endorfinas</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="rounded-xl bg-black/20 p-4 text-center">
                        <p class="text-3xl font-bold text-condo-gold">72</p>
                        <p class="text-xs text-slate-400 uppercase tracking-wide">Score</p>
                    </div>
                    <div class="rounded-xl bg-black/20 p-4 text-center">
                        <p class="text-lg font-semibold text-sky-300">R$ 680 mil</p>
                        <p class="text-xs text-slate-400 uppercase tracking-wide">Valor simbólico</p>
                    </div>
                </div>

                <p class="text-sm text-slate-400 italic border-l-2 border-teal-500/50 pl-4">
                    "Seu engajamento renderia um sobrado com piscina de stories e churrasqueira de replies."
                </p>
            </x-card>
        </div>
    </div>

    <div class="mt-24 grid sm:grid-cols-3 gap-6">
        <x-card>
            <div class="text-teal-400 text-2xl mb-3">01</div>
            <h3 class="font-semibold mb-2">Conecte o Threads</h3>
            <p class="text-sm text-slate-400">Autorize o acesso às métricas disponíveis na API oficial.</p>
        </x-card>
        <x-card>
            <div class="text-teal-400 text-2xl mb-3">02</div>
            <h3 class="font-semibold mb-2">Calculamos seu score</h3>
            <p class="text-sm text-slate-400">Seguidores, views, engajamento e consistência viram um índice de 0 a 100.</p>
        </x-card>
        <x-card>
            <div class="text-condo-gold text-2xl mb-3">03</div>
            <h3 class="font-semibold mb-2">Receba seu imóvel digital</h3>
            <p class="text-sm text-slate-400">Compartilhe sua página pública e libere a versão premium com Pix.</p>
        </x-card>
    </div>
</section>
@endsection
