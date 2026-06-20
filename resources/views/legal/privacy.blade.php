@extends('layouts.app')

@section('title', 'Política de Privacidade — Condominio Threads')

@section('content')
<section class="max-w-3xl mx-auto px-4 py-12">
    <x-card class="prose prose-invert prose-teal max-w-none">
        <h1 class="text-3xl font-bold text-white mb-6">Política de Privacidade</h1>
        <p class="text-slate-300 leading-relaxed mb-4">Última atualização: {{ date('d/m/Y') }}</p>

        <div class="space-y-6 text-slate-300 text-sm leading-relaxed">
            <section>
                <h2 class="text-lg font-semibold text-teal-300 mb-2">O que coletamos</h2>
                <p>
                    O Condominio Threads coleta dados autorizados da conta Threads que você conecta,
                    incluindo nome de usuário, foto de perfil, biografia, métricas públicas e de insights
                    disponíveis via API (como seguidores, visualizações, curtidas, respostas, reposts e engajamento).
                </p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-teal-300 mb-2">Como usamos os dados</h2>
                <p>
                    Utilizamos essas informações exclusivamente para gerar uma classificação lúdica e simbólica
                    do seu "imóvel digital" no Condominio Threads. Não vendemos seus dados, não compartilhamos
                    com terceiros para marketing e não publicamos nada em sua conta sem autorização explícita.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-teal-300 mb-2">Armazenamento e segurança</h2>
                <p>
                    Tokens de acesso são armazenados de forma criptografada. Snapshots de métricas são mantidos
                    para recalcular seu score quando solicitado. Você pode solicitar a exclusão completa dos seus dados
                    a qualquer momento.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-teal-300 mb-2">Seus direitos</h2>
                <p>
                    Você pode desconectar sua conta Threads, solicitar exclusão de dados ou entrar em contato
                    para esclarecimentos. Consulte nossa página de
                    <a href="{{ route('legal.data-deletion') }}" class="text-teal-400 hover:underline">exclusão de dados</a>.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-teal-300 mb-2">Contato</h2>
                <p>
                    Dúvidas sobre privacidade: <a href="mailto:rapha@raphael-martins.com" class="text-teal-400 hover:underline">rapha@raphael-martins.com</a>
                </p>
            </section>
        </div>
    </x-card>
</section>
@endsection
