@extends('layouts.app')

@section('title', 'Política de Privacidade — Condomínio Threads')

@section('content')
<section class="max-w-3xl mx-auto px-6 py-12">
    <x-card>
        <h1 class="text-3xl font-heading font-bold mb-6">Política de Privacidade</h1>
        <p class="text-condo-text-secondary leading-relaxed mb-4">Última atualização: {{ date('d/m/Y') }}</p>

        <div class="space-y-6 text-condo-text-secondary text-sm leading-relaxed">
            <section>
                <h2 class="text-lg font-semibold text-condo-gold-bright mb-2">O que coletamos</h2>
                <p>
                    O Condomínio Threads coleta dados autorizados da conta Threads que você conecta,
                    incluindo nome de usuário, foto de perfil, biografia, métricas públicas e de insights
                    disponíveis via API (como seguidores, visualizações, curtidas, respostas, reposts e engajamento).
                </p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-condo-gold-bright mb-2">Como usamos os dados</h2>
                <p>
                    Utilizamos essas informações exclusivamente para gerar uma classificação lúdica e simbólica
                    do seu "imóvel digital" no Condomínio Threads. Não vendemos seus dados, não compartilhamos
                    com terceiros para marketing e não publicamos nada em sua conta sem autorização explícita.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-condo-gold-bright mb-2">Armazenamento e segurança</h2>
                <p>
                    Tokens de acesso são armazenados de forma criptografada. Snapshots de métricas são mantidos
                    para recalcular seu score quando solicitado. Você pode solicitar a exclusão completa dos seus dados
                    a qualquer momento.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-condo-gold-bright mb-2">Seus direitos</h2>
                <p>
                    Você pode desconectar sua conta Threads, solicitar exclusão de dados ou entrar em contato
                    para esclarecimentos. Consulte nossa página de
                    <a href="{{ route('legal.data-deletion') }}" class="text-condo-gold hover:underline">exclusão de dados</a>.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-condo-gold-bright mb-2">Contato</h2>
                <p>
                    Dúvidas sobre privacidade: <a href="mailto:rapha@raphael-martins.com" class="text-condo-gold hover:underline">rapha@raphael-martins.com</a>
                </p>
            </section>
        </div>
    </x-card>
</section>
@endsection
