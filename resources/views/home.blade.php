@extends('layouts.landing')

@section('title', 'Condomínio Threads — Descubra sua Casa')

@section('meta_description', 'Conecte sua conta do Threads e descubra em qual bairro do condomínio você mora. Análise de métricas reais via API, classificação simbólica e imagem premium com IA.')

@php
    $priceReais = (int) floor($premiumPrice);
    $priceCentavos = sprintf('%02d', (int) round(($premiumPrice - floor($premiumPrice)) * 100));
@endphp

@section('content')
<header id="header" class="header">
    <div class="header-inner">
        <a href="{{ route('home') }}" class="logo" aria-label="Condomínio Threads">
            <svg class="logo-icon" viewBox="0 0 24 24" width="28" height="28" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M16.87 10.27c-.06-.03-.13-.05-.19-.08a5.65 5.65 0 0 0-2.06-4.66 5.65 5.65 0 0 0-5.04-1.11A5.65 5.65 0 0 0 5.5 8.69a5.65 5.65 0 0 0 1.11 5.04 5.65 5.65 0 0 0 4.27 2.08c.26 0 .52-.02.78-.05a3.44 3.44 0 0 1-1.06 1.78 3.44 3.44 0 0 1-2.03.87c-.42.03-.77.37-.77.79v.01c0 .46.39.82.85.79a5.22 5.22 0 0 0 3.35-1.5 5.22 5.22 0 0 0 1.55-3.28c.01-.1.02-.2.02-.3v-.28c.8-.22 1.51-.65 2.08-1.24a4.83 4.83 0 0 0 1.22-2.89v-.04a.44.44 0 0 0-.44-.44.44.44 0 0 0-.44.44 3.94 3.94 0 0 1-1 2.36 3.94 3.94 0 0 1-2.2 1.14v-.27a3.6 3.6 0 0 0-.71-2.14 3.6 3.6 0 0 0-1.84-1.34c.02-.24.06-.47.13-.7a4.78 4.78 0 0 1 1.56-2.3 4.78 4.78 0 0 1 2.6-1.02c.97-.07 1.92.19 2.72.73a4.78 4.78 0 0 1 1.81 2.08c.08.17.12.27.15.37" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                <circle cx="12" cy="12" r="10.5" stroke="currentColor" stroke-width="1.5"/>
            </svg>
            <span class="logo-text">Condomínio<span class="logo-accent">Threads</span></span>
        </a>
        <a href="{{ $threadsConnectUrl }}" class="btn btn-header">
            Descubra sua Casa
        </a>
    </div>
</header>

<section id="hero" class="hero">
    <div class="hero-bg">
        <div class="hero-orb hero-orb-1"></div>
        <div class="hero-orb hero-orb-2"></div>
        <div class="hero-orb hero-orb-3"></div>
        <div class="hero-grid"></div>
    </div>
    <div class="hero-content">
        <div class="hero-badge animate-on-scroll">
            <span class="hero-badge-dot"></span>
            Cada perfil tem seu lugar
        </div>
        <h1 class="hero-title animate-on-scroll">
            Descubra sua Casa no<br>
            <span class="text-gradient">Condomínio Threads</span>
        </h1>
        <p class="hero-subtitle animate-on-scroll">
            Conecte sua conta, analise suas métricas reais via API oficial e receba uma classificação simbólica:
            tipo de imóvel, bairro digital, endereço fictício e score — tudo no clima de condomínio tech.
        </p>
        <div class="hero-actions animate-on-scroll">
            <a href="{{ $threadsConnectUrl }}" class="btn btn-primary btn-glow">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5" fill="none"/>
                    <path d="M12 7c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5zm0 8.5c-1.93 0-3.5-1.57-3.5-3.5S10.07 8.5 12 8.5s3.5 1.57 3.5 3.5-1.57 3.5-3.5 3.5z" fill="currentColor"/>
                </svg>
                Conectar com Threads
            </a>
            <a href="#bairros" class="btn btn-ghost">
                Ver os Bairros
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="M8 3v10M8 13l4-4M8 13L4 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        </div>
        <p class="hero-disclaimer animate-on-scroll">
            Resultado simbólico e recreativo. Não representa avaliação financeira, social ou patrimonial real.
        </p>
        <div class="hero-stats animate-on-scroll">
            <div class="hero-stat">
                <span class="hero-stat-number" data-count="50000">0</span>
                <span class="hero-stat-label">análises feitas</span>
            </div>
            <div class="hero-stat-divider"></div>
            <div class="hero-stat">
                <span class="hero-stat-number">11</span>
                <span class="hero-stat-label">bairros</span>
            </div>
            <div class="hero-stat-divider"></div>
            <div class="hero-stat">
                <span class="hero-stat-number">30s</span>
                <span class="hero-stat-label">resultado</span>
            </div>
        </div>
    </div>
    <div class="hero-scroll-indicator" aria-hidden="true">
        <div class="hero-scroll-mouse">
            <div class="hero-scroll-wheel"></div>
        </div>
    </div>
