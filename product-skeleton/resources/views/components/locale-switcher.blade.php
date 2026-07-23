@php use App\Support\Locale; @endphp

<div class="flex items-center gap-1 text-sm">
    @foreach (Locale::supported() as $locale)
        <a href="{{ route('locale.switch', $locale) }}"
           class="rounded-md px-2 py-1 {{ app()->getLocale() === $locale ? 'bg-brand-50 font-semibold text-brand-700' : 'text-gray-500 hover:text-gray-700' }}">
            {{ Locale::name($locale) }}
        </a>
    @endforeach
</div>
