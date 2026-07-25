<x-guest-layout>
    <div class="mb-6 text-center">
        @if ($merchant->logoUrl())
            <img src="{{ $merchant->logoUrl() }}" alt="{{ $merchant->displayStoreName() }}"
                 class="mx-auto h-16 w-16 rounded-2xl object-cover shadow-lift ring-1 ring-ink-900/5 dark:ring-white/10">
        @else
            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500 to-violet-500 text-white shadow-glow">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m-1.5 0h10.5a1.5 1.5 0 0 1 1.5 1.5v6a1.5 1.5 0 0 1-1.5 1.5H6.75a1.5 1.5 0 0 1-1.5-1.5v-6a1.5 1.5 0 0 1 1.5-1.5Z" /></svg>
            </span>
        @endif
        <h1 class="mt-4 text-xl font-bold tracking-tight text-ink-900 dark:text-white">
            {{ __('orders.intake_heading', ['store' => $merchant->displayStoreName()]) }}
        </h1>
        <p class="mt-2 text-sm text-ink-500 dark:text-ink-400">{{ __('orders.intake_hint') }}</p>
    </div>

    @if ($full)
        <div class="rounded-xl border border-amber-300/70 bg-amber-50 p-5 text-center dark:border-amber-500/30 dark:bg-amber-500/10">
            <p class="font-semibold text-amber-800 dark:text-amber-200">{{ __('orders.intake_closed_title') }}</p>
            <p class="mt-1 text-sm text-amber-700 dark:text-amber-300">{{ __('orders.intake_closed_body') }}</p>
        </div>
    @else
        @if (session('error'))
            <div class="mb-4 rounded-xl border border-rose-300/70 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('intake.store', $merchant->store_slug) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <x-input-label for="customer_name" :value="__('orders.form_name')" />
                <x-text-input id="customer_name" class="mt-1.5 block w-full" type="text" name="customer_name" :value="old('customer_name')" required autofocus />
                <x-input-error :messages="$errors->get('customer_name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="customer_phone" :value="__('orders.form_phone')" />
                <x-text-input id="customer_phone" dir="ltr" class="mt-1.5 block w-full text-start" type="tel" name="customer_phone" :value="old('customer_phone')" required placeholder="9XXXXXXX" />
                <x-input-error :messages="$errors->get('customer_phone')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="item_description" :value="__('orders.form_item')" />
                <textarea id="item_description" name="item_description" rows="3" required
                          class="form-input-field mt-1.5 block w-full" placeholder="{{ __('orders.form_item_hint') }}">{{ old('item_description') }}</textarea>
                <x-input-error :messages="$errors->get('item_description')" class="mt-2" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="quantity" :value="__('orders.form_quantity')" />
                    <x-text-input id="quantity" class="mt-1.5 block w-full" type="number" name="quantity" :value="old('quantity', 1)" min="1" required />
                    <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="amount" :value="__('orders.form_amount')" />
                    <x-text-input id="amount" dir="ltr" class="mt-1.5 block w-full text-start" type="number" step="0.001" min="0" name="amount" :value="old('amount')" placeholder="0.000" />
                    <p class="mt-1 text-xs text-ink-400 dark:text-ink-500">{{ __('orders.form_amount_hint') }}</p>
                </div>
            </div>

            <div>
                <x-input-label for="customer_location" :value="__('orders.form_location')" />
                <x-text-input id="customer_location" class="mt-1.5 block w-full" type="text" name="customer_location" :value="old('customer_location')" placeholder="{{ __('orders.form_location_hint') }}" />
                <x-input-error :messages="$errors->get('customer_location')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="notes" :value="__('orders.form_notes')" />
                <textarea id="notes" name="notes" rows="2"
                          class="form-input-field mt-1.5 block w-full" placeholder="{{ __('orders.form_notes_hint') }}">{{ old('notes') }}</textarea>
                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
            </div>

            {{-- Optional product photo with a live preview --}}
            <div x-data="{ preview: null, name: null,
                pick(e) { const f = e.target.files[0]; if (!f) { this.preview = null; this.name = null; return; }
                          this.name = f.name; this.preview = URL.createObjectURL(f); } }">
                <x-input-label for="image" :value="__('orders.form_image')" />
                <label for="image" class="mt-1.5 flex cursor-pointer items-center gap-3 rounded-xl border border-dashed border-ink-300 bg-ink-50/50 px-4 py-3 transition hover:border-brand-400 dark:border-ink-700 dark:bg-ink-950/40">
                    <template x-if="!preview">
                        <span class="flex h-12 w-12 items-center justify-center rounded-lg bg-white text-ink-400 dark:bg-ink-800">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.16-5.16a2.25 2.25 0 0 1 3.18 0l5.16 5.16m-1.5-1.5 1.41-1.41a2.25 2.25 0 0 1 3.18 0l2.16 2.16M3.75 4.5h16.5a1.5 1.5 0 0 1 1.5 1.5v12a1.5 1.5 0 0 1-1.5 1.5H3.75a1.5 1.5 0 0 1-1.5-1.5V6a1.5 1.5 0 0 1 1.5-1.5Zm11.25 4.5a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" /></svg>
                        </span>
                    </template>
                    <template x-if="preview">
                        <img :src="preview" alt="" class="h-12 w-12 rounded-lg object-cover">
                    </template>
                    <span class="min-w-0 flex-1">
                        <span class="block truncate text-sm font-medium text-ink-700 dark:text-ink-200" x-text="name || '{{ __('orders.form_image') }}'"></span>
                        <span class="block text-xs text-ink-400 dark:text-ink-500">{{ __('orders.form_image_hint') }}</span>
                    </span>
                    <input id="image" name="image" type="file" accept="image/png,image/jpeg,image/webp" class="sr-only" @change="pick($event)">
                </label>
                <x-input-error :messages="$errors->get('image')" class="mt-2" />
            </div>

            <x-primary-button class="w-full justify-center py-3">
                {{ __('orders.submit_order') }}
            </x-primary-button>
        </form>
    @endif
</x-guest-layout>