</section>

<section id="como-funciona" class="section section-steps">
    <div class="container">
        <div class="section-header animate-on-scroll">
            <span class="section-badge">Como funciona</span>
            <h2 class="section-title">Três passos para<br><span class="text-gradient">descobrir sua casa</span></h2>
        </div>
        <div class="steps-grid">
            <div class="step-card animate-on-scroll" data-delay="0">
                <div class="step-number">01</div>
                <div class="step-icon">
                    <svg viewBox="0 0 48 48" width="48" height="48" fill="none" aria-hidden="true">
                        <rect x="4" y="8" width="40" height="32" rx="4" stroke="currentColor" stroke-width="2"/>
                        <circle cx="24" cy="20" r="6" stroke="currentColor" stroke-width="2"/>
                        <path d="M14 36c0-5.52 4.48-10 10-10s10 4.48 10 10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
                <h3 class="step-title">Conecte-se</h3>
                <p class="step-desc">Autorize o acesso via OAuth às métricas disponíveis na API oficial do Threads. Seguro e rápido.</p>
            </div>
            <div class="step-connector animate-on-scroll" aria-hidden="true">
                <svg viewBox="0 0 80 24" width="80" height="24">
                    <path d="M0 12h70M60 4l10 8-10 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="connector-path"/>
                </svg>
            </div>
            <div class="step-card animate-on-scroll" data-delay="200">
                <div class="step-number">02</div>
                <div class="step-icon">
                    <svg viewBox="0 0 48 48" width="48" height="48" fill="none" aria-hidden="true">
                        <path d="M24 4v8M24 36v8M4 24h8M36 24h8M10.34 10.34l5.66 5.66M32 32l5.66 5.66M10.34 37.66l5.66-5.66M32 16l5.66-5.66" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <circle cx="24" cy="24" r="8" stroke="currentColor" stroke-width="2"/>
                    </svg>
                </div>
                <h3 class="step-title">Análise Inteligente</h3>
                <p class="step-desc">Calculamos seu score com seguidores, views, engajamento e consistência — tudo transformado em um índice de 0 a 100.</p>
            </div>
            <div class="step-connector animate-on-scroll" aria-hidden="true">
                <svg viewBox="0 0 80 24" width="80" height="24">
                    <path d="M0 12h70M60 4l10 8-10 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="connector-path"/>
                </svg>
            </div>
            <div class="step-card animate-on-scroll" data-delay="400">
                <div class="step-number">03</div>
                <div class="step-icon">
                    <svg viewBox="0 0 48 48" width="48" height="48" fill="none" aria-hidden="true">
                        <path d="M8 40V20l16-12 16 12v20H8z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                        <rect x="18" y="28" width="12" height="12" stroke="currentColor" stroke-width="2"/>
                        <path d="M24 8v4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
                <h3 class="step-title">Sua Casa</h3>
                <p class="step-desc">Receba seu imóvel digital, compartilhe a página pública e libere a versão premium com Pix.</p>
            </div>
        </div>
    </div>
</section>

