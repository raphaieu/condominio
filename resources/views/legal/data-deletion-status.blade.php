@extends('layouts.app')

@section('title', 'Status da Exclusão — Condomínio Threads')

@section('content')
<section class="max-w-3xl mx-auto px-6 py-12">
    <x-card>
        <h1 class="text-3xl font-heading font-bold mb-6">Solicitação de exclusão registrada</h1>

        <div class="space-y-6 text-condo-text-secondary text-sm leading-relaxed">
            <p>
                Recebemos sua solicitação de exclusão de dados via Meta.
                O processamento será concluído em até <strong class="text-condo-text">7 dias úteis</strong>.
            </p>

            <div class="rounded-xl bg-black/30 border border-condo-border p-6 text-center">
                <p class="text-xs text-condo-text-muted uppercase tracking-wider mb-2">Código de confirmação</p>
                <p class="text-2xl font-mono font-bold text-condo-gold-bright">{{ $confirmationCode }}</p>
            </div>

            <p class="text-condo-text-muted text-xs">
                Guarde este código para acompanhar sua solicitação.
                Para dúvidas, entre em contato em
                <a href="mailto:rapha@raphael-martins.com" class="text-condo-gold hover:underline">rapha@raphael-martins.com</a>.
            </p>
        </div>
    </x-card>
</section>
@endsection
