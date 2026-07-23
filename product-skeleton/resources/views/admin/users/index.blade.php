<x-admin-layout>
    <x-slot name="header">{{ __('admin.users') }}</x-slot>

    <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
        <form method="GET" class="flex flex-wrap items-end gap-2">
            <input type="search" name="q" value="{{ request('q') }}"
                   placeholder="{{ __('common.search') }}" class="rounded-lg border-gray-300 text-sm">
            <select name="role" class="rounded-lg border-gray-300 text-sm">
                <option value="">{{ __('common.all') }}</option>
                @foreach (App\Enums\UserRole::options() as $value => $label)
                    <option value="{{ $value }}" @selected(request('role') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button class="btn-secondary text-xs">{{ __('common.filter') }}</button>
        </form>

        <a href="{{ route('admin.users.create') }}" class="btn-primary text-xs">{{ __('admin.new_user') }}</a>
    </div>

    <div class="card overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="table-th">{{ __('admin.name') }}</th>
                    <th class="table-th">{{ __('admin.email') }}</th>
                    <th class="table-th">{{ __('admin.role') }}</th>
                    <th class="table-th">{{ __('admin.orders_count') }}</th>
                    <th class="table-th">{{ __('common.status') }}</th>
                    <th class="table-th">{{ __('common.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($users as $user)
                    <tr>
                        <td class="table-td font-medium text-gray-900">{{ $user->name }}</td>
                        <td class="table-td">{{ $user->email }}</td>
                        <td class="table-td">{{ $user->role->label() }}</td>
                        <td class="table-td">{{ $user->orders_count }}</td>
                        <td class="table-td">
                            <span class="badge {{ $user->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                {{ $user->is_active ? __('common.active') : __('common.inactive') }}
                            </span>
                        </td>
                        <td class="table-td">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.users.edit', $user) }}" class="text-brand-700 hover:underline">
                                    {{ __('common.edit') }}
                                </a>
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                      onsubmit="return confirm('{{ __('common.confirm_delete') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-rose-600 hover:underline">{{ __('common.delete') }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-6 text-center text-sm text-gray-500">{{ __('admin.no_records') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $users->links() }}</div>
</x-admin-layout>
