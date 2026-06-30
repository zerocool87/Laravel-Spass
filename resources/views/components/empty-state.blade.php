@props(['icon', 'message'])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center text-gray-400 py-10']) }}>
    {{ $icon }}
    <p class="text-sm font-medium">{{ $message }}</p>
</div>
