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
    <label class="block text-gray-200 mb-2">File {{ isset($document) ? '(leave blank to keep current)' : '' }}</label>
    <input type="file" name="file" class="block w-full text-sm text-gray-100">
    @error('file')<div class="text-red-400">{{ $message }}</div>@enderror
</div>

<div class="mb-4">
    <label class="inline-flex items-center text-gray-200">
        <input type="checkbox" name="visible_to_all" value="1" class="mr-2" {{ old('visible_to_all', $document->visible_to_all ?? true) ? 'checked' : '' }}>
        <span class="ms-2">Visible to all users</span>
    </label>
</div>

<div class="mb-4">
    <label class="block text-gray-200 mb-2">Assign to specific users (optional)</label>
    <select name="assigned_users[]" multiple class="block w-full bg-gray-800 text-gray-100 p-2 rounded">
        @foreach($users as $u)
            <option value="{{ $u->id }}" {{ in_array($u->id, $assigned ?? []) ? 'selected' : '' }}>{{ $u->name }} ({{ $u->email }})</option>
        @endforeach
    </select>
</div>

<div class="mb-4">
    <button type="submit" class="neon-btn">Save</button>
</div>