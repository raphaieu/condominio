@props([
    'href' => '#',
    'type' => 'link',
    'variant' => 'primary',
])

@php
    $base = 'inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-semibold text-sm transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-condo-dark';
    $variants = [
        'primary' => 'bg-teal-500 hover:bg-teal-400 text-condo-dark focus:ring-teal-400 shadow-lg shadow-teal-500/25',
        'secondary' => 'bg-white/10 hover:bg-white/20 text-white border border-white/20 focus:ring-white/30',
        'gold' => 'bg-gradient-to-r from-condo-gold to-condo-coral text-condo-dark hover:opacity-90 focus:ring-condo-gold shadow-lg shadow-amber-500/20',
    ];
    $classes = $base . ' ' . ($variants[$variant] ?? $variants['primary']);
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