<section id="bairros" class="section section-bairros">
    <div class="container">
        <div class="section-header animate-on-scroll">
            <span class="section-badge">Os bairros</span>
            <h2 class="section-title">11 bairros, <span class="text-gradient">infinitas personalidades</span></h2>
            <p class="section-subtitle">Cada bairro reflete um perfil. Arraste para explorar todos →</p>
        </div>
    </div>
    <div class="bairros-carousel" id="bairros-carousel">
        <div class="bairro-card animate-on-scroll" style="--accent: #E8C869;">
            <div class="bairro-emoji">🏆</div>
            <h3 class="bairro-name">Bairro Premium</h3>
            <p class="bairro-desc">Os mais influentes. Alto engajamento, muitos seguidores, conteúdo de valor e presença constante.</p>
            <span class="bairro-tag">Elite</span>
        </div>
        <div class="bairro-card animate-on-scroll" style="--accent: #9B8EC4;">
            <div class="bairro-emoji">🏢</div>
            <h3 class="bairro-name">Torre dos Influenciadores</h3>
            <p class="bairro-desc">Top creators em andares exclusivos. Os que movem tendências e pautam conversas.</p>
            <span class="bairro-tag">Top Creators</span>
        </div>
        <div class="bairro-card animate-on-scroll" style="--accent: #5B9BD5;">
            <div class="bairro-emoji">💼</div>
            <h3 class="bairro-name">Alameda dos Experts</h3>
            <p class="bairro-desc">Tecnologia, negócios e carreira. Quem ensina, compartilha e agrega valor profissional.</p>
            <span class="bairro-tag">Especialistas</span>
        </div>
        <div class="bairro-card animate-on-scroll" style="--accent: #4CAF50;">
            <div class="bairro-emoji">💪</div>
            <h3 class="bairro-name">Vila do Fitness</h3>
            <p class="bairro-desc">Saúde, treino e performance. Quem vive na disciplina e inspira hábitos saudáveis.</p>
            <span class="bairro-tag">Saúde</span>
        </div>
        <div class="bairro-card animate-on-scroll" style="--accent: #FF9800;">
            <div class="bairro-emoji">😂</div>
            <h3 class="bairro-name">Vila da Resenha</h3>
            <p class="bairro-desc">Memes, humor e zoeira. O bairro mais barulhento e divertido do condomínio.</p>
            <span class="bairro-tag">Humor</span>
        </div>
        <div class="bairro-card animate-on-scroll" style="--accent: #26C6DA;">
            <div class="bairro-emoji">💬</div>
            <h3 class="bairro-name">Praça da Conversa</h3>
            <p class="bairro-desc">Discussões do momento. Quem comenta, debate e participa de tudo.</p>
            <span class="bairro-tag">Debates</span>
        </div>
        <div class="bairro-card animate-on-scroll" style="--accent: #66BB6A;">
            <div class="bairro-emoji">🌱</div>
            <h3 class="bairro-name">Pracinha dos Iniciantes</h3>
            <p class="bairro-desc">Novos por aqui! Conta recente, poucos posts, mas muito potencial de crescimento.</p>
            <span class="bairro-tag">Novatos</span>
        </div>
        <div class="bairro-card animate-on-scroll" style="--accent: #78909C;">
            <div class="bairro-emoji">🔇</div>
            <h3 class="bairro-name">Subúrbio do Silêncio</h3>
            <p class="bairro-desc">Pouco postam, mas observam tudo. Os lurkers que sabem de tudo sem dizer nada.</p>
            <span class="bairro-tag">Observadores</span>
        </div>
        <div class="bairro-card animate-on-scroll" style="--accent: #EF5350;">
            <div class="bairro-emoji">🚨</div>
            <h3 class="bairro-name">Delegacia da Treta</h3>
            <p class="bairro-desc">Confusões, cancelamentos e polêmicas. Onde cada post é uma nova ocorrência.</p>
            <span class="bairro-tag">Polêmicos</span>
        </div>
        <div class="bairro-card animate-on-scroll" style="--accent: #8D6E63;">
            <div class="bairro-emoji">👻</div>
            <h3 class="bairro-name">Bairro Esquecido</h3>
            <p class="bairro-desc">Contas abandonadas e inativas. Já fizeram parte, mas sumiram do mapa.</p>
            <span class="bairro-tag">Inativos</span>
        </div>
        <div class="bairro-card animate-on-scroll" style="--accent: #546E7A;">
            <div class="bairro-emoji">⚰️</div>
            <h3 class="bairro-name">Cemitério das Contas</h3>
            <p class="bairro-desc">Contas deletadas e suspensas. Descanse em paz, perfil.</p>
            <span class="bairro-tag">R.I.P.</span>
        </div>
    </div>
</section>

