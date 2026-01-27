@csrf

<div class="mb-4">
    <label class="block text-gray-200 mb-2">Title</label>
    <input type="text" name="title" value="{{ old('title', $document->title ?? '') }}" class="border border-gray-600 bg-gray-800 text-gray-100 p-2 w-full rounded" required>
    @error('title')<div class="text-red-400">{{ $message }}</div>@enderror
</div>

<div class="mb-4">
    <label class="block text-gray-200 mb-2">Description</label>
    <textarea name="description" class="border border-gray-600 bg-gray-800 text-gray-100 p-2 w-full rounded">{{ old('description', $document->description ?? '') }}</textarea>
    @error('description')<div class="text-red-400">{{ $message }}</div>@enderror
</div>

<div class="mb-4">
    <label class="block text-gray-200 mb-2">Category</label>
    <select name="category" class="block w-full bg-gray-800 text-gray-100 p-2 rounded">
        <option value="">-- {{ __('None') }} --</option>
        @foreach(config('documents.categories', []) as $cat)
            <option value="{{ $cat }}" {{ old('category', $document->category ?? '') === $cat ? 'selected' : '' }}>{{ __($cat) }}</option>
        @endforeach
    </select>
    @error('category')<div class="text-red-400">{{ $message }}</div>@enderror
</div>

<div class="mb-4">
    <label class="block text-gray-200 mb-2">File {{ isset($document) ? '(leave blank to keep current)' : '' }}</label>
    <input type="file" name="file" class="block w-full text-sm text-gray-100">
    @error('file')<div class="text-red-400">{{ $message }}</div>@enderror
</div>

<div class="mb-4">
    <label class="inline-flex items-center text-gray-200">
        <input type="checkbox" name="visible_to_all" value="1" class="mr-2" {{ old('visible_to_all', $document->visible_to_all ?? true) ? 'checked' : '' }}>
        <span class="ms-2">Visible to all users</span>
    </label>
    @error('visible_to_all')<div class="text-red-400">{{ $message }}</div>@enderror
</div>

<div class="mb-4" id="assigned-users-wrapper">
    <label class="block text-gray-200 mb-2">Assign to specific users (required if not visible to all)</label>
    <select name="assigned_users[]" multiple class="block w-full bg-gray-800 text-gray-100 p-2 rounded">
        @foreach($users as $u)
            <option value="{{ $u->id }}" {{ in_array($u->id, $assigned ?? []) ? 'selected' : '' }}>{{ $u->name }} ({{ $u->email }})</option>
        @endforeach
    </select>
    @error('assigned_users')<div class="text-red-400">{{ $message }}</div>@enderror
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

<div class="mb-4">
    <button type="submit" class="inline-flex items-center px-3 py-1 bg-gray-800 text-white rounded-md">Save</button>
</div>