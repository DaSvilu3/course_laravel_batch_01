<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('shop.my_bookings') }}</h1>
    </x-slot>

    @if ($bookings->isEmpty())
        <div class="card p-12 text-center text-gray-500">{{ __('shop.no_bookings') }}</div>
    @else
        <div class="space-y-4">
            @foreach ($bookings as $booking)
                <div class="card flex flex-wrap items-center justify-between gap-4 p-4">
                    <div>
                        <p class="font-medium text-gray-900">{{ $booking->service?->name }}</p>
                        <p class="text-sm text-gray-500">
                            {{ $booking->starts_at?->format('Y-m-d H:i') ?? __('shop.unscheduled') }}
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <x-status-badge :status="$booking->status" />

                        @can('update', $booking)
                            <form method="POST" action="{{ route('bookings.update', $booking) }}"
                                  class="flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <input type="datetime-local" name="starts_at" class="rounded-lg border-gray-300 text-sm"
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