<section id="metricas" class="section section-metricas">
    <div class="container">
        <div class="section-header animate-on-scroll">
            <span class="section-badge">Métricas</span>
            <h2 class="section-title">O que <span class="text-gradient">analisamos</span></h2>
            <p class="section-subtitle">Cruzamos dados públicos do seu perfil para mapear seu lugar no condomínio e estimar um valor simbólico</p>
        </div>
        <div class="metricas-grid">
            <div class="metrica-card animate-on-scroll" data-delay="0">
                <div class="metrica-icon-wrap">
                    <svg viewBox="0 0 32 32" width="32" height="32" fill="none" aria-hidden="true">
                        <circle cx="16" cy="10" r="6" stroke="currentColor" stroke-width="2"/>
                        <path d="M6 28c0-5.52 4.48-10 10-10s10 4.48 10 10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
                <h3 class="metrica-title">Seguidores</h3>
                <p class="metrica-desc">Volume de seguidores e relação seguindo/seguidores</p>
            </div>
            <div class="metrica-card animate-on-scroll" data-delay="100">
                <div class="metrica-icon-wrap">
                    <svg viewBox="0 0 32 32" width="32" height="32" fill="none" aria-hidden="true">
                        <path d="M16 6l3.09 6.26L26 13.27l-5 4.87 1.18 6.86L16 21.77 9.82 25l1.18-6.86-5-4.87 6.91-1.01L16 6z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h3 class="metrica-title">Engajamento</h3>
                <p class="metrica-desc">Curtidas, respostas e reposts por thread</p>
            </div>
            <div class="metrica-card animate-on-scroll" data-delay="200">
                <div class="metrica-icon-wrap">
                    <svg viewBox="0 0 32 32" width="32" height="32" fill="none" aria-hidden="true">
                        <rect x="4" y="4" width="24" height="24" rx="4" stroke="currentColor" stroke-width="2"/>
                        <path d="M4 12h24M12 12v16" stroke="currentColor" stroke-width="2"/>
                        <circle cx="20" cy="20" r="3" stroke="currentColor" stroke-width="2"/>
                    </svg>
                </div>
                <h3 class="metrica-title">Frequência</h3>
                <p class="metrica-desc">Quantas threads por semana e consistência</p>
            </div>
            <div class="metrica-card animate-on-scroll" data-delay="300">
                <div class="metrica-icon-wrap">
                    <svg viewBox="0 0 32 32" width="32" height="32" fill="none" aria-hidden="true">
                        <path d="M6 26V14l10-8 10 8v12a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                        <path d="M12 28V18h8v10" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h3 class="metrica-title">Tipo de Conteúdo</h3>
                <p class="metrica-desc">Humor, tech, fitness, debates ou polêmicas</p>
            </div>
            <div class="metrica-card animate-on-scroll" data-delay="400">
                <div class="metrica-icon-wrap">
                    <svg viewBox="0 0 32 32" width="32" height="32" fill="none" aria-hidden="true">
                        <path d="M8 28V18M16 28V8M24 28V14" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                    </svg>
                </div>
                <h3 class="metrica-title">Crescimento</h3>
                <p class="metrica-desc">Velocidade de crescimento e tendência do perfil</p>
            </div>
            <div class="metrica-card animate-on-scroll" data-delay="500">
                <div class="metrica-icon-wrap">
                    <svg viewBox="0 0 32 32" width="32" height="32" fill="none" aria-hidden="true">
                        <circle cx="16" cy="16" r="12" stroke="currentColor" stroke-width="2"/>
                        <path d="M16 8v8l6 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h3 class="metrica-title">Valor Simbólico</h3>
                <p class="metrica-desc">Estimativa lúdica do seu imóvel digital no condomínio</p>
            </div>
        </div>
    </div>
</section>

