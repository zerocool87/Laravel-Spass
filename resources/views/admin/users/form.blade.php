@csrf

<div class="mb-4">
    <label class="block text-gray-200 mb-2">Name</label>
    <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" class="border border-gray-600 bg-gray-700 text-gray-100 p-2 w-full rounded" required>
    @error('name')<div class="text-red-400">{{ $message }}</div>@enderror
</div>

<div class="mb-4">
    <label class="block text-gray-200 mb-2">Email</label>
    <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" class="border border-gray-600 bg-gray-700 text-gray-100 p-2 w-full rounded" required>
    @error('email')<div class="text-red-400">{{ $message }}</div>@enderror
</div>

<div class="mb-4">
    <label class="block text-gray-200 mb-2">Password {{ isset($user) ? '(leave blank to keep current)' : '' }}</label>
    <input type="password" name="password" class="border border-gray-600 bg-gray-700 text-gray-100 p-2 w-full rounded" {{ isset($user) ? '' : 'required' }}>
    @error('password')<div class="text-red-400">{{ $message }}</div>@enderror
</div>

<div class="mb-4">
    <label class="block text-gray-200 mb-2">Confirm Password {{ isset($user) ? '(leave blank to keep current)' : '' }}</label>
    <input type="password" name="password_confirmation" class="border border-gray-600 bg-gray-700 text-gray-100 p-2 w-full rounded" {{ isset($user) ? '' : 'required' }}>
</div>

<div class="mb-4">
    <label class="inline-flex items-center text-gray-200">
        <input type="checkbox" name="is_admin" value="1" class="mr-2" {{ old('is_admin', $user->is_admin ?? false) ? 'checked' : '' }}>
        <span class="ms-2">Administrator</span>
    </label>
</div>

<div class="mb-4">
    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded">Save</button>
</div>
