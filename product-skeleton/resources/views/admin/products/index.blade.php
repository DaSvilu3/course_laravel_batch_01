<x-admin-layout>
    <x-slot name="header">{{ __('admin.products') }}</x-slot>

    <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
        <form method="GET" class="flex items-end gap-2">
            <input type="search" name="q" value="{{ request('q') }}"
                   placeholder="{{ __('common.search') }}"
                   class="rounded-xl border-ink-200 bg-white text-sm text-ink-800 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-ink-700 dark:bg-ink-900 dark:text-ink-100 dark:placeholder-ink-500">
            <button class="btn-secondary text-xs">{{ __('common.filter') }}</button>
        </form>

        <a href="{{ route('admin.products.create') }}" class="btn-primary text-xs">{{ __('admin.new_product') }}</a>
    </div>

    <div class="card overflow-hidden overflow-x-auto">
        <table class="min-w-full divide-y divide-ink-100 dark:divide-ink-800">
            <thead class="bg-ink-50 dark:bg-ink-900/60">
                <tr>
                    <th class="table-th">{{ __('admin.name') }}</th>
                    <th class="table-th">{{ __('admin.category') }}</th>
                    <th class="table-th">{{ __('shop.price') }}</th>
                    <th class="table-th">{{ __('admin.stock') }}</th>
                    <th class="table-th">{{ __('common.status') }}</th>
                    <th class="table-th">{{ __('common.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-100 dark:divide-ink-800">
                @forelse ($products as $product)
                    <tr class="hover:bg-ink-50 dark:hover:bg-ink-800/50">
                        <td class="table-td font-medium text-ink-900 dark:text-white">
                            {{ $product->name }}
                            @if ($product->is_featured)
                                <span class="badge bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200">{{ __('admin.featured') }}</span>
                            @endif
                        </td>
                        <td class="table-td">{{ $product->category?->name ?? __('admin.no_category') }}</td>
                        <td class="table-td">{{ $product->formattedPrice() }}</td>
                        <td class="table-td">{{ $product->stock ?? '∞' }}</td>
                        <td class="table-td">
                            <span class="badge {{ $product->is_active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200' : 'bg-ink-100 text-ink-600 dark:bg-ink-800 dark:text-ink-300' }}">
                                {{ $product->is_active ? __('common.active') : __('common.inactive') }}
                            </span>
                        </td>
                        <td class="table-td">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.products.edit', $product) }}" class="font-medium text-brand-600 hover:text-brand-500 dark:text-brand-400">
                                    {{ __('common.edit') }}
                                </a>
                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                                      onsubmit="return confirm('{{ __('common.confirm_delete') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="font-medium text-rose-600 hover:text-rose-500 dark:text-rose-400">{{ __('common.delete') }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-6 text-center text-sm text-ink-500 dark:text-ink-400">{{ __('admin.no_records') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $products->links() }}</div>
</x-admin-layout>
