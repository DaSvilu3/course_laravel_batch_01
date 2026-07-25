@php use App\Support\Locale; @endphp

<div class="inline-flex items-center gap-0.5 rounded-xl border border-ink-200 bg-white p-0.5 text-sm dark:border-ink-700 dark:bg-ink-900">
    @foreach (Locale::supported() as $locale)
        <a href="{{ route('locale.switch', $locale) }}"
           class="rounded-lg px-2.5 py-1 transition {{ app()->getLocale() === $locale
                ? 'bg-brand-600 font-semibold text-white shadow-soft'
                : 'text-ink-500 hover:text-ink-800 dark:text-ink-400 dark:hover:text-ink-100' }}">
            {{ Locale::name($locale) }}
        </a>
    @endforeach
</div>
