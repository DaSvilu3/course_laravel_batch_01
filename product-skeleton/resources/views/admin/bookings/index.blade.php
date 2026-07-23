<x-admin-layout>
    <x-slot name="header">{{ __('admin.bookings') }}</x-slot>

    <form method="GET" class="mb-4 flex flex-wrap items-end gap-2">
        <select name="status"
                class="rounded-xl border-ink-200 bg-white text-sm text-ink-800 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-ink-700 dark:bg-ink-900 dark:text-ink-100">
            <option value="">{{ __('admin.all_statuses') }}</option>
            @foreach (App\Enums\BookingStatus::options() as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <button class="btn-secondary text-xs">{{ __('common.filter') }}</button>
    </form>

    <div class="card overflow-hidden overflow-x-auto">
        <table class="min-w-full divide-y divide-ink-100 dark:divide-ink-800">
            <thead class="bg-ink-50 dark:bg-ink-900/60">
                <tr>
                    <th class="table-th">{{ __('admin.services') }}</th>
                    <th class="table-th">{{ __('admin.customer') }}</th>
                    <th class="table-th">{{ __('shop.appointment') }}</th>
                    <th class="table-th">{{ __('common.status') }}</th>
                    <th class="table-th">{{ __('admin.update_status') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-100 dark:divide-ink-800">
                @forelse ($bookings as $booking)
                    <tr class="hover:bg-ink-50 dark:hover:bg-ink-800/50">
                        <td class="table-td font-medium text-ink-900 dark:text-white">{{ $booking->service?->name }}</td>
                        <td class="table-td">
                            {{ $booking->user?->name }}
                            <span class="block text-xs text-ink-500 dark:text-ink-400">{{ $booking->user?->email }}</span>
                        </td>
                        <td class="table-td">{{ $booking->starts_at?->format('Y-m-d H:i') ?? __('shop.unscheduled') }}</td>
                        <td class="table-td"><x-status-badge :status="$booking->status" /></td>
                        <td class="table-td">
                            <form method="POST" action="{{ route('admin.bookings.update', $booking) }}" class="flex gap-2">
                                @csrf
                                @method('PATCH')
                                <input type="datetime-local" name="starts_at"
                                       class="rounded-xl border-ink-200 bg-white text-xs text-ink-800 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-ink-700 dark:bg-ink-900 dark:text-ink-100"
                                       value="{{ $booking->starts_at?->format('Y-m-d\TH:i') }}">
                                <select name="status"
                                        class="rounded-xl border-ink-200 bg-white text-xs text-ink-800 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-ink-700 dark:bg-ink-900 dark:text-ink-100">
                                    @foreach (App\Enums\BookingStatus::options() as $value => $label)
                                        <option value="{{ $value }}" @selected($booking->status->value === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <button class="btn-secondary text-xs">{{ __('common.save') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-6 text-center text-sm text-ink-500 dark:text-ink-400">{{ __('admin.no_records') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $bookings->links() }}</div>
</x-admin-layout>
