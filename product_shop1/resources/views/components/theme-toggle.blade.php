{{-- Light / dark switch. Persists the choice in localStorage; the layout head
     applies it before paint so there is no flash on reload. --}}
<button
    type="button"
    x-data="{
        dark: document.documentElement.classList.contains('dark'),
        toggle() {
            this.dark = !this.dark;
            document.documentElement.classList.toggle('dark', this.dark);
            localStorage.setItem('theme', this.dark ? 'dark' : 'light');
        },
    }"
    @click="toggle()"
    :aria-pressed="dark"
    aria-label="{{ __('common.toggle_theme') }}"
    class="relative inline-flex h-9 w-9 items-center justify-center rounded-xl border border-ink-200 bg-white text-ink-500 transition hover:border-ink-300 hover:text-ink-700 dark:border-ink-700 dark:bg-ink-900 dark:text-ink-400 dark:hover:border-ink-600 dark:hover:text-ink-200"
>
    {{-- Sun (shown in dark mode: click to go light) --}}
    <svg x-show="dark" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
        <circle cx="12" cy="12" r="4" />
        <path stroke-linecap="round" d="M12 2v2m0 16v2M2 12h2m16 0h2M4.9 4.9l1.4 1.4m11.4 11.4 1.4 1.4M19.1 4.9l-1.4 1.4M6.3 17.7l-1.4 1.4" />
    </svg>
    {{-- Moon (shown in light mode: click to go dark) --}}
    <svg x-show="!dark" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z" />
    </svg>
</button>
