<x-app-layout>
    <x-slot name="title">{{ __('shop.new_order') }}</x-slot>

    <div class="mx-auto max-w-3xl">
        <div class="flex items-center gap-3">
            <a href="{{ route('orders.index') }}" class="btn-ghost px-2.5 py-2">
                <svg class="h-5 w-5 rtl-flip" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
            </a>
            <h1 class="text-2xl font-black tracking-tight text-ink-900 dark:text-white">{{ __('shop.create_order') }}</h1>
        </div>

        @error('quota')
            <div class="card mt-5 border-rose-200 bg-rose-50 p-4 text-sm text-rose-700 dark:border-rose-900/50 dark:bg-rose-950/40 dark:text-rose-300">
                {{ $message }}
            </div>
        @enderror

        <form method="POST" action="{{ route('orders.store') }}" enctype="multipart/form-data" class="card mt-5 p-6">
            @csrf
            @include('partials.order-fields')

            <div class="mt-8 flex items-center justify-end gap-3">
                <a href="{{ route('orders.index') }}" class="btn-secondary px-5 py-2.5">{{ __('common.cancel') }}</a>
                <button class="btn-primary px-6 py-2.5">{{ __('shop.create_order') }}</button>
            </div>
        </form>
    </div>
</x-app-layout>
