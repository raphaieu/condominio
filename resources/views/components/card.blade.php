@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm p-6 shadow-xl ' . $class]) }}>
    {{ $slot }}
</div>
