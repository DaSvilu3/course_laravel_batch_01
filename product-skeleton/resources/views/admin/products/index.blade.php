<x-admin-layout>
    <x-slot name="header">{{ __('admin.products') }}</x-slot>

    <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
        <form method="GET" class="flex items-end gap-2">
            <input type="search" name="q" value="{{ request('q') }}"
                   placeholder="{{ __('common.search') }}" class="rounded-lg border-gray-300 text-sm">
            <button class="btn-secondary text-xs">{{ __('common.filter') }}</button>
        </form>

        <a href="{{ route('admin.products.create') }}" class="btn-primary text-xs">{{ __('admin.new_product') }}</a>
    </div>

    <div class="card overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="table-th">{{ __('admin.name') }}</th>
                    <th class="table-th">{{ __('admin.category') }}</th>
                    <th class="table-th">{{ __('shop.price') }}</th>
                    <th class="table-th">{{ __('admin.stock') }}</th>
                    <th class="table-th">{{ __('common.status') }}</th>
                    <th class="table-th">{{ __('common.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($products as $product)
                    <tr>
                        <td class="table-td font-medium text-gray-900">
                            {{ $product->name }}
                            @if ($product->is_featured)
                                <span class="badge bg-amber-100 text-amber-800">{{ __('admin.featured') }}</span>
                            @endif
                        </td>
                        <td class="table-td">{{ $product->category?->name ?? __('admin.no_category') }}</td>
                        <td class="table-td">{{ $product->formattedPrice() }}</td>
                        <td class="table-td">{{ $product->stock ?? '∞' }}</td>
                        <td class="table-td">
                            <span class="badge {{ $product->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                {{ $product->is_active ? __('common.active') : __('common.inactive') }}
                            </span>
                        </td>
                        <td class="table-td">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.products.edit', $product) }}" class="text-brand-700 hover:underline">
                                    {{ __('common.edit') }}
                                </a>
                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
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

    <div class="mt-6">{{ $products->links() }}</div>
</x-admin-layout>
