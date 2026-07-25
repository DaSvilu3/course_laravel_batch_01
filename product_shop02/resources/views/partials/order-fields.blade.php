{{--
    Shared order fields for the merchant (orders/create) and public intake form.
    Expects: $countries, $wilayatGroups, $paymentMethods, $defaultCountry
--}}
<div class="space-y-6">
    {{-- Customer --}}
    <fieldset class="space-y-4">
        <legend class="text-sm font-bold text-ink-900 dark:text-white">{{ __('shop.customer_details') }}</legend>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="customer_name" :value="__('shop.customer_name')" />
                <x-text-input id="customer_name" name="customer_name" :value="old('customer_name')" class="mt-1.5 block w-full" required />
                <x-input-error :messages="$errors->get('customer_name')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="customer_phone" :value="__('shop.phone')" />
                <x-text-input id="customer_phone" name="customer_phone" :value="old('customer_phone')" class="mt-1.5 block w-full" type="tel" inputmode="tel" dir="ltr" required placeholder="9XXXXXXX" />
                <x-input-error :messages="$errors->get('customer_phone')" class="mt-2" />
            </div>
        </div>
    </fieldset>

    {{-- Order --}}
    <fieldset class="space-y-4">
        <legend class="text-sm font-bold text-ink-900 dark:text-white">{{ __('shop.order_details') }}</legend>
        <div>
            <x-input-label for="item_description" :value="__('shop.item_description')" />
            <textarea id="item_description" name="item_description" rows="3" required class="form-input-field mt-1.5" placeholder="{{ __('shop.item_description_hint') }}">{{ old('item_description') }}</textarea>
            <x-input-error :messages="$errors->get('item_description')" class="mt-2" />
        </div>
        <div class="grid gap-4 sm:grid-cols-3">
            <div>
                <x-input-label for="quantity" :value="__('shop.quantity')" />
                <x-text-input id="quantity" name="quantity" :value="old('quantity', 1)" class="mt-1.5 block w-full" type="number" min="1" required dir="ltr" />
                <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="price" :value="__('shop.price_omr')" />
                <x-text-input id="price" name="price" :value="old('price')" class="mt-1.5 block w-full" type="number" step="0.001" min="0" dir="ltr" placeholder="12.500" />
                <p class="mt-1 text-xs text-ink-500 dark:text-ink-400">{{ __('shop.price_hint') }}</p>
                <x-input-error :messages="$errors->get('price')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="payment_method" :value="__('shop.payment_method')" />
                <select id="payment_method" name="payment_method" class="form-input-field mt-1.5">
                    <option value="">— {{ __('common.optional') }} —</option>
                    @foreach ($paymentMethods as $value => $label)
                        <option value="{{ $value }}" @selected(old('payment_method') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
            </div>
        </div>
    </fieldset>

    {{-- Delivery --}}
    <fieldset class="space-y-4">
        <legend class="text-sm font-bold text-ink-900 dark:text-white">{{ __('shop.delivery_details') }}</legend>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="country" :value="__('shop.country')" />
                <select id="country" name="country" class="form-input-field mt-1.5">
                    @foreach ($countries as $code => $label)
                        <option value="{{ $code }}" @selected(old('country', $defaultCountry) === $code)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <x-input-label for="wilayat" :value="__('shop.governorate')" />
                <select id="wilayat" name="wilayat" class="form-input-field mt-1.5">
                    <option value="">— {{ __('common.optional') }} —</option>
                    @foreach ($wilayatGroups as $group => $wilayats)
                        <optgroup label="{{ $group }}">
                            @foreach ($wilayats as $value => $label)
                                <option value="{{ $value }}" @selected(old('wilayat') === $value)>{{ $label }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('wilayat')" class="mt-2" />
            </div>
        </div>
        <div>
            <x-input-label for="address" :value="__('shop.address')" />
            <textarea id="address" name="address" rows="2" class="form-input-field mt-1.5" placeholder="{{ __('shop.address_hint') }}">{{ old('address') }}</textarea>
            <x-input-error :messages="$errors->get('address')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="location_note" :value="__('shop.location_note')" />
            <x-text-input id="location_note" name="location_note" :value="old('location_note')" class="mt-1.5 block w-full" dir="ltr" placeholder="https://maps.google.com/…" />
            <p class="mt-1 text-xs text-ink-500 dark:text-ink-400">{{ __('shop.location_note_hint') }}</p>
            <x-input-error :messages="$errors->get('location_note')" class="mt-2" />
        </div>
    </fieldset>

    {{-- Extras --}}
    <fieldset class="space-y-4">
        <div>
            <x-input-label for="notes" :value="__('shop.notes')" />
            <textarea id="notes" name="notes" rows="2" class="form-input-field mt-1.5" placeholder="{{ __('shop.notes_hint') }}">{{ old('notes') }}</textarea>
            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="attachment" :value="__('shop.attachment')" />
            <input id="attachment" name="attachment" type="file" accept="image/*"
                   class="mt-1.5 block w-full text-sm text-ink-600 file:me-4 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100 dark:text-ink-300 dark:file:bg-brand-900/40 dark:file:text-brand-300" />
            <p class="mt-1 text-xs text-ink-500 dark:text-ink-400">{{ __('shop.attachment_hint') }}</p>
            <x-input-error :messages="$errors->get('attachment')" class="mt-2" />
        </div>
    </fieldset>
</div>
