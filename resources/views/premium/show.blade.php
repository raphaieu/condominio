@extends('layouts.app')

@section('title', 'Sua Casa com IA — Condomínio Threads')

@section('meta_og')
    @if ($facadeAsset ?? null)
        <meta property="og:title" content="Minha casa no Condomínio Threads">
        <meta property="og:description" content="{{ $result->property_type }} no {{ $result->neighborhood }}">
        <meta property="og:image" content="{{ $facadeAsset->url() }}">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
    @endif
@endsection

@section('content')
<script>
    window.premiumRoutes = {
        generate: @json(route('premium.image.generate')),
        status: @json(route('premium.image.status')),
        retry: @json(route('premium.image.retry')),
    };
</script>

<section class="max-w-3xl mx-auto px-6 py-12" x-data="premiumImagePanel(@js($uiState))">
    <div class="text-center mb-10">
        <span class="app-badge mb-4">Sua casa</span>
        <h1 class="text-3xl sm:text-4xl font-heading font-bold app-section-title">Gerar minha casa com IA</h1>
        <p class="text-condo-text-secondary mt-4 max-w-xl mx-auto">
            Uma imagem exclusiva do seu imóvel simbólico no Condomínio Threads, pronta para compartilhar.
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

        {{-- Estado: concluído --}}
        <template x-if="uiState === 'completed'">
            <div class="space-y-6">
                <div class="rounded-2xl overflow-hidden border border-condo-border bg-black/30">
                    <img
                        :src="assetUrl || @js($facadeAsset?->url())"
                        alt="Sua casa no Condomínio Threads"
                        class="w-full h-auto object-cover"
                    >
                </div>

                <div class="rounded-xl bg-black/30 border border-condo-border p-5">
                    <p class="text-condo-text-secondary text-sm leading-relaxed" x-text="houseDescription || @js($houseDescription)"></p>
                </div>

                <div class="flex flex-col sm:flex-row flex-wrap gap-4">
                    <button
                        type="button"
                        @click="copyShare()"
                        class="inline-flex items-center justify-center px-6 py-3 rounded-full font-medium bg-condo-gold/15 hover:bg-condo-gold/25 text-condo-gold-bright border border-condo-border transition"
                    >
                        <span x-text="copied ? 'Copiado!' : 'Copiar texto para compartilhar'"></span>
                    </button>

                    @if (($shareAssets ?? collect())->isNotEmpty())
                        @foreach ($shareAssets as $card)
                            <x-button href="{{ $card->url() }}" variant="secondary" target="_blank" rel="noopener">
                                Baixar {{ $card->type === 'story_card' ? 'Story' : 'Feed' }}
                            </x-button>
                        @endforeach
                    @endif

                    @if ($account->username)
                        <x-button href="{{ route('result.public', $account->username) }}" variant="secondary">
                            Ver página pública
                        </x-button>
                    @endif
                </div>
            </div>
        </template>

        {{-- Estado: gerando --}}
        <template x-if="uiState === 'generating'">
            <div class="text-center py-8 space-y-4">
                <svg class="animate-spin h-10 w-10 text-condo-gold mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <p class="text-lg font-medium">Gerando sua casa...</p>
                <p class="text-sm text-condo-text-secondary">Isso pode levar até um minuto. Não feche esta página.</p>
                <div class="space-y-2 text-left max-w-sm mx-auto pt-4">
                    <p class="text-xs text-condo-text-muted flex items-center gap-2"><span class="text-condo-gold">✓</span> Montando o prompt do seu perfil</p>
                    <p class="text-xs text-condo-text-muted flex items-center gap-2"><span class="text-condo-gold animate-pulse">⏳</span> Criando a fachada com IA</p>
                    <p class="text-xs text-condo-text-muted flex items-center gap-2"><span class="opacity-40">○</span> Preparando para compartilhar</p>
                </div>
            </div>
        </template>

        {{-- Estado: falhou --}}
        <template x-if="uiState === 'failed'">
            <div class="text-center py-6 space-y-4">
                <p class="text-red-300" x-text="errorMessage || 'Não foi possível gerar sua casa agora.'"></p>
                <x-button type="button" variant="gold" @click="retry()" ::disabled="loading">
                    Tentar novamente
                </x-button>
            </div>
        </template>

        {{-- Estado: pronto para gerar --}}
        <template x-if="uiState === 'ready'">
            <div class="space-y-6">
                <ul class="space-y-3 text-sm text-condo-text-secondary">
                    <li class="flex items-start gap-3">
                        <span class="text-condo-gold mt-0.5">✓</span>
                        Imagem personalizada do seu imóvel simbólico
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-condo-gold mt-0.5">✓</span>
                        Alta resolução para stories e feed
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-condo-gold mt-0.5">✓</span>
                        Pronta para compartilhar
                    </li>
                </ul>

                @if ($testMode)
                    <div class="app-disclaimer text-left">
                        <p class="font-semibold mb-1 text-condo-gold">Modo teste ativo</p>
                        <p>Geração liberada sem pagamento para validação.</p>
                    </div>
                @endif

                <x-button type="button" variant="gold" class="w-full sm:w-auto" @click="generate()" ::disabled="loading">
                    <span x-text="loading ? 'Iniciando...' : 'Gerar minha casa com IA'"></span>
                </x-button>
            </div>
        </template>

        {{-- Estado: bloqueado (precisa pagar) --}}
        <template x-if="uiState === 'locked'">
            <div class="space-y-6">
                <ul class="space-y-3 text-sm text-condo-text-secondary">
                    <li class="flex items-start gap-3">
                        <span class="text-condo-gold mt-0.5">✓</span>
                        Imagem personalizada do seu imóvel simbólico
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-condo-gold mt-0.5">✓</span>
                        Alta resolução para stories e feed
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-condo-gold mt-0.5">✓</span>
                        Pagamento instantâneo via Pix (simulação em teste)
                    </li>
                </ul>

                <div class="flex items-baseline gap-2 pt-2">
                    <span class="text-3xl font-heading font-bold text-condo-gold">R$ {{ number_format($premiumPrice, 2, ',', '.') }}</span>
                    <span class="text-condo-text-muted text-sm">pagamento único</span>
                </div>

                <form action="{{ route('checkout.pix') }}" method="POST">
                    @csrf
                    <x-button type="submit" variant="gold" class="w-full sm:w-auto">
                        Pagar com Pix para gerar minha casa
                    </x-button>
                </form>
            </div>
        </template>

        <div class="app-disclaimer">
            Classificação simbólica e recreativa. Não representa avaliação financeira, social ou patrimonial real.
        </div>
    </x-card>
</section>
@endsection
