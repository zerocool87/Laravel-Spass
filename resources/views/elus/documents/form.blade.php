@csrf

<div class="mb-4">
    <x-input-label for="title" :value="__('Titre')" />
    <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $document->title ?? '')" required autofocus />
    <x-input-error :messages="$errors->get('title')" class="mt-2" />
</div>

<div class="mb-4">
    <x-input-label for="description" :value="__('Description')" />
    <textarea id="description" name="description" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">{{ old('description', $document->description ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>

<div class="mb-4">
    <x-input-label for="category" :value="__('Catégorie')" />
    <select name="category" id="category" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
        <option value="">-- {{ __('Aucune') }} --</option>
        @foreach(config('documents.categories', []) as $cat)
            <option value="{{ $cat }}" {{ old('category', $document->category ?? '') === $cat ? 'selected' : '' }}>{{ __($cat) }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('category')" class="mt-2" />
</div>

<div class="mb-4">
    <x-input-label for="file" :value="__('Fichier')" />
    <x-text-input id="file" class="block mt-1 w-full" type="file" name="file" />
    <x-input-error :messages="$errors->get('file')" class="mt-2" />
</div>

<div class="mb-4">
    <label class="inline-flex items-center">
        <input type="checkbox" name="visible_to_all" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('visible_to_all', $document->visible_to_all ?? true) ? 'checked' : '' }}>
        <span class="ms-2 text-sm text-gray-600">{{ __('Visible par tous les utilisateurs') }}</span>
    </label>
</div>

<div class="mb-4" id="assigned-users-wrapper">
    <x-input-label for="assigned_users" :value="__('Assigner à des utilisateurs spécifiques (requis si non visible par tous)')" />
    <select name="assigned_users[]" multiple id="assigned_users" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
        @foreach($users as $u)
            <option value="{{ $u->id }}" {{ in_array($u->id, $assigned ?? []) ? 'selected' : '' }}>{{ $u->name }} ({{ $u->email }})</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('assigned_users')" class="mt-2" />
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const cb = document.querySelector('input[name="visible_to_all"]');
    const wrap = document.getElementById('assigned-users-wrapper');
    function toggle(){
        if(cb && wrap){
            if(cb.checked){ wrap.style.display = 'none'; }
            else { wrap.style.display = ''; }
        }
    }
    if(cb){ cb.addEventListener('change', toggle); toggle(); }
});
</script>

<div class="flex items-center justify-end mt-4">
    <x-primary-button>
        {{ __('Enregistrer') }}
    </x-primary-button>
</div>