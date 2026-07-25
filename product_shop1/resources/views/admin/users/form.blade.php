<x-admin-layout>
    <x-slot name="header">{{ $user->exists ? __('admin.edit_user') : __('admin.new_user') }}</x-slot>

    <form method="POST" class="card max-w-2xl space-y-5 p-6"
          action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}">
        @csrf
        @if ($user->exists) @method('PUT') @endif

        <div>
            <x-input-label for="name" :value="__('admin.name')" />
            <x-text-input id="name" name="name" class="mt-1 block w-full" :value="old('name', $user->name)" required />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" :value="__('admin.email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="phone" :value="__('admin.phone')" />
            <x-text-input id="phone" name="phone" class="mt-1 block w-full" :value="old('phone', $user->phone)" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="role" :value="__('admin.role')" />
            <select id="role" name="role" class="form-input-field mt-1">
                @foreach (App\Enums\UserRole::options() as $value => $label)
                    <option value="{{ $value }}" @selected(old('role', $user->role?->value) === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="password" :value="__('admin.password')" />
                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                @if ($user->exists)
                    <p class="mt-1 text-xs text-ink-500 dark:text-ink-400">{{ __('admin.password_hint') }}</p>
                @endif
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" :value="__('admin.confirm_password')" />
                <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                              class="mt-1 block w-full" autocomplete="new-password" />
            </div>
        </div>

        <label class="flex items-center gap-2">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1"
                   class="rounded border-ink-300 text-brand-600 focus:ring-brand-500 dark:border-ink-600 dark:bg-ink-900"
                   @checked(old('is_active', $user->is_active ?? true))>
            <span class="text-sm text-ink-700 dark:text-ink-300">{{ __('common.active') }}</span>
        </label>

        <div class="flex gap-3 border-t border-ink-100 pt-4 dark:border-ink-800">
            <button type="submit" class="btn-primary">{{ __('common.save') }}</button>
            <a href="{{ route('admin.users.index') }}" class="btn-secondary">{{ __('common.cancel') }}</a>
        </div>
    </form>
</x-admin-layout>
