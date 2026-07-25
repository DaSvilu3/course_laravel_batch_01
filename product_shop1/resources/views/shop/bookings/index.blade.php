<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold tracking-tight text-ink-900 dark:text-white">{{ __('shop.my_bookings') }}</h1>
    </x-slot>

    @if ($bookings->isEmpty())
        <div class="rounded-2xl border border-dashed border-ink-300 py-16 text-center text-ink-500 dark:border-ink-700 dark:text-ink-400">{{ __('shop.no_bookings') }}</div>
    @else
        <div class="space-y-4">
            @foreach ($bookings as $booking)
                <div class="card flex flex-wrap items-center justify-between gap-4 p-4">
                    <div>
                        <p class="font-medium text-ink-900 dark:text-white">{{ $booking->service?->name }}</p>
                        <p class="text-sm text-ink-500 dark:text-ink-400">
                            {{ $booking->starts_at?->format('Y-m-d H:i') ?? __('shop.unscheduled') }}
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <x-status-badge :status="$booking->status" />

                        @can('update', $booking)
                            <form method="POST" action="{{ route('bookings.update', $booking) }}"
                                  class="flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <input type="datetime-local" name="starts_at" class="form-input-field text-sm"
                                       value="{{ $booking->starts_at?->format('Y-m-d\TH:i') }}" required>
                                <button type="submit" class="btn-secondary text-xs">{{ __('shop.schedule') }}</button>
                            </form>
                        @endcan
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $bookings->links() }}</div>
    @endif
</x-app-layout>
