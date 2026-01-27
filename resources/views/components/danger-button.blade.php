@if($attributes->has('href'))
    <a {{ $attributes->except('type')->merge(['class' => 'btn-danger inline-flex items-center px-4 py-2 rounded-lg font-medium text-sm']) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => $attributes->get('type', 'submit'), 'class' => 'btn-danger inline-flex items-center px-4 py-2 rounded-lg font-medium text-sm']) }}>
        {{ $slot }}
    </button>
@endif
