@php
    // Render the features map back to the "key: value" textarea format.
    $featuresText = collect($plan->features ?? [])
        ->map(fn ($v, $k) => $k.': '.(is_bool($v) ? ($v ? 'true' : 'false') : $v))
        ->implode("\n");
@endphp
<x-admin-layout>
    <x-slot name="header">{{ $plan->exists ? __('admin.edit_plan') : __('admin.new_plan') }}</x-slot>

    <form method="POST" class="card max-w-2xl space-y-5 p-6"
          action="{{ $plan->exists ? route('admin.plans.update', $plan) : route('admin.plans.store') }}">
        @csrf
        @if ($plan->exists) @method('PUT') @endif

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="name_ar" :value="__('admin.name_ar')" />
                <x-text-input id="name_ar" name="name_ar" class="mt-1 block w-full" :value="old('name_ar', $plan->name_ar)" required />
                <x-input-error :messages="$errors->get('name_ar')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="name_en" :value="__('admin.name_en')" />
                <x-text-input id="name_en" name="name_en" class="mt-1 block w-full" :value="old('name_en', $plan->name_en)" required />
                <x-input-error :messages="$errors->get('name_en')" class="mt-2" />
            </div>
        </div>

        <div>
            <x-input-label for="slug" :value="__('admin.slug')" />
            <x-text-input id="slug" name="slug" class="mt-1 block w-full" :value="old('slug', $plan->slug)" />
            <p class="mt-1 text-xs text-ink-500 dark:text-ink-400">{{ __('admin.slug_hint') }}</p>
            <x-input-error :messages="$errors->get('slug')" class="mt-2" />
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <div>
                <x-input-label for="price" :value="__('admin.price_omr')" />
                <x-text-input id="price" name="price" type="number" step="0.001" min="0" class="mt-1 block w-full"
                              :value="old('price', $plan->exists ? App\Support\Money::decimalString($plan->price) : '0')" required />
                <p class="mt-1 text-xs text-ink-500 dark:text-ink-400">{{ __('billing.free') }} = 0</p>
                <x-input-error :messages="$errors->get('price')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="interval" :value="__('admin.interval')" />
                <select id="interval" name="interval" class="form-input-field mt-1">
                    @foreach (App\Enums\BillingInterval::options() as $value => $label)
                        <option value="{{ $value }}" @selected(old('interval', $plan->interval?->value) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <x-input-label for="trial_days" :value="__('admin.trial_days')" />
                <x-text-input id="trial_days" name="trial_days" type="number" min="0" class="mt-1 block w-full"
                              :value="old('trial_days', $plan->trial_days ?? 0)" />
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="description_ar" :value="__('admin.description_ar')" />
                <textarea id="description_ar" name="description_ar" rows="3" class="form-input-field mt-1">{{ old('description_ar', $plan->description_ar) }}</textarea>
            </div>
            <div>
                <x-input-label for="description_en" :value="__('admin.description_en')" />
                <textarea id="description_en" name="description_en" rows="3" class="form-input-field mt-1">{{ old('description_en', $plan->description_en) }}</textarea>
            </div>
        </div>

        <div>
            <x-input-label for="features" :value="__('admin.plan_features')" />
            <textarea id="features" name="features" rows="4" class="form-input-field mt-1 font-mono text-sm"
                      placeholder="max_projects: 10&#10;api_access: true">{{ old('features', $featuresText) }}</textarea>
            <p class="mt-1 text-xs text-ink-500 dark:text-ink-400">{{ __('admin.plan_features_hint') }}</p>
        </div>

        <div class="flex flex-wrap gap-6">
            <label class="flex items-center gap-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" class="rounded border-ink-300 text-brand-600 focus:ring-brand-500 dark:border-ink-700 dark:bg-ink-800"
                       @checked(old('is_active', $plan->is_active ?? true))>
                <span class="text-sm text-ink-700 dark:text-ink-300">{{ __('common.active') }}</span>
            </label>

            <label class="flex items-center gap-2">
                <input type="hidden" name="is_featured" value="0">
                <input type="checkbox" name="is_featured" value="1" class="rounded border-ink-300 text-brand-600 focus:ring-brand-500 dark:border-ink-700 dark:bg-ink-800"
                       @checked(old('is_featured', $plan->is_featured ?? false))>
                <span class="text-sm text-ink-700 dark:text-ink-300">{{ __('billing.featured') }}</span>
            </label>
        </div>

        <div class="flex gap-3 border-t border-ink-200 pt-4 dark:border-ink-800">
            <button type="submit" class="btn-primary">{{ __('common.save') }}</button>
            <a href="{{ route('admin.plans.index') }}" class="btn-secondary">{{ __('common.cancel') }}</a>
        </div>
    </form>
</x-admin-layout>
