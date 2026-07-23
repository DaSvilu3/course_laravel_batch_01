<x-admin-layout>
    <x-slot name="header">{{ __('admin.categories') }}</x-slot>

    <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
        <form method="GET" class="flex items-end gap-2">
            <select name="type" class="rounded-lg border-gray-300 text-sm">
                <option value="">{{ __('common.all') }}</option>
                @foreach (App\Enums\CatalogType::options() as $value => $label)
                    <option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button class="btn-secondary text-xs">{{ __('common.filter') }}</button>
        </form>

        <a href="{{ route('admin.categories.create') }}" class="btn-primary text-xs">{{ __('admin.new_category') }}</a>
    </div>

    <div class="card overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="table-th">{{ __('admin.name') }}</th>
                    <th class="table-th">{{ __('admin.type') }}</th>
                    <th class="table-th">{{ __('admin.slug') }}</th>
                    <th class="table-th">{{ __('admin.items') }}</th>
                    <th class="table-th">{{ __('common.status') }}</th>
                    <th class="table-th">{{ __('common.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($categories as $category)
                    <tr>
                        <td class="table-td font-medium text-gray-900">{{ $category->name }}</td>
                        <td class="table-td">{{ $category->type->label() }}</td>
                        <td class="table-td font-mono text-xs">{{ $category->slug }}</td>
                        <td class="table-td">{{ $category->services_count + $category->products_count }}</td>
                        <td class="table-td">
                            <span class="badge {{ $category->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                {{ $category->is_active ? __('common.active') : __('common.inactive') }}
                            </span>
                        </td>
                        <td class="table-td">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="text-brand-700 hover:underline">
                                    {{ __('common.edit') }}
                                </a>
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
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

    <div class="mt-6">{{ $categories->links() }}</div>
</x-admin-layout>
