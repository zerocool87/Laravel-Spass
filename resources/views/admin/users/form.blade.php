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

<div class="mb-4">
    <x-input-label for="fonction" :value="__('Fonction')" />
    <x-text-input id="fonction" class="block mt-1 w-full" type="text" name="fonction" :value="old('fonction', $user->fonction ?? '')" />
    <x-input-error :messages="$errors->get('fonction')" class="mt-2" />
</div>

<div class="mb-4">
    <x-input-label for="commune" :value="__('Commune')" />
    <select id="commune" name="commune" class="block mt-1 w-full rounded">
        <option value="">— {{ __('Select a commune') }} —</option>
        @foreach($communes as $c)
            <option value="{{ $c }}" {{ old('commune', $user->commune ?? '') === $c ? 'selected' : '' }}>{{ $c }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('commune')" class="mt-2" />
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="mb-4">
        <x-input-label for="password" :value="__('Password')" />
        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" />
        @if(isset($user) && $user->exists)
            <p class="text-sm text-gray-500 mt-1">{{ __('Leave empty to keep current password') }}</p>
        @endif
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>

    <div class="mb-4">
        <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
        <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" />
        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
    </div>
</div>

<div class="mb-4">
    <x-input-label for="territory" :value="__('Territoire')" />
    <x-text-input id="territory" class="block mt-1 w-full" type="text" name="territory" :value="old('territory', $user->territory ?? '')" placeholder="Ex: Zone Nord, Centre-ville..." />
    <x-input-error :messages="$errors->get('territory')" class="mt-2" />
</div>

<div class="mb-4 p-4 bg-gray-50 rounded-lg">
    <x-input-label :value="__('Rôles')" class="mb-3" />
    <div class="space-y-2">
        <label class="inline-flex items-center">
            <input type="checkbox" name="is_admin" value="1" class="rounded text-blue-600" {{ old('is_admin', $user->is_admin ?? false) ? 'checked' : '' }}>
            <span class="ms-2">{{ __('Administrateur') }}</span>
            <span class="ms-2 text-xs text-gray-500">{{ __('(Accès complet à l\'administration)') }}</span>
        </label>
        <br>
        <label class="inline-flex items-center">
            <input type="checkbox" name="is_elu" value="1" class="rounded text-green-600" {{ old('is_elu', $user->is_elu ?? false) ? 'checked' : '' }}>
            <span class="ms-2">{{ __('Élu') }}</span>
            <span class="ms-2 text-xs text-gray-500">{{ __('(Accès à l\'Espace Élus)') }}</span>
        </label>
    </div>
</div>

<div class="flex items-center justify-end mt-4">
    <x-primary-button>
        {{ __('Save') }}
    </x-primary-button>
</div>
