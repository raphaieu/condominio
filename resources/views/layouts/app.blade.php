<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('meta_description', 'Condominio Threads — descubra seu imóvel simbólico com base nas suas métricas do Threads.')">

    @hasSection('meta_og')
        @yield('meta_og')
    @else
        <meta property="og:title" content="@yield('title', config('app.name'))">
        <meta property="og:description" content="@yield('meta_description', 'Condominio Threads — descubra seu imóvel simbólico com base nas suas métricas do Threads.')">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
    @endif

    <title>@yield('title', config('app.name'))</title>

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen gradient-condo text-slate-100 font-sans">
    <x-header />

    @if (session('success'))
        <div class="max-w-4xl mx-auto px-4 pt-4">
            <div class="rounded-lg bg-teal-500/20 border border-teal-400/40 px-4 py-3 text-teal-100 text-sm">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="max-w-4xl mx-auto px-4 pt-4">
            <div class="rounded-lg bg-rose-500/20 border border-rose-400/40 px-4 py-3 text-rose-100 text-sm">
                {{ session('error') }}
            </div>
        </div>
    @endif

    <main class="flex-1">
        @yield('content')
    </main>

    <x-footer />
</body>
</html>
