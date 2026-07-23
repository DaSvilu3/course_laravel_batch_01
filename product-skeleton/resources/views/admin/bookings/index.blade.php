<x-admin-layout>
    <x-slot name="header">{{ __('admin.bookings') }}</x-slot>

    <form method="GET" class="mb-4 flex flex-wrap items-end gap-2">
        <select name="status" class="rounded-lg border-gray-300 text-sm">
            <option value="">{{ __('admin.all_statuses') }}</option>
            @foreach (App\Enums\BookingStatus::options() as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <button class="btn-secondary text-xs">{{ __('common.filter') }}</button>
    </form>

    <div class="card overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="table-th">{{ __('admin.services') }}</th>
                    <th class="table-th">{{ __('admin.customer') }}</th>
                    <th class="table-th">{{ __('shop.appointment') }}</th>
                    <th class="table-th">{{ __('common.status') }}</th>
                    <th class="table-th">{{ __('admin.update_status') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($bookings as $booking)
                    <tr>
                        <td class="table-td font-medium text-gray-900">{{ $booking->service?->name }}</td>
                        <td class="table-td">
                            {{ $booking->user?->name }}
                            <span class="block text-xs text-gray-500">{{ $booking->user?->email }}</span>
                        </td>
                        <td class="table-td">{{ $booking->starts_at?->format('Y-m-d H:i') ?? __('shop.unscheduled') }}</td>
                        <td class="table-td"><x-status-badge :status="$booking->status" /></td>
                        <td class="table-td">
                            <form method="POST" action="{{ route('admin.bookings.update', $booking) }}" class="flex gap-2">
                                @csrf
                                @method('PATCH')
                                <input type="datetime-local" name="starts_at" class="rounded-lg border-gray-300 text-xs"
                                       value="{{ $booking->starts_at?->format('Y-m-d\TH:i') }}">
                                <select name="status" class="rounded-lg border-gray-300 text-xs">
                                    @foreach (App\Enums\BookingStatus::options() as $value => $label)
                                        <option value="{{ $value }}" @selected($booking->status->value === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <button class="btn-secondary text-xs">{{ __('common.save') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-6 text-center text-sm text-gray-500">{{ __('admin.no_records') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $bookings->links() }}</div>
</x-admin-layout>