<section id="premium" class="section section-premium">
    <div class="premium-glow"></div>
    <div class="container">
        <div class="premium-content">
            <div class="premium-text animate-on-scroll">
                <span class="premium-badge">
                    <svg viewBox="0 0 20 20" width="16" height="16" fill="currentColor" aria-hidden="true">
                        <path d="M10 1l2.39 4.84L18 6.87l-4 3.9.94 5.5L10 13.77l-4.94 2.5.94-5.5-4-3.9 5.61-1.03L10 1z"/>
                    </svg>
                    Premium
                </span>
                <h2 class="premium-title">Sua casa em <span class="text-shimmer">imagem realista com IA</span></h2>
                <p class="premium-desc">
                    Gere uma imagem exclusiva do seu imóvel simbólico no Condomínio Threads, personalizada com base no seu perfil. Pronta para compartilhar nas redes.
                </p>
                <ul class="premium-features">
                    <li>
                        <svg viewBox="0 0 20 20" width="18" height="18" fill="none" aria-hidden="true"><path d="M4 10l4 4 8-8" stroke="#C4994C" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Imagem em alta resolução para stories e feed
                    </li>
                    <li>
                        <svg viewBox="0 0 20 20" width="18" height="18" fill="none" aria-hidden="true"><path d="M4 10l4 4 8-8" stroke="#C4994C" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Personalizada com suas métricas
                    </li>
                    <li>
                        <svg viewBox="0 0 20 20" width="18" height="18" fill="none" aria-hidden="true"><path d="M4 10l4 4 8-8" stroke="#C4994C" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Pagamento instantâneo via Pix
                    </li>
                    <li>
                        <svg viewBox="0 0 20 20" width="18" height="18" fill="none" aria-hidden="true"><path d="M4 10l4 4 8-8" stroke="#C4994C" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Gerada por IA em menos de 60s
                    </li>
                </ul>
                <div class="premium-price-row">
                    <div class="premium-price">
                        <span class="premium-price-currency">R$</span>
                        <span class="premium-price-value">{{ $priceReais }}</span>
                        <span class="premium-price-cents">,{{ $priceCentavos }}</span>
                    </div>
                    <span class="premium-price-note">pagamento único</span>
                </div>
                <a href="{{ route('premium.show') }}" class="btn btn-premium">
                    <svg viewBox="0 0 20 20" width="18" height="18" fill="currentColor" aria-hidden="true">
                        <path d="M10 1l2.39 4.84L18 6.87l-4 3.9.94 5.5L10 13.77l-4.94 2.5.94-5.5-4-3.9 5.61-1.03L10 1z"/>
                    </svg>
                    Quero Minha Casa com IA
                </a>
            </div>
            <div class="premium-visual animate-on-scroll">
                <div class="premium-image-frame">
                    <div class="premium-image-glow"></div>
                    <img src="{{ asset('images/premium-house.png') }}" alt="Casa premium gerada por IA no Condomínio Threads" class="premium-image" loading="lazy" width="480" height="360">
                    <div class="premium-image-badge">
                        <span>✨ Gerado por IA</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<footer id="footer" class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-brand">
                <div class="logo">
                    <svg class="logo-icon" viewBox="0 0 24 24" width="24" height="24" fill="none" aria-hidden="true">
                        <path d="M16.87 10.27c-.06-.03-.13-.05-.19-.08a5.65 5.65 0 0 0-2.06-4.66 5.65 5.65 0 0 0-5.04-1.11A5.65 5.65 0 0 0 5.5 8.69a5.65 5.65 0 0 0 1.11 5.04 5.65 5.65 0 0 0 4.27 2.08c.26 0 .52-.02.78-.05a3.44 3.44 0 0 1-1.06 1.78 3.44 3.44 0 0 1-2.03.87c-.42.03-.77.37-.77.79v.01c0 .46.39.82.85.79a5.22 5.22 0 0 0 3.35-1.5 5.22 5.22 0 0 0 1.55-3.28c.01-.1.02-.2.02-.3v-.28c.8-.22 1.51-.65 2.08-1.24a4.83 4.83 0 0 0 1.22-2.89v-.04a.44.44 0 0 0-.44-.44.44.44 0 0 0-.44.44 3.94 3.94 0 0 1-1 2.36 3.94 3.94 0 0 1-2.2 1.14v-.27a3.6 3.6 0 0 0-.71-2.14 3.6 3.6 0 0 0-1.84-1.34c.02-.24.06-.47.13-.7a4.78 4.78 0 0 1 1.56-2.3 4.78 4.78 0 0 1 2.6-1.02c.97-.07 1.92.19 2.72.73a4.78 4.78 0 0 1 1.81 2.08c.08.17.12.27.15.37" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        <circle cx="12" cy="12" r="10.5" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                    <span class="logo-text">Condomínio<span class="logo-accent">Threads</span></span>
                </div>
                <p class="footer-tagline">Aqui cada perfil tem seu lugar. Qual é o seu?</p>
            </div>
            <div class="footer-links">
                <div class="footer-col">
                    <h4>Condomínio</h4>
                    <a href="#como-funciona">Como funciona</a>
                    <a href="#bairros">Os Bairros</a>
                    <a href="#metricas">Métricas</a>
                    <a href="#premium">Premium</a>
                </div>
                <div class="footer-col">
                    <h4>Legal</h4>
                    <a href="{{ route('legal.terms') }}">Termos de Uso</a>
                    <a href="{{ route('legal.privacy') }}">Privacidade</a>
                    <a href="{{ route('legal.data-deletion') }}">Exclusão de dados</a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>Síndico: <a href="https://threads.net/@threads.oficial" target="_blank" rel="noopener noreferrer">@threads.oficial</a></p>
            <p class="footer-copy">&copy; {{ date('Y') }} Condomínio Threads. Classificação simbólica e recreativa.</p>
        </div>
    </div>
</footer>
@endsection
