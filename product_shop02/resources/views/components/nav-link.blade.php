@props(['active'])

@php
$base = 'inline-flex items-center rounded-lg px-3 py-2 text-sm font-medium transition-colors focus:outline-none';
$classes = ($active ?? false)
            ? $base.' bg-brand-50 text-brand-700 dark:bg-brand-900/40 dark:text-brand-300'
            : $base.' text-ink-500 hover:bg-ink-100 hover:text-ink-800 dark:text-ink-400 dark:hover:bg-ink-800 dark:hover:text-ink-100';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
