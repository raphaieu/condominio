@extends('layouts.app')

@section('title', 'Termos de Serviço — Condomínio Threads')

@section('content')
<section class="max-w-3xl mx-auto px-6 py-12">
    <x-card>
        <h1 class="text-3xl font-heading font-bold mb-6">Termos de Serviço</h1>
        <p class="text-condo-text-muted text-sm mb-8">Última atualização: {{ date('d/m/Y') }}</p>

        <div class="space-y-6 text-condo-text-secondary text-sm leading-relaxed">
            <section>
                <h2 class="text-lg font-semibold text-condo-gold-bright mb-2">Natureza recreativa</h2>
                <p>
                    O Condomínio Threads é uma experiência de entretenimento. Os imóveis, bairros, endereços simbólicos,
                    classes sociais digitais, valores estimados e descrições gerados pela plataforma são
                    <strong class="text-condo-text">estritamente simbólicos e recreativos</strong>.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-condo-gold-bright mb-2">Sem valor legal ou financeiro</h2>
                <p>
                    Nenhum resultado representa avaliação financeira, social, profissional ou patrimonial real.
                    O score e a classificação não devem ser interpretados como indicadores de sucesso, status
                    ou capacidade econômica.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-condo-gold-bright mb-2">Uso da plataforma</h2>
                <p>
                    Ao conectar sua conta Threads, você autoriza a coleta das métricas necessárias para gerar
                    seu resultado. Você é responsável por manter a confidencialidade do acesso à sua conta
                    e por usar a plataforma de forma lícita.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-condo-gold-bright mb-2">Versão premium</h2>
                <p>
                    A versão premium, quando disponível, oferece conteúdo adicional (como imagem personalizada)
                    mediante pagamento via Pix. Reembolsos seguem a legislação aplicável e políticas do provedor de pagamento.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-condo-gold-bright mb-2">Alterações</h2>
                <p>
                    Podemos atualizar estes termos periodicamente. O uso continuado da plataforma após alterações
                    constitui aceitação dos novos termos.
                </p>
            </section>
        </div>
    </x-card>
</section>
@endsection
