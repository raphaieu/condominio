@props([
    'href' => null,
    'size' => 'md',
    'showText' => true,
    'tag' => null,
])

@php
    $markSizes = [
        'sm' => 'logo-mark-sm',
        'md' => 'logo-mark-md',
        'lg' => 'logo-mark-lg',
        'xl' => 'logo-mark-xl',
    ];
    $pixelSizes = [
        'sm' => 32,
        'md' => 40,
        'lg' => 48,
        'xl' => 56,
    ];
    $markClass = $markSizes[$size] ?? $markSizes['md'];
    $pixels = $pixelSizes[$size] ?? $pixelSizes['md'];

    $iconSrc = match (true) {
        file_exists(public_path('logo-icon.png')) => asset('logo-icon.png'),
        file_exists(public_path('logo-icon.svg')) => asset('logo-icon.svg'),
        default => null,
    };

    $element = $tag ?? ($href ? 'a' : 'div');
@endphp

<{{ $element }}
    @if ($href) href="{{ $href }}" @endif
    {{ $attributes->merge(['class' => 'logo']) }}
>
    @if ($iconSrc)
        <img
            src="{{ $iconSrc }}"
            alt=""
            class="logo-mark {{ $markClass }}"
            width="{{ $pixels }}"
            height="{{ $pixels }}"
            aria-hidden="true"
        >
    @else
        <svg class="logo-icon" viewBox="0 0 24 24" width="28" height="28" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M16.87 10.27c-.06-.03-.13-.05-.19-.08a5.65 5.65 0 0 0-2.06-4.66 5.65 5.65 0 0 0-5.04-1.11A5.65 5.65 0 0 0 5.5 8.69a5.65 5.65 0 0 0 1.11 5.04 5.65 5.65 0 0 0 4.27 2.08c.26 0 .52-.02.78-.05a3.44 3.44 0 0 1-1.06 1.78 3.44 3.44 0 0 1-2.03.87c-.42.03-.77.37-.77.79v.01c0 .46.39.82.85.79a5.22 5.22 0 0 0 3.35-1.5 5.22 5.22 0 0 0 1.55-3.28c.01-.1.02-.2.02-.3v-.28c.8-.22 1.51-.65 2.08-1.24a4.83 4.83 0 0 0 1.22-2.89v-.04a.44.44 0 0 0-.44-.44.44.44 0 0 0-.44.44 3.94 3.94 0 0 1-1 2.36 3.94 3.94 0 0 1-2.2 1.14v-.27a3.6 3.6 0 0 0-.71-2.14 3.6 3.6 0 0 0-1.84-1.34c.02-.24.06-.47.13-.7a4.78 4.78 0 0 1 1.56-2.3 4.78 4.78 0 0 1 2.6-1.02c.97-.07 1.92.19 2.72.73a4.78 4.78 0 0 1 1.81 2.08c.08.17.12.27.15.37" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <circle cx="12" cy="12" r="10.5" stroke="currentColor" stroke-width="1.5"/>
        </svg>
    @endif

    @if ($showText)
        <span class="logo-text">Condomínio<span class="logo-accent">Threads</span></span>
    @endif
</{{ $element }}>
