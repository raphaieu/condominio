<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Condomínio Threads — Descubra sua Casa')</title>
    <meta name="description" content="@yield('meta_description', 'Conecte sua conta do Threads e descubra em qual bairro do condomínio você mora. Análise de métricas, ranking e geração de imagem com IA.')">
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
        <meta property="og:title" content="@yield('title', 'Condomínio Threads — Descubra sua Casa')">
        <meta property="og:description" content="@yield('meta_description', 'Cada perfil tem seu lugar. Descubra em qual bairro do condomínio você mora com base nas suas métricas do Threads.')">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
    @endif

    @fonts
    @vite(['resources/css/landing.css', 'resources/js/landing.js'])
</head>
<body>
    @if (session('success') || session('error'))
        <div class="landing-flash" role="status">
            @if (session('success'))
                <p class="landing-flash-message landing-flash-success">{{ session('success') }}</p>
            @endif
            @if (session('error'))
                <p class="landing-flash-message landing-flash-error">{{ session('error') }}</p>
            @endif
        </div>
    @endif

    @yield('content')
</body>
</html>
