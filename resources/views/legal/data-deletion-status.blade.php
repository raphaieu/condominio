@extends('layouts.app')

@section('title', 'Status da Exclusão — Condominio Threads')

@section('content')
<section class="max-w-3xl mx-auto px-4 py-12">
    <x-card>
        <h1 class="text-3xl font-bold text-white mb-6">Solicitação de exclusão registrada</h1>

        <div class="space-y-6 text-slate-300 text-sm leading-relaxed">
            <p>
                Recebemos sua solicitação de exclusão de dados via Meta.
                O processamento será concluído em até <strong class="text-white">7 dias úteis</strong>.
            </p>

            <div class="rounded-xl bg-black/20 border border-teal-500/20 p-6 text-center">
                <p class="text-xs text-slate-500 uppercase tracking-wider mb-2">Código de confirmação</p>
                <p class="text-2xl font-mono font-bold text-teal-300">{{ $confirmationCode }}</p>
            </div>

            <p class="text-slate-500 text-xs">
                Guarde este código para acompanhar sua solicitação.
                Para dúvidas, entre em contato em
                <a href="mailto:rapha@raphael-martins.com" class="text-teal-400 hover:underline">rapha@raphael-martins.com</a>.
            </p>
        </div>
    </x-card>
</section>
@endsection
