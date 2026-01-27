@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'input-text border rounded-lg shadow-sm px-3 py-2 text-sm']) }}>
