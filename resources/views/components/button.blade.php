@props([
    'href' => '#',
    'type' => 'link',
    'variant' => 'primary',
])

@php
    $variants = [
        'primary' => 'app-btn app-btn-primary',
        'secondary' => 'app-btn app-btn-secondary',
        'gold' => 'app-btn app-btn-gold',
    ];
    $classes = $variants[$variant] ?? $variants['primary'];
@endphp

@if ($type === 'submit')
    <button type="submit" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@else
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@endif
