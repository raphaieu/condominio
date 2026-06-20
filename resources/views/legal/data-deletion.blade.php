@extends('layouts.app')

@section('title', 'Exclusão de Dados — Condominio Threads')

@section('content')
<section class="max-w-3xl mx-auto px-4 py-12">
    <x-card>
        <h1 class="text-3xl font-bold text-white mb-6">Exclusão de Dados</h1>

        <div class="space-y-6 text-slate-300 text-sm leading-relaxed">
            <p>
                Você tem direito a solicitar a exclusão completa dos seus dados do Condominio Threads,
                incluindo conta conectada, snapshots de métricas, resultados gerados e histórico de pedidos.
            </p>

            <section class="rounded-xl bg-black/20 border border-teal-500/20 p-6">
                <h2 class="text-lg font-semibold text-teal-300 mb-4">Como solicitar</h2>
                <ol class="list-decimal list-inside space-y-3">
                    <li>Envie um e-mail para <a href="mailto:rapha@raphael-martins.com" class="text-teal-400 hover:underline font-medium">rapha@raphael-martins.com</a></li>
                    <li>Use o assunto: <code class="bg-black/30 px-2 py-0.5 rounded text-teal-200">Exclusão de dados - Condominio Threads</code></li>
                    <li>Informe seu @ do Threads no corpo do e-mail</li>
                </ol>
            </section>

            <p>
                Seus dados serão removidos em até <strong class="text-white">7 dias úteis</strong> após a confirmação da solicitação.
                Você receberá uma confirmação por e-mail quando o processo for concluído.
            </p>

            <p class="text-slate-500 text-xs">
                Também é possível desconectar sua conta Threads nas configurações do app Meta,
                o que acionará nosso webhook de desautorização.
            </p>
        </div>
    </x-card>
</section>
@endsection
