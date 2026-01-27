@csrf

<div class="mb-4">
    <x-input-label for="name" :value="__('Name')" />
    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name ?? '')" required autofocus />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div class="mb-4">
    <x-input-label for="email" :value="__('Email')" />
    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email ?? '')" required />
    <x-input-error :messages="$errors->get('email')" class="mt-2" />
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="mb-4">
        <x-input-label for="password" :value="__('Password')" />
        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" />
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>

    <div class="mb-4">
        <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
        <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" />
        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
    </div>
</div>

<div class="mb-4">
    <label class="inline-flex items-center">
        <input type="checkbox" name="is_admin" value="1" class="rounded" {{ old('is_admin', $user->is_admin ?? false) ? 'checked' : '' }}>
        <span class="ms-2">{{ __('Administrator') }}</span>
    </label>
</div>

<div class="flex items-center justify-end mt-4">
    <x-primary-button>
        {{ __('Save') }}
    </x-primary-button>
</div>