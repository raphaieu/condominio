@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'app-card ' . $class]) }}>
    {{ $slot }}
</div>
