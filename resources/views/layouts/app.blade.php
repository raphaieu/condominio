<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('meta_description', 'Condomínio Threads — descubra seu imóvel simbólico com base nas suas métricas do Threads.')">
    <meta name="theme-color" content="#C4994C">
    @if (file_exists(public_path('logo-icon.png')))
        <link rel="icon" href="{{ asset('logo-icon.png') }}" type="image/png">
    @elseif (file_exists(public_path('logo-icon.svg')))
        <link rel="icon" href="{{ asset('logo-icon.svg') }}" type="image/svg+xml">
    @elseif (file_exists(public_path('logo-icon.png')))
        <link rel="icon" href="{{ asset('logo-icon.png') }}" type="image/png">
    @elseif (file_exists(public_path('logo-condominio-threads.png')))
        <link rel="icon" href="{{ asset('logo-condominio-threads.png') }}" type="image/png">
    @endif

    @hasSection('meta_og')
        @yield('meta_og')
    @else
        <meta property="og:title" content="@yield('title', config('app.name'))">
        <meta property="og:description" content="@yield('meta_description', 'Condomínio Threads — descubra seu imóvel simbólico com base nas suas métricas do Threads.')">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
    @endif

    <title>@yield('title', config('app.name'))</title>

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="app-shell">
    <x-header />

    @if (session('success'))
        <div class="app-flash-wrap">
            <div class="app-flash app-flash-success" role="status">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="app-flash-wrap">
            <div class="app-flash app-flash-error" role="alert">
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
