@props(['active'])

@php
$base = 'block w-full rounded-lg px-3 py-2 text-start text-base font-medium transition focus:outline-none';
$classes = ($active ?? false)
            ? $base.' bg-brand-50 text-brand-700 dark:bg-brand-900/40 dark:text-brand-300'
            : $base.' text-ink-600 hover:bg-ink-100 hover:text-ink-900 dark:text-ink-300 dark:hover:bg-ink-800 dark:hover:text-white';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
