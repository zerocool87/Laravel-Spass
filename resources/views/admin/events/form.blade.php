@csrf

<div class="mb-4">
    <x-input-label for="title" :value="__('Title')" />
    <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $event->title ?? '')" required autofocus />
    <x-input-error :messages="$errors->get('title')" class="mt-2" />
</div>

<div class="mb-4">
    <x-input-label for="description" :value="__('Description')" />
    <textarea id="description" name="description" class="block w-full mt-1">{{ old('description', $event->description ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="mb-4">
        <x-input-label for="start_at" :value="__('Start Date')" />
        <x-text-input id="start_at" class="block mt-1 w-full" type="datetime-local" name="start_at" :value="old('start_at', !empty($event->start_at) ? $event->start_at->format('Y-m-d\TH:i') : '')" />
        <x-input-error :messages="$errors->get('start_at')" class="mt-2" />
    </div>

    <div class="mb-4">
        <x-input-label for="end_at" :value="__('End Date')" />
        <x-text-input id="end_at" class="block mt-1 w-full" type="datetime-local" name="end_at" :value="old('end_at', !empty($event->end_at) ? $event->end_at->format('Y-m-d\TH:i') : '')" />
        <x-input-error :messages="$errors->get('end_at')" class="mt-2" />
    </div>
</div>

<div class="mb-4">
    <label class="inline-flex items-center">
        <input type="checkbox" name="is_all_day" value="1" class="rounded" {{ old('is_all_day', $event->is_all_day ?? false) ? 'checked' : '' }}>
        <span class="ms-2">{{ __('All day event') }}</span>
    </label>
</div>

<div class="mb-4">
    <x-input-label for="location" :value="__('Location')" />
    <x-text-input id="location" class="block mt-1 w-full" type="text" name="location" :value="old('location', $event->location ?? '')" />
    <x-input-error :messages="$errors->get('location')" class="mt-2" />
</div>

<div class="mb-4">
    <x-input-label for="type" :value="__('Type')" />
    <select name="type" id="type" class="block w-full mt-1">
        <option value="">-- {{ __('Choose a type') }} --</option>
        @foreach(config('events.types', []) as $type)
            <option value="{{ $type }}" {{ old('type', $event->type ?? '') === $type ? 'selected' : '' }}>{{ __($type) }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('type')" class="mt-2" />
</div>

<div class="flex items-center justify-end mt-4">
    <x-primary-button>
        {{ __('Save') }}
    </x-primary-button>
</div>
