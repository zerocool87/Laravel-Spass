@csrf

<div class="mb-4">
    <label class="block">Name</label>
    <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" class="border p-2 w-full" required>
    @error('name')<div class="text-red-600">{{ $message }}</div>@enderror
</div>

<div class="mb-4">
    <label class="block">Email</label>
    <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" class="border p-2 w-full" required>
    @error('email')<div class="text-red-600">{{ $message }}</div>@enderror
</div>

<div class="mb-4">
    <label class="block">Password {{ isset($user) ? '(leave blank to keep current)' : '' }}</label>
    <input type="password" name="password" class="border p-2 w-full" {{ isset($user) ? '' : 'required' }}>
    @error('password')<div class="text-red-600">{{ $message }}</div>@enderror
</div>

<div class="mb-4">
    <label class="block">Confirm Password {{ isset($user) ? '(leave blank to keep current)' : '' }}</label>
    <input type="password" name="password_confirmation" class="border p-2 w-full" {{ isset($user) ? '' : 'required' }}>
</div>

<div class="mb-4">
    <label class="inline-flex items-center">
        <input type="checkbox" name="is_admin" value="1" {{ old('is_admin', $user->is_admin ?? false) ? 'checked' : '' }}>
        <span class="ms-2">Administrator</span>
    </label>
</div>

<div class="mb-4">
    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Save</button>
</div>
